<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Student as StudentModel;
use think\Session;
class Student extends Base
{
    //学生列表
    public function studentList(){
        $this->view ->assign('title','学生列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('desc','v 1.0');

        //分页获取所有学生数据
        $studentList=StudentModel::paginate(2);

        $count=StudentModel::count();
        //给$studentList数组中的每一条数据添加外键关联的grade表中的name字段
        foreach ($studentList as $value) {
            # code...
            $value->grade=$value->grade->name;
        }
        $this->view->assign('studentList',$studentList);
        $this->view->assign('count',$count);

        return $this->view->fetch('student_list');

    }

    //学生状态变更
    public function setStatus(Request $request){
        $student_id=$request->param('id');
        $result=StudentModel::get($student_id);
        if($result->getData('status')==1){
            StudentModel::update(['status'=>0],['id'=>$student_id]);
        }else{
            StudentModel::update(['status'=>1],['id'=>$student_id]);
        }
    }

    //渲染添加学生信息界面
    public function studentAdd(){
        $this->view->assign('title','添加班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');

        //将班级表中所有数据赋值给当前模板
        $this->view->assign('gradeList',\app\index\model\Grade::all());

        return $this->view->fetch('student_add');
    }

    //添加学生信息操作
    public function addStudentInfo(Request $request){
        $getData=$request->param();

        $result=StudentModel::create($getData);
        $status=0;
        $message='添加失败';
        if(true==$result){
            $status=1;
            $message='添加成功';
        }
        return ['status'=>$status,'message'=>$message];
    }

    //删除操作
    public function deleteStudent(Request $request){
        $student_id=$request->param('id');
        StudentModel::update(['is_delete'=>1],['id'=>$student_id]);
        StudentModel::destroy($student_id);
    }


    //渲染编辑学生信息界面
    public function studentEdit(Request $request){
        //获取要编辑的学生生的ID
        $student_id = $request -> param('id');
        $result = StudentModel::get($student_id);
        //获取关联表:grade数据
        $result ->grade = $result ->grade->name;
        $this->view->assign('title','编辑班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        $this->view->assign('student_info',$result);
        //将班级表中所有数据赋值给当前模板
        $this->view->assign('gradeList',\app\index\model\Grade::all());
        return $this->view->fetch('student_edit');
    }

    //修改操作
    public function editStudentInfo(Request $request){
        $data = $request ->param();

        //设置更新条件      
        $map['id'] =$data['id'];
        //更新当前记录
        $result = StudentModel::update($data, $map);
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