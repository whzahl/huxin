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
namespace app\hxuser\controller;
use cmf\controller\AdminBaseController;
use think\Db;

class UserController extends AdminBaseController
{
	

	/**
	*  用户列表
	*/
    public function index(){
		//搜索
 		$where   = [];
        $request = input('request.');
        if (!empty($request['id'])) {
            $where['id'] = intval($request['id']);
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
            $keywordComplex['name']    = ['like', "%$keyword%"];
            $keywordComplex['phone'] = ['like', "%$keyword%"];
        }
		$list = Db::name('hx_user')->where($where)->whereOr($keywordComplex)->order("id DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
	}


	/**
	*  新增用户
	*/
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->param();
			$res = array(
				'name'  	=> $data['name'],
				'sex'   	=> $data['sex'],
				'phone' 	=> $data['phone'],
				'idcard'	=> $data['idcard'],
				'password' 	=> $data['password'],
				'deal_password' => $data['deal_password'],
				'level' 	=> $data['level'],
 	 	   	    'address' 	=> $data['address'],
 	 	   	    'create_time'   => $this->request->time(),
		    );
			$result = Db::name('hx_user')->insert($res);
			if(!empty($result)){
				$this->success("新增成功！",url('user/index'));
			}else{
				$this->error("新增失败！");
			}
		}
		return $this->fetch();
	}


	/**
	*  修改用户信息
	*/
	public function edit(){

		$id = $this->request->param("id", 0, 'intval');

        $data = Db::name('hx_user')->where(["id" => $id])->find();

        if ($this->request->isPost()) {
            $data1  = $this->request->param();
			$res = array(
				'name'  	=> $data1['name'],
				'sex'   	=> $data1['sex'],
				'phone' 	=> $data1['phone'],
				'idcard'	=> $data1['idcard'],
				'password' 	=> $data1['password'],
				'deal_password' => $data1['deal_password'],
				'level' 	=> $data1['level'],
 	 	   	    'address' 	=> $data1['address'],
 	 	   	    'create_time'   => $this->request->time(),
		    );

            if (Db::name('hx_user')->where(['id' => $data1['id']])->update($res) !== false) {
                $this->success("修改成功！", url('user/index'));
            } else {
                $this->error("修改失败！");
            }
        }
        $this->assign('data',$data);
        return $this->fetch();
	}



	/**
	*  删除用户
	*/
	public function delete(){
		$id = $this->request->param("id", 0, 'intval');
    	$res = Db::name('hx_user')->delete($id);
    	if(!empty($res)){
    		$this->success("删除成功！");
    	}else{
    		$this->error("删除失败！");
    	}
	}



	/**
	*  用户详细信息
	*/
	public function content(){
		$id = $this->request->param("id", 0, 'intval');
		$data = Db::name('hx_user')->where(["id" => $id])->find();
		$time = date("Y-m-d H:i:s",$data['create_time']);
		$this->assign('data',$data);
		$this->assign('time',$time);
		return $this->fetch();
	}

   
}