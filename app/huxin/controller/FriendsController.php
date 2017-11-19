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

class FriendsController extends HomeBaseController
{

    public function hy()
    {
        return $this->fetch();
    }


    public function hy2()
    {   
        return $this->fetch();
    }
   

    public function tjhy()
    {   header("Content-Type: text/html; charset=utf-8");
     
    //	$phone=$_POST['phone'];
        $phone= input('post.phone');
    	
    	$friends=Db::name('hx_user')->where('phone',$phone)->find();
    	//dump($friends);die;
    	
    	if($friends){
    		
    		$this->assign('data',$friends);
    		//$this->success('查询成功',url('/huxin/friends/tjhy'));
    	}
    	else{
    		$this->success('添加失败，请重新添加',url('/huxin/friends/hy2'));
    	}
    	
    	
        return $this->fetch();
    }
    public  function addfriends(){
    	//Session::get('id');
    	$phone=input('get.phone');
    	//dump($phone);die;
    	$se=session('userid');
    	$mid=Db::name('hx_friends')->where(array('uid'=>$se))->select();
    	dump($se);die;
    	foreach ($mid as $value){
    		$fid=$value['fid'];
    		$user=Db::name('hx_user')->where('phone',$phone)->find();
    		$id=$user['id'];
    		if($id=$fid){
    			echo "ds";
    		}
    	}
    	
 
    	
    }
    public function agreefriend(){
    
    }
     
    public function deletfriend(){
    
    }
     
    public function request(){
    	return $this->fetch();
    }
    public function xy()
    {
        return $this->fetch();
    }


    public function xy2()
    {
        return $this->fetch();
    }


    public function xycx()
    {
        return $this->fetch();
    }
}
