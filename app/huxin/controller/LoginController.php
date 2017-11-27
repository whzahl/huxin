<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\huxin\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;
class LoginController extends HomeBaseController
{

    public function login()
    {
        return $this->fetch();
    }


    /**
     *  登录表单的提交
     */
    public function index(){    
        header("Content-Type: text/html; charset=utf-8");

        $phone=input('post.phone');
        $pwd=input('post.password');
        $res=Db::name('hx_user')->where(array('phone'=>$phone,'password'=>$pwd))->find();
        $id=$res['id'];

        session('userid', $id);
        session([
            'expire' => 3600,
            'name'=>'userid'
        ]);
        // $se=session('userid');
        if($res){
            $this->success('登录成功',url('/huxin/user/grzx'));
        }else{
            $this->error('登录失败！');
        }
            
    }
    

    /**
      *  用户注册
      */
    public function register()
    {
        if($this->request->isPost()){
            $data = $this->request->param();

            if(!empty($data['phone'])){
                //查询所有手机号
                $phone = Db::name('hx_user')->field('phone')->select();
                foreach ($phone as $key => $value) {
                    if($data['phone'] == $value['phone']){
                        $this->error("该手机号已被注册！");
                    }
                 }
                //验证手机号是否被注册

                if(empty($data['passwordt'])){
                    $this->error("请输入密码！");
                }elseif($data['password'] != $data['passwordt']){
                    $this->error("密码不一致！");
                }else{
                    $cmsCode =  Db::name('hx_code')->where(array('phone' => $data['phone']))->field('code')->find();

                    if($data['cmsCode'] == $cmsCode['code']){
                        $res = array(
                            'phone'     => $data['phone'],
                            'password'  => $data['password'],
                            'create_time'   => $this->request->time(),
                        );
                        $result = Db::name('hx_user')->insert($res);
                        if(!empty($result)){
                            $this->success("注册成功！",url('login/login'));
                        }else{
                            $this->error("注册失败！");
                        }
                    }else{
                        $this->error("验证码错误！");
                    }
                }
            }else{
                $this->error("请输入手机号！");
            }
        }
        return $this->fetch();
    }



    public function editPassword(){

        return $this->fetch();
    }


    /**
      *  发送验证码--注册
     */
    public  function sendSMSl(){
        $ch = curl_init();
        // 必要参数
        $apikey = "d0a614ab1e413bb0ef972f42d88fe57f"; //修改为您的apikey(https://www.yunpian.com)登录官网后获取

        $mobile =  $_GET['mobile']; //请用手机号代替
        $code = $this->createSMSCode();
        $text="【互信网】您的验证码是".$code."，如非本人操作，请忽略本短信";
        //将验证码存入数据库
        $data['phone'] = $mobile;
        $data['code'] = $code;
        $data['create_time'] = time();
        $arrId = Db::name('hx_code')->where(array('phone'=>$data['phone']))->find();
        $id = Db::name('hx_code')->where(array('id'=>$arrId['id']))->insert($data);
        // 发送短信
        $data=array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile);
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $json_data = curl_exec($ch);
        //解析返回结果（json格式字符串）
        $array = json_decode($json_data,true);
        $array['smsCode'] = $id;
        echo json_encode($array);
    }



    /**
     *  发送验证码
     */
    public  function sendSMS(){
        $ch = curl_init();
        // 必要参数
        $apikey = "d0a614ab1e413bb0ef972f42d88fe57f"; //修改为您的apikey(https://www.yunpian.com)登录官网后获取

        $mobile =  $_GET['mobile']; //请用手机号代替
        $code = $this->createSMSCode();
        $text="【互信网】您的验证码是".$code."，如非本人操作，请忽略本短信";
        //将验证码存入数据库
        $data['phone'] = $mobile;
        $data['code'] = $code;
        $data['create_time'] = time();
        $arrId = Db::name('hx_code')->where(array('phone'=>$data['phone']))->find();
        $id = Db::name('hx_code')->where(array('id'=>$arrId['id']))->update($data);
        // 发送短信
        $data=array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile);
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $json_data = curl_exec($ch);
        //解析返回结果（json格式字符串）
        $array = json_decode($json_data,true);
        $array['smsCode'] = $id;
        echo json_encode($array);
    }


     /**
      *  随机生成4位验证码
      */
     public function createSMSCode($length = 4){
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

}
