<?php
namespace app\index\model;
use think\Model;

class Student extends Model{
    //设置当前日期显示格式
    protected $dataFormat='Y/m/d';

    //开启自动写入时间戳
    protected $autoWriteTimeStamp=true;

    protected $deleteTime='delete_time';
    protected $createTime='create_time';
    protected $updateTime='update_time';

    protected $type=['start_time'=>'timestamp'];

    //定义性别的属性
    public function getSexAttr($value){
        $sex=[0=>'女',1=>'男'];
        return $sex[$value];
    }

    //状态字段，status返回值处理
    public function getStatusAttr($value){
        $status=[
            0=>'已禁用',
            1=>'已启用'
        ];
        return $status[$value];
    }

    //定义与班级表的一对多关联
    public function grade(){
        return $this->belongsTo('Grade');
    }
    

}