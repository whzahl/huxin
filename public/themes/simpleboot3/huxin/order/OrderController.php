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

class OrderController extends CheckController
{


    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }


/**
     * 还有列表排序
     * weilang
     * 20171124
     */
    public function getFirstChar($s){
        $s0 = mb_substr($s,0,3); //获取名字的姓
        $s = iconv('UTF-8','GB2312//IGNORE', $s0); //将UTF-8转换成GB2312编码
        //   dump($s0);
        if (ord($s0)>128) { //汉字开头，汉字没有以U、V开头的
            $asc=ord($s{0})*256+ord($s{1})-65536;
            if($asc>=-20319 and $asc<=-20284)return "A";
            if($asc>=-20283 and $asc<=-19776)return "B";
            if($asc>=-19775 and $asc<=-19219)return "C";
            if($asc>=-19218 and $asc<=-18711)return "D";
            if($asc>=-18710 and $asc<=-18527)return "E";
            if($asc>=-18526 and $asc<=-18240)return "F";
            if($asc>=-18239 and $asc<=-17760)return "G";
            if($asc>=-17759 and $asc<=-17248)return "H";
            if($asc>=-17247 and $asc<=-17418)return "I";
            if($asc>=-17417 and $asc<=-16475)return "J";
            if($asc>=-16474 and $asc<=-16213)return "K";
            if($asc>=-16212 and $asc<=-15641)return "L";
            if($asc>=-15640 and $asc<=-15166)return "M";
            if($asc>=-15165 and $asc<=-14923)return "N";
            if($asc>=-14922 and $asc<=-14915)return "O";
            if($asc>=-14914 and $asc<=-14631)return "P";
            if($asc>=-14630 and $asc<=-14150)return "Q";
            if($asc>=-14149 and $asc<=-14091)return "R";
            if($asc>=-14090 and $asc<=-13319)return "S";
            if($asc>=-13318 and $asc<=-12839)return "T";
            if($asc>=-12838 and $asc<=-12557)return "W";
            if($asc>=-12556 and $asc<=-11848)return "X";
            if($asc>=-11847 and $asc<=-11056)return "Y";
            if($asc>=-11055 and $asc<=-10247)return "Z";
        }else if(ord($s)>=48 and ord($s)<=57){ //数字开头
            switch(iconv_substr($s,0,1,'utf-8')){
                case 1:return "Y";
                case 2:return "E";
                case 3:return "S";
                case 4:return "S";
                case 5:return "W";
                case 6:return "L";
                case 7:return "Q";
                case 8:return "B";
                case 9:return "J";
                case 0:return "#";
            }
        }else if(ord($s)>=65 and ord($s)<=90){ //大写英文开头
            return substr($s,0,1);
        }else if(ord($s)>=97 and ord($s)<=122){ //小写英文开头
            return strtoupper(substr($s,0,1));
        }
        else
        {
            return iconv_substr($s0,0,1,'utf-8');
            //中英混合的词语，不适合上面的各种情况，因此直接提取首个字符即可
        }
    }


