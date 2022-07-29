<?php


namespace wksnow\crud;


use Symfony\Component\VarExporter\VarExporter;

class BuildFile
{

    /**
     * 获取基础模板
     * @param string $name 文件名称
     * @return string
     */
    public function getStub($name)
    {
        return dirname(dirname(__DIR__)) . '/src/crud/tpl/'.$name.".stub";
    }

    /**
     * 控制器啊类的赋值和写入文件
     * @param $basePath 基础的命令空间
     * @param $force 1强制覆盖
     * @return false|int
     */
    public function setControllerData($basePath,$force)
    {
        //判断存不存在父类文件
        $basecontrollernamespace = str_replace('\\', '/',$basePath);
        $extendfile = \think\facade\App::getRootPath().$basecontrollernamespace."/Base.php";
        if(!file_exists($extendfile)){
            $basecontent = file_get_contents($this->getStub("controller/base"));
            $basecontent = str_replace('{%baseNamespace%}', $basePath, $basecontent);
            $this->makefile($extendfile,$basecontent);
        }

        $controllerName = ["Admin.php"=>"controller/adminController","Node.php"=>"controller/nodeController","Role.php"=>"controller/roleController"];
        $pathName = \think\facade\App::getRootPath().$basecontrollernamespace.'/admin';
        foreach ($controllerName as $k=>$v){
            $fileName = $pathName.'/'.$k;
            $stubcontent = file_get_contents($this->getStub($v));
            $stubcontent = str_replace('{%controllerNamespace%}', $basePath."\admin", $stubcontent);
            $stubcontent = str_replace('{%baseNamespace%}', $basePath, $stubcontent);
            $this->makefile($fileName,$stubcontent,$force);
        }
    }

    /**
     * 设置验证类的文件
     * @param int $force
     */
    public function setValidateData($force=0)
    {
        //验证类文件的根目录
        $bathValidatePath = \think\facade\App::getRootPath()."/app/admin/validate/admin";
        $validateName = ["AdminValidate.php"=>"validate/adminValidate",
            "NodeValidate.php"=>"validate/nodeValidate",
            "RoleValidate.php"=>"validate/roleValidate"];
        foreach ($validateName as $k=>$v){
            $fileName = $bathValidatePath.'/'.$k;
            $stubcontent = file_get_contents($this->getStub($v));
            $this->makefile($fileName,$stubcontent,$force);
        }

    }


    /**
     * 生成文件并写入
     * @param $pathname //文件夹名称
     * @param $fileName //文件名称
     * @param $content //文件内容
     * @param $force 1强制覆盖
     */
    protected function makefile($fileName,$content,$force=0)
    {
//        if(!file_exists($pathName)){
        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }
            return file_put_contents($fileName, $content);
//        }
    }
    
}