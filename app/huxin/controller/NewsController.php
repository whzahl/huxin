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
class NewsController extends HomeBaseController
{

    public function xxzx()
    {
        return $this->fetch();
    }


    public function newsshow()
    {   header("Content-Type: text/html; charset=utf-8");

        $id = $this->request->param("id", 0, 'intval');
    	$data = Db::name('hx_news')->where(["id" => $id])->find();
    	$this->assign('data',$data);
        return $this->fetch();
    }
    public function newslist()
    {   
    	header("Content-Type: text/html; charset=utf-8");
    	$data = Db::name('hx_news')->select();
    	//duma($list['title']);die;
    	//$data=['title'=>$list['title'],'content'=>$list['content']];
        // dump($data);
    	$this->assign('data',$data);
    	return $this->fetch();
    }
    
}
