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
    //	$data['phone']=$phone;
    //	$data['pwd']=$pwd;
    	$res=Db::name('hx_user')->where(array('phone'=>$phone,'password'=>$pwd))->find();
    	
    	$id=$res['id'];
    	session('userid', $id);
    	$se=session('userid');
    	dump($se);die;
    	//dump($se);die;
    	//dump(Session::set('id',$id));die;
    //$se=	session('user','$id');
    
    	
   
    	 
    	if($res){
    		$this->success('登录成功',url('/huxin/friends/hy2'));
    	}
    }

	/**
	  *  用户注册
	  */
    public function register()
    {
    	if($this->request->isPost()){
    		$data = $this->request->param();

    		$phone = Db::name('hx_user')->field('phone')->select()->toArray();
    		foreach ($phone as $key => $value) {
    			// dump($value['phone']);
    		}

    		if($data['phone'] == $value['phone']){
    			$this->error("手机号已被注册,请重新输入！");
    		}else{

    			if(!isset($cmsCode)){
					$cmsCode = '';
				}
    			$cmsCode =  Db::name('hx_code')->where(array('phone' => $data['phone']))->find();

    			if($data['cmsCode'] == $cmsCode['cmsCode']){
    				$data['create_time'] = time();
		    		$result = Db::name('hx_user')->insert($data);
		    		if(!empty($result)){
						$this->success("注册成功！",url('login/login'));
					}else{
						$this->error("注册失败！");
					}
    			}else{
    				$this->error("验证码错误！");
    			}
    		}
    	}
        return $this->fetch();
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
	 	$text="【天使家教】您的验证码是".$code."，如非本人操作，请忽略本短信";
	 	//将验证码存入数据库
	 	$data['phone'] = $mobile;
	 	$data['code'] = $code;
	 	$data['create_time'] = time();
	    $id = Db::name('hx_code')->insert($data);
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
