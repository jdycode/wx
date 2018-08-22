<?php

namespace app\wx\controller;

use EasyWeChat\Foundation\Application;
use think\Db;
use think\Request;

class UserController extends BaseController
{
    /**
     * 显示订单列表
     *
     * @return \think\Response
     */
    public function order()
    {
        //判断用户有没有绑定
        //得到当前用户openid
        $wx = session('wechat_user');
        $openId = $wx['id'];
       //通过openid 查数据
        $users = Db::name('members')->where('openid',$openId)->find();
       //判断有没有绑定  没有绑定则跳转到绑定页面 如果绑定则取出数据
        if ($users===null) {
            return $this->error('你还没有绑定','bin');
        }
        $orders = Db::name('orders')->where('user_id',$users['id'])->select();
        //取出数据
       return view('order',compact('orders'));
}

    /**
     * 显示用户信息
     */
    public function info()
    {
        //得到openid
        $wx = session('wechat_user');
        $openId = $wx['id'];
       //通过openid 查数据
        $user = Db::name('members')->where('openid',$openId)->find();
//        dump($user);exit;
        //判断用户有没有绑定
        if ($user===null) {
        //页面跳转
            return $this->error('你还没有绑定','bin');
        }
        //用户绑定了取出用户信息
        //::模板消息
        $app = new Application(config('wx'));
        $notice = $app->notice;
        //halt($notice);
        $userId = $openId;
        $templateId = '0hD6aI-fOy5_SqaBD0Bu849VdkO1T_YyFsdtbTb_U_o';
        $url = 'http://wxx.jdyboss.com/wx/user/info';
        $data = array(
            "first"  => "个人信息",
            "name"   =>$user['username'] ,
            "tel"   =>$user['tel'] ,
            "money"   =>$user['money'] ,
            "jifen"   =>$user['jifen'] ,
            "remark" => "感谢你的关注！",
        );
        $data = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
        $members = Db::name('members')->where('openid',$openId)->select();
        //取出数据
        return view('info',compact('members'));
    }

//绑定
    public function bin(Request $request)
    {
        //得到openid
        $wx = session('wechat_user');
        $openId = $wx['id'];
        if (request()->post()) {
            //找到当前用户
            $data = \request()->post();
            $user = Db::name('members')->where('username', $data['username'])->find();
            //判断用户是否存在
            if ($data && password_verify($data['password'], $user['password'])) {               //保存openid
                $result = Db::name('members')->where('id', $user['id'])->update(['openid' => $openId]);
     //halt($result);
                if ($result) {
                    //模板消息
                    $app = new Application(config('wx'));
                    $notice = $app->notice;
                   //halt($notice);
                   $userId = $openId;
                   $templateId = 'VJmuYDbc8t8Q03TN25J5PfVp5Tg0_UwhQm3zm_kYQtc';
                  $url = 'http://wxx.jdyboss.com/wx/user/info';
                    $data = array(
                        "first"  => "恭喜你绑定成功！",
                        "name"   =>$user['username'] ,
                        "remark" => "感谢你的关注！",
                    );
                    $data = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
//                    var_dump($data);exit;
                    return $this->success('绑定成功','info');
                }
            }
            return $this->error('你已经绑定','info');
        }
        //判断当前用户有没有绑定
        $user = Db::name('members')->where('openid', $openId)->find();
        //显示视图
        return view('bin', compact('user'));
    }

    /**
     *解除绑定
     */
    public function unbin()
    {
//得到当前用户openid
        $wx = session('wechat_user');
        $openId = $wx['id'];
        //找出当前用户有没有绑定
        $user = Db::name('members')->where('openid',$openId)->find();
        //判断有没有绑定
        if($user===null){
            //没有绑定跳转绑定页面
            return $this->error('你还为绑定账号','bin');
        }
        //解除绑定
        if (Db::name('members')->where('openid',$openId)->update(['openid'=>null])) {
            return $this->success('解绑成功','bin');
        }
return $this->error('你还没有绑定','unbin');
    }
}
