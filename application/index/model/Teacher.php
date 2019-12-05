<?php

namespace app\index\model;


use think\Model;
use traits\model\SoftDelete;

class Teacher extends Model
{
    //引用软删除方法集
    /*use SoftDelete;*/

    //设置当前表默认日期时间显示格式
    protected $dateFormat = 'Y/m/d';

    //定义表中的删除时间字段,来记录删除时间
    protected $deleteTime = 'delete_time';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $updateTime = 'update_time';
    protected $create_time='create_time';

    protected $type = [
        'hiredate'=>'timestamp'
    ];

    // 定义自动完成的属性
    protected $insert = ['status' => 1];

    //状态字段，status返回值处理
    public function getStatusAttr($value){
        $status=[
            0=>'已禁用',
            1=>'已启用'
        ];
        return $status[$value];
    }

    public function getDegreeAttr($value)
    {
        $degree = [
            1=>'专科',
            2=>'本科',
            3=>'研究生',
            4=>'博士',
        ];
        //根据表中数据返回对应值
        return $degree[$value];
    }

    //设置与grade表的反关联
    public function grade()
    {
        // 教师表teacher BELONGS TO 关联班级grade
        return $this->belongsTo('Grade');
    }

}