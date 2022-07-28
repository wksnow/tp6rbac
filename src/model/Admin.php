<?php


namespace mapleLeaves\model;


class Admin extends BaseModel
{
    /**
     * 根据账号查询密码
     * @param $adminName
     * @param string $field
     * @return array
     * @throws \Exception
     */
    public function getAdminInfoByName($adminName,$field="*")
    {
        try{
            $info = $this->where('admin_name',$adminName)->field($field)->find();
        }catch (\Exception $e){
          throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0,'查询成功',$info);
    }

    /**
     * 根据角色获取用户
     * @param $roleId
     * @return array
     * @throws \Exception
     */
    public function getUserByRoleID($roleId)
    {
        try{
            $list = $this->where('role_id', $roleId)->select()->toArray();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0, 'success', $list);
    }

}