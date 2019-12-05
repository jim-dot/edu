<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Teacher as TeacherModel;
use think\Session;
class Teacher extends Base
{
    //教师列表
    public function teacherList(){
        $this->view ->assign('title','班级列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('desc','v 1.0');

        $teacher=TeacherModel::all();
        $count=TeacherModel::count();

        //遍历教师数据表
        foreach ($teacher as $key => $value) {
            # code...
            $data=[
                'id'=>$value->id,
                'name'=>$value->name,
                'degree'=>$value->degree,
                'school'=>$value->school,
                'mobile'=>$value->mobile,
                'hiredate'=>$value->hiredate,
                'status'=>$value->status,
                //用关联方法teacher属性方式访问teacher表中数据
                'grade' => isset($value->grade->name)? $value->grade->name : '<span style="color:red;">未分配</span>',
            ];
            $teacherList[]=$data;
        }

        $this->view->assign('teacherList',$teacherList);
        $this->view->assign('count',$count);
        //渲染管理员列表模板
        return $this->view->fetch('teacher_list');

    }

    //教师状态变更
    public function setStatus(Request $request){
        $teacher_id=$request->param('id');
        $result=TeacherModel::get($teacher_id);
        if($result->getData('status')==1){
            TeacherModel::update(['status'=>0],['id'=>$teacher_id]);
        }else{
            TeacherModel::update(['status'=>1],['id'=>$teacher_id]);
        }
    }

    //渲染添加教师信息界面
    public function teacherAdd(){
        $this->view->assign('title','添加班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        return $this->view->fetch('teacher_add');
    }

    //添加教师信息操作
    public function addTeacherInfo(Request $request){
        $getData=$request->param();

        $data['name'] = $getData['name'];
        $data['degree'] = $getData['degree'];
        $data['school'] = $getData['school'];
        $data['mobile'] = $getData['mobile'];
        $data['status'] = $getData['status'];

        $rule=[
            'name|姓名'=>"require",//姓名必填
            'degree|学位'=>"require", //学位必填
            'school|'=>"require", //毕业学校必填
            'mobile|'=>"require", //手机号必填
        ];

        $msg=[
            'name'=>['require'=>'姓名不能为空，请检查'],
            'degree'=>['require'=>'学位不能为空，请检查'],
            'school'=>['require'=>'毕业学校不能为空，请检查'],
            'mobile'=>['require'=>'手机号不能为空，请检查']
        ];

        $request=$this->validate($data,$rule,$msg);

        $result=TeacherModel::create($data);

        $status=0;
        $message='添加失败，请重新添加';
        if(true==$result){
            $status=1;
            $message='添加成功';
        }
        return ['status'=>$status,'message'=>$message];
    }

    //删除操作
    public function deleteTeacher(Request $request){
        $teacher_id=$request->param('id');
        TeacherModel::update(['is_delete'=>1],['id'=>$teacher_id]);
        TeacherModel::destroy($teacher_id);
    }


    //渲染编辑教师信息界面
    public function teacherEdit(Request $request){
        //获取要编辑的教师的ID
        $teacher_id=$request->param('id');
        $result=TeacherModel::get($teacher_id);

        $this->view->assign('title','编辑班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        $this->view->assign('teacher_info',$result->getData());
        return $this->view->fetch('teacher_edit');
    }

    //修改操作
    public function editTeacherInfo(Request $request){
        $data = $request ->param();
        $dataList['name']=$data['name'];
        $dataList['degree']=$data['degree'];
        $dataList['school']=$data['school'];
        $dataList['mobile']=$data['mobile'];
        $dataList['status']=$data['status'];
        $id=$data['id'];
        //设置更新条件      
        $map['id'] =$id;
        //更新当前记录
        $result = TeacherModel::update($dataList, $map);
        /*$result = TeacherModel::where($map)->update($dataList);*/
        
        //设置返回数据
        $status = 0;
        $message = '更新失败,请检查';

        //检测更新结果,将结果返回给grade_edit模板中的ajax提交回调处理
        if (true == $result) {
            $status = 1;
            $message = '恭喜, 更新成功~~';
        }
        return ['status'=>$status, 'message'=>$message];
    }

}