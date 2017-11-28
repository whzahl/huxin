<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\hxorder\controller;
use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ThemeModel;
use app\portal\service\PostService;

class OrderController extends AdminBaseController
{
	

	/**
	*  订单列表
	*/
    public function index(){
		//搜索
 		$where   = [];
        $request = input('request.');
        if (!empty($request['id'])) {
            $where['id'] = intval($request['id']);
        }
        if (!empty($request['status'])) {
            $where['status'] = intval($request['status']);
        }
        //时间搜索
        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['add_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['add_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['add_time'] = ['<= time', $endTime];
            }
        }
        //关键词搜索
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['uname']    = ['like', "%$keyword%"];
        }
		$list = Db::name('hx_order')->where($where)->whereOr($keywordComplex)->order("id DESC")->paginate(10);
        $data = $list->toArray();
		$data1 = array();
		foreach ($data['data'] as $key => $value) {
		    $fname = Db::name('hx_user')->field('name')->where(array('id' => $value['fid']))->find();
		    $uname = Db::name('hx_user')->field('name')->where(array('id' => $value['uid']))->find();
		    $value['fname'] = $fname['name'];
		    $value['uname'] = $uname['name'];
		    $data1[] = $value;
		}
        // 获取分页显示
        $page = $list->render();
        $this->assign('data', $data1);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
	}


	/**
	*  新增订单
	*/
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->param();
			$uid = Db::name('hx_user')->where(['name' => $data['uname']])->field('id')->find();
			$fid = Db::name('hx_user')->where(['name' => $data['fname']])->field('id')->find();
			$data['uid'] = $uid['id'];
			$data['fid'] = $fid['id'];
			$res = array(
				'uid'  	    => $data['uid'],
				'fid'   	=> $data['fid'],
				'price' 	=> $data['price'],
				'start_time'=> $data['start_time'],
 	 	   	    'end_time'  => $data['end_time'],
			    'rate' 		=> $data['rate'],
			    'status'    => $data['status'],
			    'create_time' => $this->request->time(),
		    );
			$result = Db::name('hx_order')->insert($res);
			if(!empty($result)){
				$this->success("新增成功！",url('order/index'));
			}else{
				$this->error("新增失败！");
			}
		}
		return $this->fetch();
	}


	/**
	*  修改订单信息
	*/
	public function edit(){
		$id = $this->request->param("id", 0, 'intval');
        $data = Db::name('hx_order')->where(["id" => $id])->find();
        //两个人的名字  
            $fname = Db::name('hx_user')->field('name')->where(array('id' => $data['fid']))->find();
            $uname = Db::name('hx_user')->field('name')->where(array('id' => $data['uid']))->find();
            $data['fname'] = $fname['name'];
            $data['uname'] = $uname['name'];
        if ($this->request->isPost()) {
            $data2  = $this->request->param();
			$res = array(
			    'end_time'  => $data2['end_time'],
			    'status'    => $data2['status'], 
		    );
            if (Db::name('hx_order')->where(['id' => $id])->update($res) !== false) {
                $this->success("修改成功！", url('order/index'));
            } else {
                $this->error("修改失败！");
            }
        }
        $this->assign('data',$data);
        return $this->fetch();
	}



	/**
	*  删除订单
	*/
	public function delete(){
		$id = $this->request->param("id", 0, 'intval');
    	$res = Db::name('hx_order')->delete($id);
    	if(!empty($res)){
    		$this->success("删除成功！");
    	}else{
    		$this->error("删除失败！");
    	}
	}



	/**
	*  订单详细信息
	*/
	public function content(){
		$id = $this->request->param("id", 0, 'intval');
		$data = Db::name('hx_order')->where(["id" => $id])->find();	
		$uname = Db::name('hx_user')->where(["id" => $data['uid']])->field('name')->find();
		$fname = Db::name('hx_user')->where(["id" => $data['fid']])->field('name')->find();
		$data['uname'] = $uname['name'];
		$data['fname'] = $fname['name'];
		$this->assign('data',$data);
		return $this->fetch();
	}

   
}