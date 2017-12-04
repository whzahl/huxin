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

class UserController extends CheckController
{

    function __construct() {
       parent::__construct();
       // print "In SubClass constructor\n";
   }

    public function changphone()
    {
        return $this->fetch();
    }


    public function changpw()
    {
        return $this->fetch();
    }


    public function grzx()
    {
        $id = session('userid');

        $data = Db::name('hx_user')->where(array('id' => $id))->find();
        // $photo = $data['photo'];
        // $name = $data['name'];
        // $this->assign('photo',$photo);
        $data['id'] = $id;
        $this->assign('data',$data);
        //借出金额
        $lend = Db::name('hx_order')->where(array('fid' => $id))->sum('price');
        $this->assign('lend',$lend);
        //借入金额
        $borrow = Db::name('hx_order')->where(array('uid' => $id))->sum('price');
        $this->assign('borrow',$borrow);
        //进行中的
        $ing = Db::name('hx_order')->where(array('fid' => $id))->select();

        return $this->fetch();
    }


    //修改登录密码
    public function editPw(){
        $id = session('userid');
        if($this->request->isPost()){
            $data = $this->request->param();
            if(empty($data['passwordNew2'])){
                $this->error("请输入新密码！");
            }
            $arr = Db::name('hx_user')->where(array('id' => $id))->field('password')->find();
            if($data['passwordOld'] == $arr['password']){
                $pw1 = $data['passwordNew1'];
                $pw2 = $data['passwordNew2'];
                if($pw1 == $pw2){
                    $res = array(
                        'password'  => $pw2,
                    );
                    if (Db::name('hx_user')->where(['id' => $id])->update($res) !== false) {
                        $this->success("修改成功！", url('user/grzx'));
                    } else {
                        $this->error("修改失败！");
                    }
                }else{
                    $this->error("两次密码输入不一致！");
                }                
            }else{
               $this->error("旧密码输入错误！");
            }
        }
        return $this->fetch();
    }


     //修改交易密码
    public function editDpw(){
        $id = session('userid');
        if($this->request->isPost()){
            $data = $this->request->param();
            if(empty($data['passwordNew2'])){
                $this->error("请输入新密码！");
            }
            $arr = Db::name('hx_user')->where(array('id' => $id))->field('deal_password')->find();
            if($data['passwordOld'] == $arr['deal_password']){
                $pw1 = $data['passwordNew1'];
                $pw2 = $data['passwordNew2'];
                if($pw1 == $pw2){
                    $res = array(
                        'deal_password'  => $pw2,
                    );
                    if (Db::name('hx_user')->where(['id' => $id])->update($res) !== false) {
                        $this->success("修改成功！", url('user/grzx'));
                    } else {
                        $this->error("修改失败！");
                    }
                }else{
                    $this->error("两次密码输入不一致，请重新输入！");
                }                
            }else{
               $this->error("旧密码输入错误！");
            }
        }
        return $this->fetch();
    }

    //修改手机号码
    public function editPhone(){
        $id = session('userid');
        if($this->request->isPost()){
            $data = $this->request->param();

            $phone = Db::name('hx_user')->field('phone')->select();

            foreach ($phone as $key => $value) {
                // dump($value['phone']);
            }
            // dump($value);die;
            if($data['phone'] == $value['phone']){
                $this->error("手机号已被注册,请重新输入！");
            }else{
                $cmsCode =  Db::name('hx_code')->where(array('phone' => $data['phone']))->find();
                if($data['cmsCode'] == $cmsCode['code']){
                    $res = array(
                        'phone'  => $data['phone'],
                    );
                    if (Db::name('hx_user')->where(['id' => $id])->update($res) !== false) {
                        $this->success("修改成功！", url('user/grzx'));
                    } else {
                        $this->error("修改失败！");
                    }
                }else{
                    $this->error("验证码错误！");
                }
            }
        }
        return $this->fetch();
    }


