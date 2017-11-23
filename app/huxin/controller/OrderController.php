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

class OrderController extends HomeBaseController
{

    public function bjt(){
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::name('hx_user')->where(['id' => $id])->find();
        // dump($data);
        $this->assign('data',$data);

        return $this->fetch();
    }


    public function jc()
    {
        return $this->fetch();
    }


    public function jr()
    {
        return $this->fetch();
    }


    public function jt()
    {
        return $this->fetch();
    }


    public function jted(){
        $id = session('userid');
        if($this->request->isPost()){
            $data = $this->request->param();
            if($data){
                $res = array(
                    'uid'       => $id,
                    'fid'       => $data['fid'],
                    'price'     => $data['price'],
                    'start_time'=> $data['start_time'],
                    'end_time'  => $data['end_time'],
                    'rate'      => $data['rate'],
                    'status'    => 0,
                    'create_time'=> $this->request->time(),
                );
                $result = Db::name('hx_order')->insert($res);
                if(!empty($result)){
                    $this->success("新增成功！",url('user/grzx'));
                }else{
                    $this->error("新增失败！");
                }
            }
            
        }
        return $this->fetch();
    }


    public function xz()
    {
        return $this->fetch();
    }


}
