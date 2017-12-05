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
use think\File;

class AgreementController extends CheckController
{


    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    public function agree(){
        // $news=Db::name('hx_news')->where(array('id'=>2))->find();
        // $this->assign('data',$news);
        $oid = $this->request->param();
        //order里面的 订单号、金额、借款日期、还款日期、利率、借款用途、还款总额
        $order = Db::name('hx_order')->where(['id'=>$oid['id']])->find();
        //两方的姓名、身份证号码
        $uuser = Db::name('hx_user')->where(['id'=>$order['uid']])->find();
        $fuser = Db::name('hx_user')->where(['id'=>$order['fid']])->find();
        //还款总额
        //借还款时间
        $end = $order['end_time'];
        $sta = $order['start_time'];
        //借还款时间转换为天数
        $date = strtotime("$end") - strtotime("$sta");
        //利率$rate
        $p = $order['price'];
        $o = $order['rate']/100;
        $d = $date/86400/365;
        $all = $p+$p*$d*$o;
        $this->assign('all',$all);
        $this->assign('order',$order);
        $this->assign('uuser',$uuser);
        $this->assign('fuser',$fuser);
        return $this->fetch();
    }

    public function xgxy(){
        //出借人id 及信息
        $fid = $this->request->param();
        $fuser = Db::name('hx_user')->where(['id'=>$fid['fid']])->find();
        //借款人id 及信息
        $id = session('userid');
        $uuser = Db::name('hx_user')->where(['id'=>$id])->find();
        $time = $this->request->time();
        $this->assign('time',$time);
        $this->assign('uuser',$uuser);
        $this->assign('fuser',$fuser);
        return $this->fetch();
    }
    


}
