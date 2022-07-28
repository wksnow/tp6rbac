<?php

think\console::starting(function (\think\console $console){
    $console->addCommands([
        'mapleLeaves'=>'mapleLeaves\\command\\Crud'
    ]);
});

/**
 * 模型内统一数据返回
 * @param $code
 * @param string $msg
 * @param array $data
 * @return array
 */
if (!function_exists('dataReturn')) {
    function dataReturn($code, $msg = 'success', $data = []) {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    }
}

/**
 * 统一返回json数据
 * @param $code
 * @param string $msg
 * @param array $data
 * @return \think\response\Json
 */
if (!function_exists('jsonReturn')) {
    function jsonReturn($code, $msg = 'success', $data = []) {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }
}

/**
 * 统一分页返回
 * @param $list
 * @return array
 */
if (!function_exists('pageReturn')) {
    function pageReturn($list) {
        if (0 == $list['code']) {
            return ['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()];
        }
        return ['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []];
    }
}

/**
 * 生成树形数据
 * @param $data
 * @param $id
 * @param $parent_iden
 * @return array
 */
if(!function_exists("makeUniversalTree")) {
    function makeUniversalTree($data, $id, $parent_iden)
    {
        $res = [];
        $tree = [];

        // 整理数组
        foreach ($data as $key => $vo) {
            $vo['type'] = 1;
            $vo['children'] = [];
            $res[$vo[$id]] = $vo;
        }
        unset($data);
        // 查询子孙
        foreach ($res as $key => $vo) {
            if ($vo[$parent_iden] != 0) {
                $res[$vo[$parent_iden]]['children'][] = &$res[$key];
            }
        }
        // 去除杂质
        foreach ($res as $key => $vo) {
            if (isset($vo[$parent_iden]) && $vo[$parent_iden] == 0) {
                $tree[] = $vo;
            }
        }
        unset($res);
        return $tree;

    }
}

/**
 * 密码加密算法
 * @param string $value  需要加密的值
 * @return mixed
 */
if (!function_exists('password')) {
    function password($value)
    {
        $value = md5($value) . md5('_encrypt') . sha1($value);
        return sha1($value);
    }
}
