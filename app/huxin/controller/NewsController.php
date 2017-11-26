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
        $data = Db::name('hx_infos')->where(['fid' => $id])->order('create_time desc')->select();

        foreach ($data as $key => $value) {
            # code...
        }
            $infos =array();
            foreach ($data as $key => $value) {
                //将user表里的name插入infos
                $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
                $value['name'] = $name['name'];
                //将order表里的信息插入infos
                $info = Db::name('hx_order')->where(['fid' => $id])->where(['create_time' => $value['create_time']])->find();
                $value['price'] = $info['price'];
                    $end = strtotime($info['end_time']);
                    $sta = strtotime($info['start_time']);
                    $date = $end - $sta;
                    $d = $date/86400;
                $value['time'] = $d;
                $value['rate'] = $info['rate'];
                $value['nid'] = $info['id'];
                $infos[] = $value;
            }
        $this->assign('info',$infos);
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
