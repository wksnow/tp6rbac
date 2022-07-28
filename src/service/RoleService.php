<?php

namespace mapleLeaves\service;


use mapleLeaves\model\Admin;
use mapleLeaves\model\Node;
use mapleLeaves\model\Role;

class RoleService extends BaseService
{

    private $roleModel;

    public function __construct()
    {
        $this->roleModel = new Role();
    }

    /**
     * 角色列表
     * @throws \think\Exception
     */
    public function getRoleList($param, $limit)
    {
        $where[] = ['role_id', '>', 1];
        if (!empty($param['role_name'])) {
            $where[] = ['role_name', 'like', '%' . $param['role_name'] . '%'];
        }
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }
        return $this->roleModel->getList($where, $limit);
    }


    /**
     * 角色信息
     * @throws \Exception
     */
    public function getRoleById($roleId)
    {
        $roleInfoRes = $this->roleModel->getInfo(['role_id' => $roleId]);
        if (!empty($roleInfoRes['data'])) {
            $nodeModel = new Node();
            if ($roleInfoRes['data']['role_node'] == '*') {
                $nodeList = $nodeModel->getListNoLimit(['is_del' => 1]);
            } else {
                $nodeList = $nodeModel->getListNoLimit([['node_id', 'in', $roleInfoRes['data']['role_node']], ['is_del', "=", 1]]);
            }
            $roleInfoRes['data']['role'] = makeUniversalTree($nodeList['data']->toarray(), 'node_id', 'node_pid');
        }
        return $roleInfoRes;
    }

    /**
     * 添加角色
     * @param array $param
     * @throws \Exception
     */
    public function addRole($param=[])
    {
        $roleInfoRes = $this->roleModel->getRoleInfoByName($param['role_name']);

        if (!empty($roleInfoRes['data'])) {
            return dataReturn(-1, '该角色已存在');
        }
        $param['status'] = 1;
        $param['create_time'] = date('Y-m-d H:i:s');
        $param['update_time'] = date('Y-m-d H:i:s');

        return $this->roleModel->insertRole($param);

    }

    /**
     * 编辑信息
     * @param $param
     * @throws \Exception
     */
    public function editRole($param)
    {
        $roleId = $param['role_id'];
        unset($param['role_id']);
        if (empty($param)) {
            return dataReturn(-1, '没有需要更新的内容');
        }

        if (!empty($param['role_name'])) {
            $roleInfoRes = $this->roleModel->getRoleInfoByName($param['role_name']);

            if (!empty($roleInfoRes['data']) && $roleId != $roleInfoRes['data']['role_id']) {
                return dataReturn(-1, '该角色已存在');
            }
        }
        $param['update_time'] = date('Y-m-d H:i:s');

        //todo 更新节点成功,所有登录人的缓存
        return $this->roleModel->updateRoleInfo($roleId, $param);

    }

    /**
     * 变更状态
     * @param int $roleId 角色id
     * @param int $status 状态
     * @throws \Exception
     */
    public function changeRoleStatus($roleId=0,$status=2)
    {
        return $this->roleModel->updateRoleInfo($roleId, ['status'=>$status]);
    }

    /**
     * 变更状态
     * @param int $roleId 角色的id
     * @throws \Exception
     */
    public function delRole($roleId=0)
    {
        $adminModel = new Admin();
        $adminRes = $adminModel->getInfo(['role_id'=>$roleId]);
        if (!empty($adminRes['data'])) {
            return dataReturn(-1, '有用户是此角色,不能删除');
        }
        return $this->roleModel->deleteRole($roleId);
    }
}