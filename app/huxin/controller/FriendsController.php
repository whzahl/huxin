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

class FriendsController extends HomeBaseController
{
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
	public function hy()
	{header("Content-Type: text/html; charset=utf-8");
	
		$userName = Db::name('hx_friends')->where(array('uid'=>4))->column('fname');
	
	//	$userName = array('张三','马大帅','李四','王五','小二','猫蛋','狗蛋','王花','三毛','小明','李刚','张飞');
		sort($userName);
		$nameArray = array();
		$charArray  = array();
		foreach($userName as $k=>$name){
			$char = $this->getFirstChar($name);
			
			//$count=count($charArray);
			
			/*if(count($charArray)!=0){
				$nameArray = $charArray[$char];
			}*/
			
		array_push($nameArray,$name);
		
			
		//	array_values($nameArray);
			$charArray[$char] = $nameArray;
			
			
		//	dump($nameArray);die;
			
			
			//$this->assign('vo',$charArray);
		}
		
		//dump($nameArray);die;
		ksort($charArray);
		$this->assign('list',$charArray);
		return $this->fetch();
	}
	
 


    public function hy2()
    {   
        return $this->fetch();
    }
   

    public function tjhy()
    {   header("Content-Type: text/html; charset=utf-8");
     
    //	$phone=$_POST['phone'];
        $phone= input('post.phone');
    	
    	$friends=Db::name('hx_user')->where('phone',$phone)->find();
    	//dump($friends);die;
    	
    	if($friends){
    		
    		$this->assign('data',$friends);
    		//$this->success('查询成功',url('/huxin/friends/tjhy'));
    	}
    	else{
    		$this->success('添加失败，请重新添加',url('/huxin/friends/hy2'));
    	}
    	
    	
        return $this->fetch();
    }
    public  function addfriends(){
    	//Session::get('id');
    	$se=session('userid');
    	
    	$phone=input('get.phone');
    	//dump($phone);die;
    	//$se=session('userid');
    	
    	$mid=Db::name('hx_friends')->where(array('uid'=>4))->column('fid');
    	$count=count($mid);
    	//dump($mid);die;
    	$id=Db::name('hx_user')->where('phone',$phone)->column('id');
    	//dump($id);
    	for($x=0;$x<$count;$x++){
    		if($id!=$mid[$x]){
    			echo "对方已经是您的好友！";
    			
    			
    		}
    	
    	}
    	
    
    	
    		
    	}
    		
    
    	/*foreach ($mid as $value){
    		$fid=$value['fid'];
    		
    	}*/
    		/*$user=Db::name('hx_user')->where('phone',$phone)->find();
    		$id=$user['id'];
    		if(in_array($id,$fid)){
    			echo "ds";
    		}
    		else{
    			echo "cs";
    		}
*/
    	
 
    	
  
    public function agreefriend(){
    
    }
     
    public function deletfriend(){
    
    }
     
    public function request(){
    	return $this->fetch();
    }
    public function xy()
    {
        return $this->fetch();
    }


    public function xy2()
    {
        return $this->fetch();
    }


    public function xycx()
    {
        return $this->fetch();
    }
}