/**
     * 补借条
     * weilang
     * 20171124
     */
    public function checkStatus(){
        $userid = session('userid');
        $userdata = Db::name('hx_user')->where(['id' => $userid])->find();
        $fid = $this->request->param("id");
        $frienddata = Db::name('hx_user')->where(['id' => $fid])->find();
        if(empty($userdata['idcard'])){
            $this->error('您还未实名认证，请实名认证！', url('user/auidcard'));
        }
        if(empty($userdata['deal_password'])){
            $this->error('还未设置交易密码，请设置交易密码并注意保密！', url('user/audeal_password'));
        }
        if(empty($frienddata['idcard'])){
            $this->error('该好友还未实名认证，认证后方可进行操作！');
        }
        if(empty($frienddata['deal_password'])){
            $this->error('该好友还未设置交易密码，设置后后方可进行操作！');
        }
        $this->success('success');
       
    }
    
    
    public function bjt(){
        $userid = session('userid');
        $id = $this->request->param("id");
        //全部数据
        $data = Db::name('hx_user')->where(['id' => $id])->find();
        $this->assign('data',$data);
        //逾期数据
        $overdue = Db::name('hx_order')->where(['fid' => $id])->where(['status' => 4])->count();
        $this->assign('overdue',$overdue);
        //进行中的借款
        $ing1 = Db::name('hx_order')->where(['fid' => $id, 'uid' => $userid])->where(['status' => 0])->count();
        $ing2 = Db::name('hx_order')->where(['fid' => $id, 'uid' => $userid])->where(['status' => 1])->count();
        $ing3 = Db::name('hx_order')->where(['fid' => $id, 'uid' => $userid])->where(['status' => 2])->count();
        $ing4 = Db::name('hx_order')->where(['fid' => $userid, 'uid' => $id])->where(['status' => 0])->count();
        $ing5 = Db::name('hx_order')->where(['fid' => $userid, 'uid' => $id])->where(['status' => 1])->count();
        $ing6 = Db::name('hx_order')->where(['fid' => $userid, 'uid' => $id])->where(['status' => 2])->count();
        $ing = $ing1 + $ing2 + $ing3 + $ing4 + $ing5 + $ing6;
        $this->assign('ing',$ing);
        //我们间的交易
        $ord1 = Db::name('hx_order')->where(['fid' => $id, 'uid' => $userid])->count();
        $ord2 = Db::name('hx_order')->where(['uid' => $id, 'fid' => $userid])->count();
        $ord = $ord1 + $ord2;
        $this->assign('ord',$ord);
        //我们的共同好友
        //好友的好友
        $both1 = Db::name('hx_friends')->where(['uid' => $id])->field('fid')->select(); 
        //我的好友
        if(!isset($numboth)){
            $numboth = '';
        }
        foreach ($both1 as $key => $value1) {
            // echo $value1['fname'].'*';
            // echo '</br>';
            //我们的共同好友
            $numboth = Db::name('hx_friends')->where(['uid' => $userid])->where(['fid' => $value1['fid']])->field('fname')->count(); 
        }
        //好友的好友数量
        $numf = Db::name('hx_friends')->where(['uid' => $id])->count();
        $this->assign('numf',$numf);
        $this->assign('numboth',$numboth);
        return $this->fetch();
    }


    /**
     * 借出
     * weilang
     * 20171124
     */
    public function jc(){
        $id = $this->request->param('id');
        $order = Db::name('hx_order')->where(array('id'=>$id))->find();
        $end = $order['end_time'];
        $sta = $order['start_time'];
        $user = Db::name('hx_user')->where(array('id'=>$order['uid']))->find();
        $date = strtotime("$end") - strtotime("$sta");
        $this->assign('date',$date/86400);
        $this->assign('user',$user);
        $this->assign('order',$order);   
        return $this->fetch();
    }

    /**
     * 借入
     * weilang
     * 20171124
     */
    public function jr(){
        $id = $this->request->param('id');
        $order = Db::name('hx_order')->where(array('id'=>$id))->find();
        $end = $order['end_time'];
        $sta = $order['start_time'];
        $user = Db::name('hx_user')->where(array('id'=>$order['fid']))->find();
        //借款时间$date
        $date = strtotime("$end") - strtotime("$sta");
        //已经借到 order表uid==id && status!=0
        $y = Db::name('hx_order')->where(['id' => $id])->where('status','>',0)->sum('price');
        //待借 order表uid==id && status = 0
        $d = Db::name('hx_order')->where(['id' => $id])->where(['status' => 0])->sum('price');
        //输出
        $this->assign('y',$y);
        $this->assign('d',$d);
        $this->assign('date',$date/86400);
        $this->assign('user',$user);
        $this->assign('order',$order);   
        return $this->fetch();
    }


/**
     * 好友
     * name:
     * time:
     */
    public function jt()
    {
        $id = session('userid');
        //关键词搜索
        $request = input('request.');
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['fname']    = ['like', "%$keyword%"];
        }

        $arrData = Db::name('hx_friends')->where(array('uid' => $id, 'status' => 1))->where($keywordComplex)->select();
        $friends = array();
        foreach ($arrData as $key => $value) {
            $fname = Db::name('hx_user')->field('name')->where(array('id' => $value['fid']))->find();
            $value['fname'] = $fname['name'];
            $value['char'] = $this->getFirstChar($value['fname']);
            $friends[] = $value;
        }
        if(!empty($friends)){
            $friends = $this->arraySequence($friends, 'char', $sort = 'SORT_ASC');
        }
        $this->assign('list', $friends);
        $this->assign('letter', range('A', 'Z'));
        return $this->fetch();
    }