    public function editPhoto(){
        $id = session('userid');
        $photo = Db::name('hx_user')->where(array('id'=>$id))->field('photo')->find();
        $this->assign('photo',$photo);

        // 获取表单上传文件 
        $file = request()->file('image');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>3145728,'ext'=>'jpg,png,gif,jpeg'])->move(CMF_ROOT . 'public' . DS . 'uploads');
            if($info){
  
                $arr['photo'] = '/uploads/' . $info->getSaveName();
                
                $photo = Db::name('hx_user')->where(array('id' => $id))->field('photo')->find();
                $res = array(
                        'photo'  => $arr['photo'],
                    );

                if(!isset($photo)){
                    $arrData = Db::name('hx_user')->insert($res);
                    $this->success("新增成功！", url('user/grzx'));
                }else{
                    $arrData = Db::name('hx_user')->where(['id' => $id])->update($res);
                    $this->success("修改成功！", url('user/grzx'));
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

        return $this->fetch();
        
    }



/**
     * 身份证实名认证
     * weilang
     * 20171125
     */
    public function auidcard(){
        $id = session('userid');
        $idc = Db::name('hx_user')->where(['id' => $id])->field('idcard')->find();
        if(empty($idc['idcard'])){
            if($this->request->isPost()){
                $data = $this->request->param();
                if(empty($data['name'])){
                    $this->error('请输入姓名！');
                }elseif(empty($data['idcard'])){
                    $this->error('请输入身份证号码！');
                }elseif(!$this->validation_filter_id_card($data['idcard'])){
                    $this->error('身份证号码格式错误！');
                }
                else{
//                    $this->redirect('user/idcard',['card'=>$data['idcard'], 'name'=>$data['name']]);
//                    $this->success('success',url('user/idcard',['card'=>$data['idcard'], 'name'=>$data['name']]));

                    if ($this->idcard($data['idcard'],$data['name'])){
                        $this->success("认证成功",url('user/grzx'));
                    }
                    else{
                        $this->error("认证失败");
                    }
                }
            }
        }else{
            $this->success('已经认证,无需重复认证！');
        }
        return $this->fetch();
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    function idcard_verify_number($idcard_base){
        if(strlen($idcard_base)!=17){
            return false;
        }
        //加权因子
        $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        //校验码对应值
        $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
        $checksum=0;
        for($i=0;$i<strlen($idcard_base);$i++){
            $checksum += substr($idcard_base,$i,1) * $factor[$i];
        }
        $mod=$checksum % 11;
        $verify_number=$verify_number_list[$mod];
        return $verify_number;
    }
// 将15位身份证升级到18位
    function idcard_15to18($idcard){
        if(strlen($idcard)!=15){
            return false;
        }else{
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
                $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
            }else{
                $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
            }
        }
        $idcard=$idcard.$this->idcard_verify_number($idcard);
        return $idcard;
    }
// 18位身份证校验码有效性检查
    function idcard_checksum18($idcard){
        if(strlen($idcard)!=18){
            return false;
        }
        $idcard_base=substr($idcard,0,17);
        if($this->idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
            return false;
        }else{
            return true;
        }
    }
//  身份证校验入口函数
    function validation_filter_id_card($id_card){
        if(strlen($id_card)==18){
            return $this->idcard_checksum18($id_card);
        }elseif((strlen($id_card)==15)){
            $id_card=$this->idcard_15to18($id_card);
            return $this->idcard_checksum18($id_card);
        }else{
            return false;
        }
    }

/**
     * 实名认证API接口
     * weilang
     * 20171127
     */
    public function idcard($card,$name){
        $host = "http://idcard.market.alicloudapi.com";
        $path = "/lianzhuo/idcard";
        $method = "GET";
        $appcode = "8d7e5748c7144d828b831dc30c4a56ae";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        $querys = "cardno=$card&name=$name";
        $bodys = "";
        $url = $host . $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $querys);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($output, 0, $headerSize);
            $body = substr($output, $headerSize);
            curl_close($curl);
            $obt = (array)json_decode($body);
            //$data里面包含["sex"] ="男" ["address"] = "省-地区-县" ["birthday"] = "年-月-日"
            $data = (array)$obt['data'];
            //$resp里面包括["code"]=0/1 ["desc"]="匹配/不匹配"
            $resp = (array)$obt['resp'];
            //名字转码
            $auname = urldecode($name);
            return $this->checkidcard($resp['code'],$card,$auname,$data['address'],$data['sex']);
//                $this->redirect('user/checkidcard', ['resp'=>$resp['code'], 'idcard'=>$card, 'name'=>$auname, 'address'=>$data['address'], 'sex'=>$data['sex']]);

        }else{
//            $this->error('认证失败,请输入正确的身份证号码！');
             return false;
        }
    }
  
    
/**
     * 实名认证
     * weilang
     * 20171128
     */    
    public function checkidcard($resp,$idcard,$name,$address,$sex){
        $id = session('userid');
        $name = urldecode($name);
        $address = urldecode($address);
        $sex = urldecode($sex);
        $data = array(
            'idcard'    => $idcard, 
            'name'      => $name, 
            'address'   => $address,
            'sex'       => $sex,
        );
        if($resp == 0){
            Db::name('hx_user')->where(['id' => $id])->update($data);
//            $this->success('认证成功', url('user/grzx'));
            return true;
        }else{
            return false;
//            $this->error('认证失败');
        }
    }
    

/**
     * 设置交易密码
     * weilang
     * 20171125
     */
    public function audeal_password(){
        $id = session('userid');
        if($this->request->isPost()){
            $data = $this->request->param();
            if(empty($data)){
                $this->error('交易密码为空，请输入后提交！');
            }elseif($data['passwordNew1'] != $data['passwordNew2']){
                $this->error('两次密码不一致！');
            }else{
                $arr = array(
                    'deal_password'  => $data['passwordNew2'],
                );
                Db::name('hx_user')->where(['id' => $id])->update($arr);
                $this->success('交易密码设置成功！', url('user/grzx'));
            }
        }
        return $this->fetch();
    }
  


    //常见问题
    public function question(){

        return $this->fetch();
    }

    public function set()
    {
        return $this->fetch();
    }


    public function loginout(){
        session('userid',null);
        $this->success('退出成功！',url('login/login'));
    }
    

}
