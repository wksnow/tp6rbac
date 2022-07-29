<?php


namespace wksnow\service;



use think\exception\ValidateException;
use wksnow\SelfException;

class BaseService
{

    /**
     * 自定义验证类
     * @param $data
     * @param $validate
     * @param $scene
     * @throws \Exception
     */
    protected function validateparam($data,$validate,$scene=null)
    {
        try {
            if(empty($scene)){
                \validate($validate)->check($data);
            }else{
                \validate($validate)->scene($scene)->check($data);
            }
        }catch (ValidateException $e){
          throw new SelfException($e->getMessage(),-1);
        }
    }

}