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
            $keywordComplex['main_title']    = ['like', "%$keyword%"];
        }
		$list = Db::name('hx_order')->where($where)->whereOr($keywordComplex)->order("id DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
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
			if(!isset($data['photo_urls'])){
				$data['photo_urls'] = '';
			}else{
				foreach ($data['photo_urls'] as $key => $value) {
				$data['picture'] .= $value.';';
				}
			}
			if(!isset($data['status'])){
				$data['status'] = '';
			}
			$res = array(
				'main_title'  	=> $data['title'],
				'picture'   	=> $data['picture'],
				'content' 		=> $data['content'],
				'type'      	=> $data['type'],
 	 	   	    'create_time'   => $this->request->time(),
		    );
		    unset($data['photo_urls']);
			unset($data['photo_names']);
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
		$picture = explode(';', rtrim($data['picture'],';'));
        if ($this->request->isPost()) {
            $data1  = $this->request->param();
            $img = '';
            foreach ($data1['photo_urls'] as $key => $value) {
				$img .= $value.';';
			}
			$res = array(
				'picture' 		=> rtrim($img,';'),
				'main_title'  	=> $data['title'],
				'content' 		=> $data['content'],
				'type'      	=> $data['type'],
 	 	   	    'modify_time'   => $this->request->time(),
		    );
            if (Db::name('hx_order')->where(['id' => $id])->update($res) !== false) {
                $this->success("修改成功！", url('order/index'));
            } else {
                $this->error("修改失败！");
            }
        }
        $this->assign('picture',$picture);
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
		$this->assign('data',$data);
		return $this->fetch();
	}

   
}