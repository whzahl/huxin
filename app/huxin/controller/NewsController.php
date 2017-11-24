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

    public function xxzx(){

        return $this->fetch();
    }


    public function newsshow()
    {   
        header("Content-Type: text/html; charset=utf-8");
        $id=input('get.id');
        $news=Db::name('hx_news')->where(array('id'=>$id))->find();
        $this->assign('data',$news);
        return $this->fetch();
    }


    public function newslist()
    {   
    	header("Content-Type: text/html; charset=utf-8");
    	$data = Db::name('hx_news')->select();
    	$this->assign('data',$data);
    	return $this->fetch();
    }



     public function type1()
    {   
        header("Content-Type: text/html; charset=utf-8");
        $data=Db::name('hx_news')->where(array('type'=>0))->select();
        $this->assign('data',$data);
        return $this->fetch();
    }



     public function type2()
    {   
        header("Content-Type: text/html; charset=utf-8");
        $data=Db::name('hx_news')->where(array('type'=>1))->select();
        $this->assign('data',$data);
        return $this->fetch();
    }



     public function type3()
    {   
        header("Content-Type: text/html; charset=utf-8");
        $data=Db::name('hx_news')->where(array('type'=>2))->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    
}
