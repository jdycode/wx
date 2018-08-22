<?php

namespace app\wx\controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use think\Controller;
use think\Db;

class ServerController extends Controller
{
    //打通微信服务器
    public function index()
    {
        $app = new Application(config('wx'));
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            //回复 帮助
            if ($message->Content === '帮助') {
                return "地址：重庆互联网学院\n电话：13888888888";
            }
            //回复解除绑定
            if ($message->Content === '解除绑定') {
                //得到openid
                $openId = $message->FromUserName;
                //查数据库当前用户有没有绑定
                $user = Db::name('members')->where('openid', $openId)->find();
                if ($user) {
                    //解除绑定
                    Db::name('members')->where('openid', $openId)->update(['openid' => null]);
                    return '解绑成功';
                }
                return '你还没有绑定账号';
            }
            //点击热卖商品
            if ($message->EventKey==='key_hot' || $message->Content==='热卖商品'){
                //取出5个菜品信息
                $goods = Db::name('menus')->order('id', 'desc')->limit(4)->select();
//               return 11;
                //循环取出商品  并拼装图文消息
                $goodArray = [];
                    foreach ($goods as $good){
                        //创建一个图文
                        $news = new News();
                        $news->title = $good['goods_name'];
                        $news->description = $good['description'];
                        $news->image = $good['goods_img'];
                        $news->url = "http://www.ele.com/goods/detail/".$good['id'];
                        //再压到大数组中
                        $goodArray[]=$news;
                }
                        //显示数据
                  return $goodArray;
            }
        });
        $response = $app->server->serve();

// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
    }

//授权回调
    public function call()
    {
        $app = new Application(config('wx'));
        $oauth = $app->oauth;

// 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        //$_SESSION['wechat_user'] = $user->toArray();
        session('wechat_user', $user->toArray());
        $targetUrl = session('target_url') ?? "/";
        //header('location:'. $targetUrl); // 跳转到 user/profile
        return $this->redirect($targetUrl);
    }

//的到菜单
    public function getMenu()
    {
        $app = new Application(config('wx'));
        //2.操作菜单的对象
        $menu = $app->menu;
        //3.得到所有菜单
        dump($menu->all());
        exit;
    }

//设置菜单
    public function setMenu()
    {
        $buttons = [
            [
                "type" => "click",//click就是button  view 就是a标签
                "name" => "热卖商品",
                "key" => "key_hot"
            ],
            [
                "name" => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url" => "http://wxx.jdyboss.com/wx/user/order"
                    ],
                    [
                        "type" => "view",
                        "name" => "我的信息",
                        "url" => "http://wxx.jdyboss.com/wx/user/info"
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账号",
                        "url" => "http://wxx.jdyboss.com/wx/user/bin"
                    ],
                ],
            ],
        ];
        $app = new Application(config('wx'));

        //2.操作菜单的对象
        $menu = $app->menu;
        $menu->add($buttons);
    }
}
