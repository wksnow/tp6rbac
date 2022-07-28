<?php


namespace mapleLeaves\service;


use mapleLeaves\model\Admin;
use mapleLeaves\model\Node;
use mapleLeaves\model\Role;
use xiaodi\JWTAuth\Facade\Jwt;

class AdminService extends BaseService
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    /**
     * 账号列表
     * @throws \think\Exception
     * @throws \Exception
     */
    public function getAdminList($param, $limit)
    {
        $where[] = ['admin_id', '<>', 1];
        if (!empty($param['admin_name'])) {
            $where[] = ['admin_name', 'like', '%' . $param['admin_name'] . '%'];
        }
        if (!empty($param['role_id'])) {
            $where[] = ['role_id', '=', $param['role_id']];
        }
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }
        $adminList = $this->adminModel->getList($where, $limit, 'admin_id,admin_name,nick_name,admin_avatar,role_id,status,create_time', 'admin_id desc');
        if (!empty($adminList['data'])) {
            $adminArr = $adminList['data']->toArray();
            $roleIdArr = array_column($adminArr, 'role_id');
            $roleIdArr = array_values(array_unique($roleIdArr));
            $roleModel = new Role();
            $roleList = $roleModel->getRoleList([['role_id', 'in', $roleIdArr]]);
            $roleArr = array_column($roleList['data'], 'role_name', 'role_id');
            foreach ($adminList['data']->items() as $value) {
                $value->role_name = $roleArr[$value->role_id];
            }
        }
        return pageReturn($adminList);
    }


    /**
     * 添加角色
     */
    public function addAdmin($param)
    {
        $adminInfoRes = $this->adminModel->getAdminInfoByName($param['admin_name']);

        if (!empty($adminInfoRes['data'])) {
            return dataReturn(-1, '该账号已存在');
        }
        $param['admin_pwd'] = password($param['admin_pwd']);
        $param['create_time'] = date('Y-m-d H:i:s');
        $param['update_time'] = date('Y-m-d H:i:s');
        return $this->adminModel->add($param);
    }

    /**
     * 编辑
     * @throws \Exception
     */
    public function editAdmin($param)
    {
        $adminId = $param['admin_id'];
        unset($param['admin_id']);
        if (empty($param)) {
            return dataReturn(-1, '没有需要更新的内容');
        }
        $adminInfoRes = $this->adminModel->getInfo(['admin_id' => $adminId])['data'];

        if (!empty($param['admin_name'])) {
            if ($adminInfoRes['admin_name'] != $param['admin_name']) {
                $roleInfoRes = $this->adminModel->getAdminInfoByName($param['admin_name']);
                if (!empty($roleInfoRes['data'])) {
                    return dataReturn(-1, '该账号已存在');
                }
            }
        }

        if (!empty($param['admin_pwd'])) {
            $param['admin_pwd'] = password($param['admin_pwd']);
        }

        $param['update_time'] = date('Y-m-d H:i:s');
        return $this->adminModel->edit([['admin_id', '=', $adminId]], $param);
    }

    /**
     * 修改密码
     * @throws \Exception
     */
    public function changePassword($adminId, $adminPwd)
    {
        $adminPwd = password($adminPwd);
        $this->adminModel->edit([['admin_id', '=', $adminId]], ['admin_pwd' => $adminPwd]);
        return dataReturn(0, "密码修改成功");
    }

    /**
     * 用户登录
     * @param array $param 登录的账号和密码
     */
    public function adminLogin($param = [])
    {
        try {
            $adminInfo = $this->adminModel->getAdminInfoByName($param['phone'])['data'];
            if (empty($adminInfo)) {
                return dataReturn(-2, '账号不存在');
            }
            if ($adminInfo['admin_pwd'] != password($param['password'])) {
                return dataReturn(-3, '用户名密码不正确');
            }

            if ($adminInfo['status'] != 1) {
                //记录登录信息
                return dataReturn(-5, '该账号已经被禁用');
            }
            $nodeModel = new Node();
            $nodeUrlArr = [];
            if ($adminInfo['role_id'] !== 1) {
                $roleModel = new Role();
                $roleInfo = $roleModel->getInfo(["role_id" => $adminInfo['role_id']])['data'];

                if ($roleInfo['status'] != 1) {
                    return dataReturn(-5, '该账号角色已被禁用');
                }
                $nodeList = $nodeModel->getInfo([['node_id', 'in', $roleInfo['role_node']]])['data'];
                if (empty($nodeList)) {
                    return dataReturn(-7, '权限异常请联系管理员');
                }

                foreach ($nodeList as $key => $value) {
                    if ($value['node_path'] != '*') {
                        $nodePath = strtolower($value['node_path']);
                        $nodeUrlArr[$nodePath] = 1;
                    }
                }
            }
            cache('admin_rule_' . $adminInfo['admin_id'], $nodeUrlArr);
            $token = Jwt::token([
                'uid' => $adminInfo['admin_id'],
                'name' => $adminInfo['admin_name'],
                'role' => $adminInfo['role_id'],
            ]);

            return dataReturn(0, '登录成功', ['token' => (string)$token, 'userInfo' => ['userId' => $adminInfo['admin_id'],
                'role_id' => $adminInfo['role_id'],
                'userName' => $adminInfo['admin_name'],
                'avatar' => $adminInfo['admin_avatar'],
            ]]);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }
    }

}