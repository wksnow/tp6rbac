<?php


namespace wksnow\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Exception;
use think\facade\Config;
use wksnow\crud\BuildCrud;
use wksnow\crud\BuildFile;
use wksnow\crud\CliEcho;

class Crud extends Command
{

    protected function configure()
    {
        $this->setName('rbac')
            ->addOption("createTable",'t',Option::VALUE_REQUIRED,"创建数据表",0)
            ->addOption("createFile",'s',Option::VALUE_REQUIRED,'创建文件',0)
            ->addOption('basePath', 'b', Option::VALUE_REQUIRED, '控制器基础路径', "app\admin\controller\\v1")
            ->addOption('force', 'f', Option::VALUE_REQUIRED, '强制覆盖模式', 1)
            ->setDescription('一键导入rbac表(admin,node,role)并生成对应的文件');
    }

    protected function execute(Input $input, Output $output)
    {
        $force = $input->getOption("force");
        $createFile = $input->getOption("createFile");
        $basePath = $input->getOption("basePath");
        $createTable = $input->getOption("createTable");
        //createTable为1的时候创建数据表
        if(empty($createFile) && empty($createTable)){
            CliEcho::notice("请选择生成数据表还是文件");
        }
        $content = "";
        if($createTable==1){
            (new BuildCrud())->createTable();
            $content .= "生成数据表成功  ";
        }
        //生成控制器,验证器和路由文件
        if($createFile == 1){
            $buildFile = new BuildFile();
            $buildFile->setControllerData($basePath,$force);
            $buildFile->setValidateData($force);
            $buildFile->setRoute($force);
            $buildFile->setException($force);
            $content .= "  生成文件成功";
        }
        CliEcho::success($content);

    }




    


}