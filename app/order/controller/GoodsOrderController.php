<?php

namespace app\order\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\portal\service\PostService;

class GoodsOrderController extends AdminBaseController{

	/**
	*	订单
	*
	*/

	public function index(){
		$where   = [];
        $request = input('request.');
        if (!empty($request['goods_type'])) {
            $where['goods_type'] = intval($request['goods_type']);
        }
        if (!empty($request['phone'])) {
            $where['phone'] = intval($request['phone']);
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
		$list = Db::name('goods_order')->where($where)->order("id DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
		return $this->fetch();
	}




}