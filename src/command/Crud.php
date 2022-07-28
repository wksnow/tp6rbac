<?php


namespace wksnow\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;

class Crud extends Command
{

    protected function configure()
    {
        $this->setName('rbac')
            ->addOption('controllerFilename', 'c', Option::VALUE_REQUIRED, '控制器路径', "app\admin\controller\\v1\admin")
            ->addOption('force', 'f', Option::VALUE_REQUIRED, '强制覆盖模式', 0)
            ->setDescription('一键导入rbac表(admin,node,role)');
    }

    protected function execute(Input $input, Output $output)
    {

//        $force = $input->getOption("force");
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




    /**
     * 生成文件并写入
     * @param $pathname //文件名称
     * @param $content //文件内容
     */
    protected function makefile($pathname,$content)
    {
        if(!file_exists($pathname)){
            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            return file_put_contents($pathname, $content);
        }
    }


}