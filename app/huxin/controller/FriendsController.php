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
use think\request;

class FriendsController extends CheckController

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

    public function getFirstChar($s)
    {
        $s0 = mb_substr($s, 0, 3); //获取名字的姓
        $s = iconv('UTF-8', 'GB2312//IGNORE', $s0); //将UTF-8转换成GB2312编码
        //   dump($s0);
        if (ord($s0) > 128) { //汉字开头，汉字没有以U、V开头的
            $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
            if ($asc >= -20319 and $asc <= -20284) return "A";
            if ($asc >= -20283 and $asc <= -19776) return "B";
            if ($asc >= -19775 and $asc <= -19219) return "C";
            if ($asc >= -19218 and $asc <= -18711) return "D";
            if ($asc >= -18710 and $asc <= -18527) return "E";
            if ($asc >= -18526 and $asc <= -18240) return "F";
            if ($asc >= -18239 and $asc <= -17760) return "G";
            if ($asc >= -17759 and $asc <= -17248) return "H";
            if ($asc >= -17247 and $asc <= -17418) return "I";
            if ($asc >= -17417 and $asc <= -16475) return "J";
            if ($asc >= -16474 and $asc <= -16213) return "K";
            if ($asc >= -16212 and $asc <= -15641) return "L";
            if ($asc >= -15640 and $asc <= -15166) return "M";
            if ($asc >= -15165 and $asc <= -14923) return "N";
            if ($asc >= -14922 and $asc <= -14915) return "O";
            if ($asc >= -14914 and $asc <= -14631) return "P";
            if ($asc >= -14630 and $asc <= -14150) return "Q";
            if ($asc >= -14149 and $asc <= -14091) return "R";
            if ($asc >= -14090 and $asc <= -13319) return "S";
            if ($asc >= -13318 and $asc <= -12839) return "T";
            if ($asc >= -12838 and $asc <= -12557) return "W";
            if ($asc >= -12556 and $asc <= -11848) return "X";
            if ($asc >= -11847 and $asc <= -11056) return "Y";
            if ($asc >= -11055 and $asc <= -10247) return "Z";
        } else if (ord($s) >= 48 and ord($s) <= 57) { //数字开头
            switch (iconv_substr($s, 0, 1, 'utf-8')) {
                case 1:
                    return "Y";
                case 2:
                    return "E";
                case 3:
                    return "S";
                case 4:
                    return "S";
                case 5:
                    return "W";
                case 6:
                    return "L";
                case 7:
                    return "Q";
                case 8:
                    return "B";
                case 9:
                    return "J";
                case 0:
                    return "#";
            }
        } else if (ord($s) >= 65 and ord($s) <= 90) { //大写英文开头
            return substr($s, 0, 1);
        } else if (ord($s) >= 97 and ord($s) <= 122) { //小写英文开头
            return strtoupper(substr($s, 0, 1));
        } else {
            return iconv_substr($s0, 0, 1, 'utf-8');

        }
    }

    public function hy()
    {
        header("Content-Type: text/html; charset=utf-8");

        $id = session('userid');
        $arrData = Db::name('hx_friends')->field('fid')->where(array('uid' => $id, 'status' => 1))->select();
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

    public function get_ajax_friend()
    {
        $name = input('post.keywords');
        $map = array('like', '%' . $name . '%');
        $f_name = Db::name('hx_friends')->where('fname', 'like', $map)->order('id desc')->field('fname')->select();

    }

    public function hy2()
    {

        return $this->fetch();
    }

    public function tjhy()
    {
        header("Content-Type: text/html; charset=utf-8");
        $id = session('userid');
        $uidcard =  Db::name('hx_user')->where(['id' => $id])->field('idcard')->find();
        if($uidcard['idcard'] == ''){
            $this->error('您还未实名认证，请实名认证！', url('user/auidcard'));
        }
        $phone = input('post.phone');
        session('phone', $phone);
        $myfriend = array();
        $friends = Db::name('hx_user')->where('phone', $phone)->find();
        if($friends['idcard'] == ''){
            $this->error('该用户还未实名认证！', url('friends/hy'));
        }else{
            $friendname = $friends['name'];
            $friendid = $friends['id'];
            $mid = Db::name('hx_friends')->where(array('uid' => $id))->column('fid');
            $count = count($mid);
            for ($x = 0; $x < $count; $x++) {
                $myfriend[] = $mid[$x];
            }
            //print_r($myfriend);die;
            if (in_array($friendid, $myfriend)) {
                $this->assign('data', $friends);
            }
            else {
                $this->success('查询成功', url("friends/mynewfriend", array('phone' => $phone)));
            }
        }
        return $this->fetch();
    }

    public function mynewfriend()
    {
        $phone = Request::instance()->param('phone');
        $friends = Db::name('hx_user')->where('phone', $phone)->find();
        $this->assign('data', $friends);
        return $this->fetch();
    }

    public function addfriends()
    {
        $id = session('userid');
        header("Content-Type: text/html; charset=utf-8");
        $phone = input('get.phone');
        //dump($phone);die;
        $name = Db::name('hx_user')->where(array('id' => $id))->find();
        $me = $name['name'];
        $newfriend = Db::name('hx_user')->where('phone', $phone)->find();
        $newfriendname = $newfriend['name'];
        $newfriendid = $newfriend['id'];
        $now = date("Y-m-d");
        $user = array(
            'uid'       => $id,
            'fid'       => $newfriendid,
            'fname'     => $newfriendname,
            'uname'     => $me,
            'status'    => 2,
            'create_time' => $this->request->time(),
        );
        $res = Db::name('hx_friends')->where(array('uid' => $id))->insert($user);
        if($res){
            //添加消息
            $infos = array(
                'uid'       => $id,
                'fid'       => $newfriendid,
                'content'   => '添加好友',
                'type'      => 1,
                'create_time'=> $this->request->time(),
            );
            Db::name('hx_infos')->insert($infos);
            //添加消息结束
            $this->success('好友请求已发送，等待好友审核！',url('friends/hy'));
        }else{
            $this->error('添加失败！');
        }

    }


    public function unfriend()
    {
        $id = session('userid');
        $unread = Db::name('hx_friends')->where(array('fid' => $id, 'status' => 2))->order('id desc')->select();

        $this->assign('data', $unread);
        return $this->fetch();
    }

    public function agreefriend()
    {
        $userid = session('userid');
        $id = input('get.id');
        $name = Db::name('hx_user')->where(array('id' => $id))->find();
        $fname = $name['name'];
        $me = Db::name('hx_user')->where(array('id' => $userid))->find();
        $uname = $me['name'];
        $t = date("Y-m-d");
        $agree = Db::name('hx_friends')->where(array('fid' =>  $userid, 'uid' => $id))->update(['status' => 1, 'create_time' => $t]);
        if($agree){
            $user = ['uid' =>  $userid, 'uname' => $uname, 'fid' => $id, 'fname' => $fname, 'status' => 1, 'create_time' => $t];
            Db::name('hx_friends')->where(array('uid' =>  $id))->insert($user);

            $this->success('已同意好友请求！',url('friends/hy'));
        }

    }


    public function deletefriend()
    {
        $userid = session('userid');
        $id = input('get.id');
        $name = Db::name('hx_user')->where(array('id' => $id))->find();
        $fname = $name['name'];
        $me = Db::name('hx_user')->where(array('id' => $userid))->find();
        $uname = $me['name'];
        $t = date("Y-m-d");
        $agree = Db::name('hx_friends')->where(array('fid' =>  $userid, 'uid' => $id))->update(['status' => 0, 'create_time' => $t]);
        if($agree){
            $this->error('已拒绝好友请求！',url('friends/hy'));
        }
    }



    public function request()
    {
        return $this->fetch();
    }

    public function xy()
    {
        $id = session('userid');
        $data = Db::name('hx_order')->where(['uid' => $id])->select();

        $arrName = array();
        foreach ($data as $key => $value) {
            $name = Db::name('hx_user')->where(['id' => $value['uid']])->find();
            $value['name'] = $name['name'];
            $arrName[] = $value;
        }
        $this->assign('arrName', $arrName);
        return $this->fetch();
    }

    public function xy2()
    {
        return $this->fetch();
    }

    public function xycx()
    {

        if ($this->request->isPost()) {
            $data = $this->request->param();

            $arr = Db::name('hx_user')->where(array('name' => $data['name'], 'idcard' => $data['idcard']))->find();
            if ($arr) {
                $this->success("查询成功", url('friends/xy'));
            } else {
                $this->error("查询为空，请注意输入姓名与身份证号码是否匹配！");
            }

        }

        return $this->fetch();
    }
}
