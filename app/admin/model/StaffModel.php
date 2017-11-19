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
namespace app\admin\model;

use think\Model;
use think\Db;

class StaffModel extends Model
{

    /**
     * 获取工作人员列表
     */
    public function getStaffList($param)
    {
        $where = ['delete_time' => 0];
        $param['status'] = empty($param['status'])?'':$param['status'];
        if($param['status'] != ''){
            $where['status'] = intval($param['status']);
        }
        $param['certification_status'] = empty($param['certification_status'])?'':$param['certification_status'];
        if($param['certification_status'] != ''){
            $where['certification_status'] = intval($param['certification_status']);
        }
        $address = empty($param['address']) ? '' : $param['address'];
        if (!empty($address)) {
            $where['address'] = ['like', "%$address%"];
        }
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));/*获取本月第一天的时间戳*/
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));/*获取本月最后一天的时间戳*/
        $where['create_time'] = [['>= time', $beginThismonth], ['<= time', $endThismonth]];
        $StaffList = $this->where($where)->order('create_time', 'DESC')->paginate(10);
        return $StaffList;
    }

}