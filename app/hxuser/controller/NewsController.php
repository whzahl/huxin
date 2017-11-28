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
use app\admin\model\ThemeModel;
use app\portal\service\PostService;

class NewsController extends AdminBaseController
{
	

	/**
	*  消息列表
	*/
    public function index(){
        
        $list = Db::name('hx_infos')->order("id DESC")->paginate(10);
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
        $this->assign('list', $data1);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
	}


	/**
	*  删除
	*/
	public function delete(){
		$id = $this->request->param("id", 0, 'intval');
    	$res = Db::name('hx_infos')->delete($id);
    	if(!empty($res)){
    		$this->success("删除成功！");
    	}else{
    		$this->error("删除失败！");
    	}
	}



   
}