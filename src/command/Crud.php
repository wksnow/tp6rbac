<?php


namespace wksnow\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
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
            ->addOption('basePath', 'b', Option::VALUE_REQUIRED, '控制器基础路径', "app\admin\controller\\v1")
            ->addOption('force', 'f', Option::VALUE_REQUIRED, '强制覆盖模式', 0)
            ->setDescription('一键导入rbac表(admin,node,role)并生成对应的文件');
    }

    protected function execute(Input $input, Output $output)
    {
        $force = $input->getOption("force");
        $basePath = $input->getOption("basePath");
        $createTable = $input->getOption("createTable");
        //createTable为1的时候创建数据表
        if($createTable==1){
            (new BuildCrud())->createTable();
            CliEcho::success('自动生成CURD成功');
        }
        //生成
        $buildFile = new BuildFile();
        $buildFile->setControllerData($basePath,$force);
        $buildFile->setValidateData($force);
        
        die;
//        if($force){
//            $output->info("确定覆盖rbac表?。 请输入 'yes' 按回车键继续操作: ");
//            $line = fgets(defined('STDIN') ? STDIN : fopen('php://stdin', 'r'));
//            if (trim($line) != 'yes') {
//                throw new \Exception("取消文件CURD生成操作");
//            }
//            //删除这个表再重新安装
//            $this->dropTable();
//        }else{
//            //判断这个表存不存在?存在就告诉他不能执行
//
//        }
    }




    


}