/**
     * 借条额度
     * weilang
     * 20171124
     */
    public function jted(){
        $fid = $this->request->param("id");
        $this->assign('fid',$fid);
        $uid = session('userid');
        $userdata = Db::name('hx_user')->where(['id' => $uid])->find();
        //该好友已借总额
        $totle1 = Db::name('hx_order')->where(['fid' => $fid])->where(['status'=>0])->sum('price');
        $totle2 = Db::name('hx_order')->where(['fid' => $fid])->where(['status'=>1])->sum('price');
        $totle3 = Db::name('hx_order')->where(['fid' => $fid])->where(['status'=>2])->sum('price');
        $totle4 = Db::name('hx_order')->where(['fid' => $fid])->where(['status'=>4])->sum('price');
        $totle = 200000 - ($totle1 + $totle2 +$totle3 + $totle4);
        $this->assign('totle',$totle);
        if(empty($userdata['idcard'])){
            $this->error('还未实名认证，请实名认证！', url('user/auidcard'));
        }
        if(empty($userdata['deal_password'])){
            $this->error('还未设置交易密码，请设置交易密码并注意保密！', url('user/audeal_password'));
        }
        if($this->request->isPost()){
            $data = $this->request->param();
            if($data){
                $res = array(
                    'uid'       => $data['fid'],
                    'fid'       => $uid,
                    'price'     => $data['price'],
                    'start_time'=> $data['start_time'],
                    'end_time'  => $data['end_time'],
                    'rate'      => $data['rate'],
                    'status'    => 5,
                    'create_time'=> $this->request->time(),
                    'orderid'   =>  $this->request->time(),
                );
                if(empty($data['price'])){
                    $this->error('请输入借款金额！');
                }
                if(empty($data['start_time'])){
                    $this->error('请选择借款日期！');
                }
                if(empty($data['end_time'])){
                    $this->error('请选择还款日期！');
                }
                if(empty($data['rate'])){
                    $this->error('请输入年利率');
                }
                if(intval($data['price']) > $totle){
                    $this->error('限制借款金额不能超过可借额度');
                }
                $result = Db::name('hx_order')->insert($res);
                if(!empty($result)){
                //添加消息1126
                //这条消息是给fid的人看的
                    $infos = array(
                        'uid'       => $uid,
                        'fid'       => $data['fid'],
                        'content'   => '发起借款通知',
                        'type'      => 2,
                        'create_time'=> $this->request->time(),
                        'orderid'   =>  $this->request->time(),
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                    $this->success("提交成功！",url('user/grzx'));
                }else{
                    $this->error("新增失败！");
                }
            }
            
        }

        return $this->fetch();
    }


/**
     * 借条额度（出）
     * weilang
     * 20171124
     */
    public function jtedc(){
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


/**
     * 销账
     * weilang
     * 20171124
     */
    public function xz(){
        $id = $this->request->param("id");
        $order = Db::name('hx_order')->where(array('id'=>$id))->find();
        //借还款时间
        $end = $order['end_time'];
        $sta = $order['start_time'];
        //查询 对方/收到钱的人/借款人/的个人信息
        $user = Db::name('hx_user')->where(array('id'=>$order['fid']))->find();
        //借还款时间转换为天数
        $date = strtotime("$end") - strtotime("$sta");
        //利率$rate
        $p = $order['price'];
        $o = $order['rate']/100;
        $d = $date/86400/365;
        //利率计算--本息和=本金*[(1+利率)的n次方],n就是你一共经过了多少个结算期
        //pow(x,y) 函数返回 x 的 y 次方。
        //$rate = $p * [pow((1+$o),$d)]
        $a = 1+$o;
        $b = pow($a,$d);
        $rate = $p * $b - $p;
        //借款状态，还差几天到还款时间$t
        $nowTime = date("Y-m-d");
        $t = strtotime("$end") - strtotime("$nowTime");
            // 判断如果还款时间小于当当时间，则改变状态为逾期
            if($t < 0){
                 //添加消息1126
                    $infos = array(
                        'uid'       => session('userid'),
                        'fid'       => $order['fid'],
                        'content'   => '逾期通知',
                        'type'      => 7,
                        'create_time'=> $this->request->time(),
                        'orderid'   => $order['orderid'],
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                Db::name('hx_order')->where(array('id'=>$id))->update(['status' => 4]);
            } 
        $friend = Db::name('hx_user')->where(array('id'=>$order['fid']))->find();
        $off = Db::name('hx_off')->where(['oid' => $id])->order('id desc')->find();
        $this->assign('off',$off);
        $sid = session('userid');
        $this->assign('t',$t/86400);
        $this->assign('rate',$rate);
        $this->assign('date',$date/86400);
        $this->assign('user',$user);
        //当前订单信息
        $this->assign('order',$order);
        //消息方个人信息
        $this->assign('friend',$friend);
        //当前账号人
        $this->assign('sid',$sid);
        return $this->fetch();
    }


/**
     * 收还款
     * weilang
     * 20171124
     */
    public function shk()
    {
        return $this->fetch();
    }
    
/**
     * 展期至
     * weilang
     * 20171202
     */
    public function doff(){
        $off = $this->request->param(); 
        $order = Db::name('hx_order')->where(['id'=>$off['id']])->find();
        if($off['status'] == 1){ 
            Db::name('hx_off')->where(['oid'=>$off['id'], 'create_time'=>$off['create_time']])->update(['status' => 1]);
            $infos = array(
                'uid'       => session('userid'),
                'fid'       => $order['uid'],
                'content'   => '展期同意通知',
                'type'      => 8,
                'create_time'=> $this->request->time(),
                'orderid'   => $order['orderid'],
            );
            Db::name('hx_infos')->insert($infos);
            $end_time = date('Y-m-d',(int)$off['zq_time']);
            Db::name('hx_order')->where(['id'=>$off['id']])->update(['end_time' => $off['zq_time']]);
        }
        $this->success("操作成功！",url('order/jtxxing'));
        if($off['status'] == 2){
            Db::name('hx_off')->where(['oid'=>$off['id'], 'create_time'=>$off['create_time']])->update(['status' => 2]);
            $infos = array(
                'uid'       => session('userid'),
                'fid'       => $order['uid'],
                'content'   => '展期拒绝通知',
                'type'      => 9,
                'create_time'=> $this->request->time(),
                'orderid'   => $order['orderid'],
            );
            Db::name('hx_infos')->insert($infos);
        } 
        $this->success("操作成功！",url('order/jtxxing'));
    }


/**
     * 全部的借条
     * weilang
     * 20171124
     */
    public function jtxx(){
        $id = session('userid');
        $status = $this->request->param('status');
        //查询fid==id
        $data = Db::name('hx_order')->whereOr(['fid'=>$id])->whereOr(['uid'=>$id])->order('id desc')->select();
        $arrName = array();
        //将user表的nema值插入数组
        foreach ($data as $key => $value) {
            $name1 = Db::name('hx_user')->where(['id' => $value['fid']])->find();
            $value['fname'] = $name1['name'];
            $name2 = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['uname'] = $name2['name'];
            $arrName[] = $value;
        }

        $this->assign('data',$arrName);
        return $this->fetch();
    }
    
    
/**
     * 进行中的借条
     * weilang
     * 20171130
     */
    public function jtxxing(){
        $id = session('userid');
        $status = $this->request->param('status');
        //查询fid==id
        $map = array();
        $map['status'] =  array(['=',0],['=',1],['=',2],'or');
        $data1 = Db::name('hx_order')->where($map)->order('id desc')->select(); 
        $data2 = Db::name('hx_order')->whereOr(['fid'=>$id])->whereOr(['uid'=>$id])->order('id desc')->select();
        $arrName = array();
        foreach ($data2 as $key =>$value2){
            foreach ($data1 as $key =>$value1){
                if($value2['orderid'] == $value1['orderid']){
                    //将user表的nema值插入数组       
                    $name = Db::name('hx_user')->where(['id' => $value1['fid']])->find();
                    $value1['name'] = $name['name'];
                    $arrName[] = $value1;
                    
                }
            }
            
        }
        $this->assign('data',$arrName);
        return $this->fetch();
    }
    
    
/**
     * 我们之间的交易
     * weilang
     * 20171129
     */
    public function ouring(){
        $id = $this->request->param("id");
        $userid = session('userid');
        //分别查询相互发生的交易
        $data = Db::name('hx_order')->where(['uid'=>$userid, 'fid'=>$id])->whereOr(['uid'=>$id, 'fid'=>$userid])->order('id desc')->select();
        $arrName = array();
        foreach ($data as $key => $value) {
            $name1 = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['uname'] = $name1['name'];
            $name2 = Db::name('hx_user')->where(['id' => $value['fid']])->find();
            $value['fname'] = $name2['name'];

            $arrName[] = $value;
        }

        $this->assign('data',$arrName);
        return $this->fetch();
    }
    
    
/**
     * 进行中的借款
     * weilang
     * 20171129
     */
    public function ordering(){
        $id = $this->request->param("id");
        $userid = session('userid');
        //分别查询相互发生的交易
        $data1 = Db::name('hx_order')->where(['uid'=>$id, 'fid'=>$userid])->whereOr(['status'=>0, 'status'=>1, 'status'=>2])->order('id desc')->select();
        $data2 = Db::name('hx_order')->where(['uid'=>$userid, 'fid'=>$id])->whereOr(['status'=>0, 'status'=>1, 'status'=>2])->order('id desc')->select();
        $arrName = array();
        //将user表的nema值插入数组
        foreach ($data1 as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
        foreach ($data2 as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
    
        $this->assign('data',$arrName);
        return $this->fetch();
    }


    /**
     * 同意/销账/拒绝
     * weilang
     * 20171124
     */
    public function xz1(){
        $id = session('userid');
        $info = $this->request->param();
        $fid = Db::name('hx_order')->where(['id' => $info['id']])->find();
        if($info['status'] == 0){
            //添加消息1126
                $infos = array(
                    'uid'       => $id,
                    'fid'       => $fid['fid'],
                    'content'   => '拒绝借款',
                    'type'      => 4,
                    'create_time'=> $this->request->time(),
                    'orderid'   => $fid['orderid'],
                );
                Db::name('hx_infos')->insert($infos);
                //添加消息结束
            Db::name('hx_order')->where(array('id'=>$info['id']))->update(['status' => $info['status']]);
            $this->success('处理成功！', url('order/jtxx'));
        }else{
            $this->error('请输入交易密码！', url('order/dpassword',['id' => $info['id'], 'status' => $info['status']]));
        }
        return $this->fetch();
    }


    /**
     * 交易密码
     * weilang
     * 20171124
     */
    public function dpassword(){
        $info = $this->request->param();
        $this->assign('info',$info);
        if($this->request->isPost()){
            $data = $this->request->param();
            $fid = Db::name('hx_order')->where(['id' => $data['id']])->find();
            $dpw = Db::name('hx_user')->where(array('id' => session('userid')))->field('deal_password')->find();
            if($data['password'] == $dpw['deal_password']){
                //同意
                if($data['status'] == 1){
                    //添加消息1126
                    $infos = array(
                        'uid'       => session('userid'),
                        'fid'       => $fid['fid'],
                        'content'   => '同意借款',
                        'type'      => 3,
                        'create_time'=> $this->request->time(),
                        'orderid'   => $fid['orderid'],
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                }
                //销账
                if($data['status'] == 2){
                    //添加消息1202
                    $infos = array(
                        'uid'       => session('userid'),
                        'fid'       => $fid['uid'],
                        'content'   => '销账',
                        'type'      => 5,
                        'create_time'=> $this->request->time(),
                        'orderid'   => $fid['orderid'],
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                    //销账信息
                    $off = array(
                        'oid'       => $data['id'],
                        'type'      => 1,
                        'zq_time'   => '销账',
                        'status'    => 0,
                        'create_time'=> $this->request->time(),
                    );
                    Db::name('hx_off')->insert($off);
                }
                Db::name('hx_order')->where(array('id'=>$data['id']))->update(['status' => $data['status']]);
                $this->success('操作成功！',url('order/jtxx'));
            }else{
                $this->error('密码错误，请重新输入！');
            }
        }
        return $this->fetch();
    }


    /**
     * 销账
     * weilang
     * 20171202
     */
    public function agreexz(){
        $info = $this->request->param();
        $this->assign('info',$info);
        if($this->request->isPost()){
            $data = $this->request->param();
            $fid = Db::name('hx_order')->where(['id' => $data['id']])->find();
            $dpw = Db::name('hx_user')->where(array('id' => session('userid')))->field('deal_password')->find();
            if($data['password'] == $dpw['deal_password']){
                //
                if($data['status'] == 1){
                    //添加消息1202
                    $infos = array(
                        'uid'       => session('userid'),
                        'fid'       => $fid['fid'],
                        'content'   => '同意销账',
                        'type'      => 9,
                        'create_time'=> $this->request->time(),
                        'orderid'   => $fid['orderid'],
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                    Db::name('hx_order')->where(array('id'=>$data['id']))->update(['status' => 3]);
                }
                //销账
                if($data['status'] == 2){
                    //添加消息1202
                    $infos = array(
                        'uid'       => session('userid'),
                        'fid'       => $fid['fid'],
                        'content'   => '拒绝销账',
                        'type'      => 10,
                        'create_time'=> $this->request->time(),
                        'orderid'   => $fid['orderid'],
                    );
                    Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                }
                 //销账信息
                    $off = array(
                        'oid'       => $data['id'],
                        'type'      => 2,
                        'zq_time'   => '销账',
                        'status'    => 0,
                        'create_time'=> $this->request->time(),
                    );
                    Db::name('hx_off')->insert($off);
//                 Db::name('hx_order')->where(array('id'=>$data['id']))->update(['status' => $data['status']]);
                $this->success('操作成功！',url('order/jtxx'));
            }else{
                $this->error('密码错误，请重新输入！');
            }
        }
        return $this->fetch();
    }

    /**
     * 展期
     * weilang
     * 20171124
     */
    public function zq(){
        $id = $this->request->param('id');
        $fid = Db::name('hx_order')->where(['id' => $id])->find();
        if($this->request->isPost()){
            $endt = $this->request->param();
            if($endt){
                $this->success('请输入交易密码！', url('order/dpassword', ['end_time' => $endt['end_time'], 'id' => $fid['id'], 'status' => 2]));
                //添加消息1126
//                     $infos = array(
//                         'uid'       => session('userid'),
//                         'fid'       => $fid['uid'],
//                         'content'   => '展期通知',
//                         'type'      => 6,
//                         'create_time'=> $this->request->time(),
//                         'orderid'   => $fid['orderid'],
//                     );
//                     Db::name('hx_infos')->insert($infos);
                    //添加消息结束
                // $this->success('请输入交易密码！', url('order/dpassword',['id' => $info['id']]));
                Db::name('hx_order')->where(['id' => $endt['id']])->update(['end_time' => $endt['end_time']]);
                $this->success('展期成功！', url('order/jtxx'));
            }else{
                $this->error('操作失败！', url('order/jtxx'));
            }
        }
        $order = Db::name('hx_order')->where(array('id'=>$id))->find();
        //借还款时间
        $end = $order['end_time'];
        $sta = $order['start_time'];
        $user = Db::name('hx_user')->where(array('id'=>$order['fid']))->find();
        //借还款时间转换为天数
        $date = strtotime("$end") - strtotime("$sta");
        //利率$rate
        $p = $order['price'];
        $o = $order['rate']/100;
        $d = $date/86400/365;
        //利率计算--本息和=本金*[(1+利率)的n次方],n就是你一共经过了多少个结算期
        //pow(x,y) 函数返回 x 的 y 次方。
        //$rate = $p * [pow((1+$o),$d)]
        $a = 1+$o;
        $b = pow($a,$d);
        $rate = $p * $b - $p;
        //借款状态，还差几天到还款时间$t
        $nowTime = date("Y-m-d");
        $t = strtotime("$end") - strtotime("$nowTime");
        $this->assign('t',$t/86400);
        $this->assign('rate',$rate);
        $this->assign('date',$date/86400);
        $this->assign('user',$user);
        $this->assign('order',$order);
        return $this->fetch();
    }


    /**
     * 借入列表页
     * weilang
     * 20171124
     */
    public function jrlist(){
        $id = $this->request->param('id');
        $data = Db::name('hx_order')->where(['fid'=>$id])->order('id desc')->select();
        $arrName = array();
        foreach ($data as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
        $this->assign('arrName',$arrName);
        return $this->fetch();
    }


    /**
     * 借出列表页
     * weilang
     * 20171124
     */
    public function jclist(){
        $id = $this->request->param('id');
        $data = Db::name('hx_order')->where(['uid'=>session('userid')])->order('id desc')->select();
        $arrName = array();
        foreach ($data as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['fid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
        $this->assign('arrName',$arrName);
        return $this->fetch();
    }


}
