<?php

namespace mapleLeaves\crud;

use think\facade\Db;

class BuildCrud
{
    /**
     * 当前目录
     * @var string
     */
    protected $dir;

    /**
     * 应用目录
     * @var string
     */
    protected $rootDir;

    /**
     * 分隔符
     * @var string
     */
    protected $DS = DIRECTORY_SEPARATOR;

    /**
     * 数据库名
     * @var string
     */
    protected $dbName;

    /**
     *  表前缀
     * @var string
     */
    protected $tablePrefix = '';

    public function __construct()
    {
        $this->tablePrefix = config('database.connections.mysql.prefix');
        $this->dbName = config('database.connections.mysql.database');
        $this->dir = __DIR__;
        $this->rootDir = root_path();
        return $this;
    }

    /**
     * 创建数据表
     * @param string $table
     */
    public function createTable($table = "")
    {
        Db::execute($this->adminTable());
        Db::execute($this->nodeTable());
        Db::execute($this->roleTable());
    }

    public function showTable($tableName="")
    {
        $tableName = $this->tablePrefix.$tableName;
        $isTable = Db::query('SHOW TABLES LIKE '."'".$tableName."'");
        if($isTable){
            return dataReturn(0,'存在表');
        }else{
            return dataReturn(-1,'表不存在');
        }
    }

    /**
     * 管理员表
     */
    protected function adminTable()
    {
        return<<<sql
Drop table {$this->tablePrefix}.`_admin`;
CREATE TABLE {$this->tablePrefix}.`_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `admin_name` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名称',
  `nick_name` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员昵称',
  `admin_avatar` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员头像',
  `admin_pwd` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员密码',
  `role_id` int(11) DEFAULT '0' COMMENT '管理角色 0为超级管理员有且只有1个',
  `last_login_time` datetime DEFAULT NULL COMMENT '最近一次登录时间',
  `last_login_ip` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '最近一次登录的ip',
  `last_login_ua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户最近一次登录ua头信息',
  `status` tinyint(2) DEFAULT '1' COMMENT '用户状态 1:正常  2:禁用',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

INSERT INTO {$this->tablePrefix}.`_admin`(`admin_id`, `admin_name`, `nick_name`, `admin_avatar`, `admin_pwd`, `role_id`, `last_login_time`, `last_login_ip`, `last_login_ua`, `status`, `create_time`, `update_time`) VALUES (1, 'admin', '超级管理员', '', '1b1338326eb93e5b8b2db81c3bd3df67a1bbd30a', 1, '2022-01-04 09:55:18', '114.222.189', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36', 1, NULL, '2021-06-09 17:57:23');
sql;
    }

    /**
     * 节点表
     */
    protected function nodeTable()
    {
        return<<<sql
Drop table {$this->tablePrefix}.`_node`;
CREATE TABLE {$this->tablePrefix}.`_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `flag` varchar(15) DEFAULT NULL COMMENT '节点业务标记',
  `node_name` varchar(55) DEFAULT NULL COMMENT '节点名称',
  `front_path` varchar(155) DEFAULT NULL,
  `node_path` varchar(55) DEFAULT NULL COMMENT '节点路径',
  `node_pid` int(11) DEFAULT NULL COMMENT '所属节点',
  `node_icon` varchar(55) DEFAULT NULL COMMENT '节点图标',
  `is_menu` tinyint(1) DEFAULT '1' COMMENT '是否是菜单项 1 是 0不是',
  `is_scope` tinyint(1) DEFAULT '1' COMMENT '是否设定数据范围：1不 2需要',
  `is_del` tinyint(1) DEFAULT '1' COMMENT '1默认2删除',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间 ',
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节点表';
sql;
    }

    /**
     * 角色表
     */
    protected function roleTable()
    {
        return <<<sql
Drop table {$this->tablePrefix}.`_role`;
CREATE TABLE {$this->tablePrefix}.`_role` (
 `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(55) DEFAULT NULL COMMENT '角色名称',
  `role_node` text COMMENT '角色拥有的菜单节点',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态  1 有效 2无效',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';
sql;
    }


}