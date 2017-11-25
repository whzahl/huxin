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
        $s = iconv('UTF-8','GB2312', $s0); //将UTF-8转换成GB2312编码
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
    public function bjt(){
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::name('hx_user')->where(['id' => $id])->find();
        $this->assign('data',$data);
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
        $arrData = Db::name('hx_friends')->field('fid')->where(array('uid'=>4))->select();
        $friends = array();
        foreach ($arrData as $key=>$value){
            $fname = Db::name('hx_user')->field('name')->where(array('id'=>$value['fid']))->find();

            $value['fname'] = $fname['name'];
            $value['char'] = $this->getFirstChar($value['fname']);
            $friends[] = $value;
        }
        $friends = $this->arraySequence($friends, 'char', $sort = 'SORT_ASC');
        $this->assign('list',$friends);
        $this->assign('letter',range('A','Z'));
        return $this->fetch();
    }


/**
     * 借条额度
     * weilang
     * 20171124
     */
    public function jted(){
        $fid = $this->request->param("id", 0, 'intval');
        $this->assign('fid',$fid);
        $uid = session('userid');  
        if($this->request->isPost()){
            $data = $this->request->param();
            if($data){
                $res = array(
                    'uid'       => $uid,
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
        $id = $this->request->param("id", 0, 'intval');
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
        $rate = $p * $o * $d;
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
     * 收还款
     * weilang
     * 20171124
     */
    public function shk()
    {
        return $this->fetch();
    }

/**
     * 借条中心
     * weilang
     * 20171124
     */
    public function jtxx(){
        $id = session('userid');
        //查询fid==id
        $con = 
        $data = Db::name('hx_order')->whereOr(['fid'=>$id])->whereOr(['uid'=>$id])->order('id desc')->select();

        //将user表的nema值插入数组
        foreach ($data as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }

        $this->assign('data',$arrName);
        return $this->fetch();
    }


    /**
     * 销账
     * weilang
     * 20171124
     */
    public function xz1(){
        $info = $this->request->param();
        if($info['status'] == 1){
            $this->success('请输入交易密码！', url('order/dpassword',['id' => $info['id']]));
        }else{
            Db::name('hx_order')->where(array('id'=>$info['id']))->update(['status' => $info['status']]);
            $this->success('修改成功！', url('order/jtxx'));
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
            $dpw = Db::name('hx_user')->where(array('id' => session('userid')))->field('deal_password')->find();
            if($data['password'] == $dpw['deal_password']){
                Db::name('hx_order')->where(array('id'=>$data['id']))->update(['status' => 1]);
                $this->success('操作成功！',url('order/jtxx'));
            }else{
                $this->success('密码错误，请重新输入！');
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


        return $this->fetch();
    }


    /**
     * 借入列表页
     * weilang
     * 20171124
     */
    public function jrlist(){
        $id = $this->request->param('id');
        $data = Db::name('hx_order')->where(['uid'=>$id])->select();
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
        $data = Db::name('hx_order')->where(['fid'=>$id])->select();
        $arrName = array();
        foreach ($data as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
        $this->assign('arrName',$arrName);
        return $this->fetch();
    }


}
