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


/**
     * 消息中心
     * weilang
     * 20171126
     */
    public function xxzx(){
        $id = session('userid');
        //
        //这条信息是收到消息的看到的$data1 fid
        //
        //查infos表里该看到这条信息的人
        $data1 = Db::name('hx_infos')->where(['fid' => $id])->order('create_time desc')->select();
            $infos1 =array();
            foreach ($data1 as $key => $value) {
                //将user表里的name插入infos
                //user表里发消息的人
                //同意
                $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
                $info1 = Db::name('hx_order')->where(['orderid' => $value['orderid']])->find();
                $value['name'] = $name['name'];
                //将order表里的信息插入infos
                $value['price'] = $info1['price'];
                $end = strtotime($info1['end_time']);
                $sta = strtotime($info1['start_time']);
                $date = ($end - $sta)/86400;
                $value['time'] = $date;
                $value['rate'] = $info1['rate'];
                $value['nid'] = $info1['id'];
                $infos1[] = $value;
            }
        $this->assign('info1',$infos1);
        Db::name('hx_infos')->where(['uid' => $id, 'status' => 0])->update(['status' => 1]);
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
