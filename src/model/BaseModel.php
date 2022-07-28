<?php


namespace mapleLeaves\model;

use think\Exception;
use think\Model;

class BaseModel extends Model
{
    /**
     *带分页的列表
     * @param $where
     * @param $limit
     * @param string $order
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    public function getList($where, $limit, $field="*",$order = 'id desc')
    {
        try {
            $list = $this->where($where)->order($order)->field($field)->paginate($limit);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0, '查询成功', $list);
    }

    /**
     * 不带分页的列表
     * @param $where
     * @param string $field
     * @return array
     * @throws \Exception
     */
    public function getListNoLimit($where,$field = '*')
    {
        try {
            $list = $this->where($where)->field($field)->select();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0, '查询成功', $list);
    }

    /**
     * 详情
     * @param $where
     * @param string $field
     * @return array
     * @throws \Exception
     */
    public function getInfo($where,$field="*")
    {
        try{
            $info = $this->where($where)->field($field)->find();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0,'查询成功',$info);
    }

    /**
     * 添加
     * @param $param
     * @return array
     * @throws \Exception
     */
    public function add($param)
    {
        try{
            $id = self::insertGetId($param);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0,'添加成功', $id);
    }

    /**
     * 修改信息
     * @param $where
     * @param $data
     * @throws \Exception
     */
    public function edit($where,$data)
    {
        try{
            $this->where($where)->update($data);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),-1);
        }
        return dataReturn(0,'修改成功');
    }

}