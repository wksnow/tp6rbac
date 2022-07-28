<?php


namespace wksnow\service;


use wksnow\model\Admin;
use wksnow\model\Node;
use wksnow\model\Role;
use think\Exception;

class NodeService
{
    protected $nodeModel;

    public function __construct()
    {
        $this->nodeModel = new Node();
    }

    /**
     * 权限列表
     * @throws \Exception
     */
    public function getMenuByRole($roleId, $adminId)
    {
        $adminModel = new Admin();
        $adminInfoRes = $adminModel->getInfo(['admin_id' => $adminId])['data'];
        if (empty($adminInfoRes)) throw new \Exception("管理员不存在", -1);

        $admin = [
            'id' => $adminInfoRes['admin_id'],
            'name' => $adminInfoRes['admin_name'],
            'avatar' => $adminInfoRes['admin_avatar'],
            'roleId' => $adminInfoRes['role_id'],
            'menu' => []
        ];

        if ($roleId == 1) {
            $nodeListRes = $this->nodeModel->getListNoLimit(['is_del'=>1])['data'];
        } else {
            $roleModel = new Role();
            $roleInfoRes = $roleModel->getInfo(['role_id'=>$roleId])['data'];
            if (empty($roleInfoRes)) throw new \Exception("角色异常", -1);

            if ($roleInfoRes['role_node'] == '*') {
                $nodeListRes = $this->nodeModel->getListNoLimit(['is_del'=>1])['data'];
            } else {
                $nodeListRes = $this->nodeModel->getListNoLimit([['node_id', 'in', $roleInfoRes['role_node']],['is_del','=',1]])['data'];
            }
        }
        if (!empty($nodeListRes)) {
            foreach ($nodeListRes as $key => $value) {
                if ($value['is_menu'] != 1) {
                    unset($nodeListRes[$key]);
                }
            }
            $menu = makeUniversalTree($nodeListRes, 'node_id', 'node_pid');
        }
        $admin['menu'] = $menu ?? [];
        return dataReturn(0, '成功', $admin);
    }

    /**
     * 用户权限节点
     * @param int $roleId 角色的Id
     * @throws \Exception
     */
    public function getNodeByRole($roleId=0)
    {
        if ($roleId == 1) {
            $nodeListRes = $this->nodeModel->getListNoLimit(['is_del'=>1])['data'];
        } else {
            $roleModel = new Role();
            $roleInfoRes = $roleModel->getInfo(['role_id'=>$roleId])['data'];
            if (empty($roleInfoRes)) throw new \Exception("角色不存在",-1);
            $nodeListRes = $this->nodeModel->getListNoLimit([['node_id', 'in', $roleInfoRes['role_node']],['is_del','=',1]])['data'];
        }
        return $nodeListRes;
    }

    /**
     * 获取子节点
     * @param int $nodePid
     * @throws \Exception
     */
    public function getNodeByPid($nodePid=0)
    {
        return $this->nodeModel->getListNoLimit([['node_pid', '=', $nodePid]]);
    }

    /**
     * 添加节点
     * @throws \Exception
     */
    public function addNode($param)
    {
        $nodeListRes = $this->nodeModel->getInfo([['node_name', '=', $param['node_name']], ['is_del', '=', 1]])['data'];
        if (!empty($nodeListRes)) {
            return dataReturn(-1, '同名节点已存在');
        }

        if ($param['is_menu'] != 1) {
            $param['node_path'] = trim($param['node_path']);
            $param['node_path'] = strtolower(trim($param['node_path'], '/'));
            $nodeListRes = $this->nodeModel->getInfo([['node_path', '=', $param['node_path']], ['is_menu', '<>', 1], ['is_del', '=', 1]])['data'];

            if (!empty($nodeListRes)) {
                return dataReturn(-1, '同路径节点已存在');
            }
        }
        $param['is_scope'] = 1;
        $param['is_del'] = 1;
        $param['create_time'] = date('Y-m-d H:i:s');
        $param['update_time'] = date('Y-m-d H:i:s');
        return $this->nodeModel->add($param);
    }

    /**
     * 编辑节点
     * @throws \Exception
     */
    public function editNode($param)
    {
        $nodeListRes = $this->nodeModel->getInfo([['node_name', '=', $param['node_name']], ['is_del', '=', 1], ['node_id', '<>', $param['node_id']]]);

        if (!empty($nodeListRes['data'])) {
            return dataReturn(-1, '已有同名节点存在');
        }

        if ($param['is_menu'] != 1) {
            $param['node_path'] = trim($param['node_path']);
            $param['node_path'] = strtolower(trim($param['node_path'], '/'));
            $nodeListRes = $this->nodeModel->getInfo([['node_path', '=', $param['node_path']], ['is_menu', '<>', 1], ['is_del', '=', 1], ['node_id', '<>', $param['node_id']]]);
            if ($nodeListRes['code'] != 0) {
                return $nodeListRes;
            }
            if (!empty($nodeListRes['data'])) {
                return dataReturn(-1, '同路径节点已存在');
            }
        }
        $param['update_time'] = date('Y-m-d H:i:s');
        return $this->nodeModel->edit([['node_id', '=', $param['node_id']]], $param);
    }


    /**
     * 删除节点
     */
    public function delNode($nodeId)
    {
        $nodeListRes = $this->nodeModel->getInfo([['node_pid', '=', $nodeId], ['is_del', '=', 1]]);
        if ($nodeListRes['code'] != 0) {
            return $nodeListRes;
        }
        if (!empty($nodeListRes['data'])) {
            return dataReturn(-1, '存在子节点不能直接删除');
        }

        return $this->nodeModel->edit([['node_id', '=', $nodeId]], ['is_del' => 2, 'update_time' => date('Y-m-d H:i:s')]);
    }

    /**
     * 获取所有的权限节点
     * @return array
     * @throws \Exception
     */
    public function getAllNode()
    {
        $list = $this->nodeModel->getListNoLimit(['is_del'=>1])['data'];
        $map = [];
        foreach ($list as $key => $vo) {
            $map[$key] = [
                'id' => $vo['node_id'],
                'pid' => $vo['node_pid'],
                'name' => $vo['node_name'],
            ];
        }
        return dataReturn(0, 'success', makeUniversalTree($map, 'id', 'pid'));
    }

}