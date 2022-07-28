<?php


namespace mapleLeaves\model;


use think\Exception;

class Role extends BaseModel
{

    /**
     * 查询
     * @param $roleName
     * @param string $field
     * @return array
     * @throws \Exception
     */
    public function getRoleInfoByName($roleName, $field = "*")
    {
        try {
            $info = $this->where('role_name', $roleName)->field($field)->find();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), -1);
        }
        return dataReturn(0, '查询成功', $info);
    }

    /**
     * 添加角色
     * @param $addData
     * @return array
     * @throws \Exception
     */
    public function insertRole($addData)
    {
        try {
            $roleID = $this->insertGetId($addData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), -1);
        }
        return dataReturn(0, '添加成功', ['role_id' => $roleID]);
    }

    /**
     * 更新信息
     * @param $roleID
     * @param $roleInfo
     * @return array
     * @throws \Exception
     */
    public function updateRoleInfo($roleID, $roleInfo)
    {
        try {
            $this->where('role_id', $roleID)
                ->where('role_id', '<>', 1)
                ->update($roleInfo);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), -1);
        }
        return dataReturn(0, '更新成功', ['role_id' => $roleID]);
    }

    /**
     * 删除角色
     * @param $roleId
     * @return array
     * @throws \Exception
     */
    public function deleteRole($roleId)
    {
        try {
            $adminID = $this
                ->where('role_id', '<>', 1)
                ->where('role_id', '=', $roleId)
                ->delete();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), -1);
        }
        return dataReturn(0, '删除成功');
    }

    /**
     * 查询角色列表
     * @param $where
     * @param $limit
     * @return array
     * @throws \Exception
     */
    public function getRoleList($where = [])
    {
        try {
            $list = $this->where($where)->select();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), -1);
        }
        return dataReturn(0, '请求成功', $list);
    }


}