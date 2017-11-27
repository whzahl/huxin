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

class IndexController extends HomeBaseController
{
    public function index()
    {
    	$id = session('userid');
    	$info = Db::name('hx_infos')->where(['fid' => $id])->where(['status' => 0])->select()->toArray();
    	$this->assign('info',$info);
        return $this->fetch(':index');
    }

}
