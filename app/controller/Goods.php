<?php

namespace app\controller;

use think\facade\Db;
use think\facade\Session;

class Goods
{
    public static function checkPower(){
        $msg = [
            'msg' => '权限不足，请不要非法操作！',
            'status' => 0,
        ];
        if (!Session::has('admin')) {
            return json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return 'yes';
    }
    public function delTestSession(){
        Session::delete('admin');
    }
    public function setTestSession()
    {
        Session::set('admin', '');
    }
    public static function getGoods($page)
    {
        $msg = [
            'msg' => '一点也没有了',
            'status' => 0,
        ];
        if (!isset($page) || !is_numeric($page) || $page < 1) {
            $msg = [
                'msg' => '请检查一下所传参数和值是否正确！',
                'status' => 0
            ];
            return json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $show = 10;
        $index = ($page - 1) * 10;

        $result = Db::query("select * from lmall_goods limit $index , $show");
        if (is_null($result)) {
            return json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    public static function addGoods($goods)
    {
        $result = Goods::checkPower();
        if($result != 'yes'){
            return $result;
        }
        $msg = [
            'msg' => '出错了，缺少必须值！',
            'status' => 0
        ];
        if (
            !isset($goods['name']) ||
            !isset($goods['thumbnail']) ||
            !isset($goods['price'])
        ) {
            return json_encode($msg);
        }
        Db::table('lmall_goods')->insert($goods);
        $msg['msg'] = 'ok';
        $msg['status'] = 1;
        return json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    public static function setTestGoods()
    {
        $result = Goods::checkPower();
        if($result != 'yes'){
            return $result;
        }
        $name = ['奶茶', '腾讯视频会员', '特价VIP', '超级实惠SVIP', '特价鲜花饼', '爱奇艺会员', 'QQ代挂-2.0'];
        $thumbnail = [
            'storage/testThumbnails/01.png',
            'storage/testThumbnails/02.png',
            'storage/testThumbnails/03.png',
            'storage/testThumbnails/04.png',
            'storage/testThumbnails/05.png',
            'storage/testThumbnails/06.png',
            'storage/testThumbnails/07.png',
            'storage/testThumbnails/08.png',
            'storage/testThumbnails/09.png',
            'storage/testThumbnails/010.png',
            'storage/testThumbnails/011.png',
            'storage/testThumbnails/012.png',
        ];
        $save = [];
        $failde = [];
        $success = [];
        for ($i = 0; $i < 100; $i++) {
            $msg = [
                'msg' => 'error',
                'status' => 0
            ];
            $save['name'] = $name[array_rand($name)];
            $save['thumbnail'] = $thumbnail[array_rand($thumbnail)];
            $save['price'] = mt_rand(5, 100) / 2.15;
            $save['create_time'] = date('Y-m-t h:i:s');
            $result = Goods::addGoods($save);
            $result = json_decode($result);
            if (!($result->status)) {
                array_push($failde, $save);
            } else {
                array_push($success, $save);
            }
        }
        $result = [
            'failde' => $failde,
            'success' => $success,
        ];
        return json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
