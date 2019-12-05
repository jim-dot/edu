<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Grade as GradeModel;
use think\Session;
use think\Db;
class Grade extends Base
{
    //班级列表
    public function gradeList(){
        $this->view ->assign('title','班级列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('desc','v 1.0');

        // $gradeList=GradeModel::all();
        // $count=GradeModel::count();
        $gradeList = Db::name('Grade')->select();
        $count=Db::name('Grade')->count();
        //遍历班级数据表
        // foreach ($gradeList as $key => $value) {
        //     # code...
        //     $gradeList[$key]['teacher'] = isset($value->teacher->name)? $value->teacher->name : '<span style="color:red;">未分配</span>';
        //     p($value->teacher->name);
        //     // $data=[
        //     //     'id'=>$value->id,
        //     //     'name'=>$value->name,
        //     //     'length'=>$value->length,
        //     //     'price'=>$value->price,
        //     //     'create_ time'=>$value->create_time,
        //     //     'status'=>$value->status,
        //     //     //用关联方法teacher属性方式访问teacher表中数据
        //     //     'teacher' => isset($value->teacher->name)? $value->teacher->name : '<span style="color:red;">未分配</span>',
        //     // ];
        //     // $gradeList[]=$data;
        // }
        foreach ($gradeList as $key => $value) {
            //跳过模型层，直接从数据库中查询teacher表
            $teacherName = Db::name('Teacher')->where(array('id'=>$value['teacher_id']))->value('name');
            if(empty($teacherName)){
               $gradeList[$key]['teacher'] =  '<span style="color:red;">未分配</span>'; 
            }
            $gradeList[$key]['teacher'] =  $teacherName;
            if($value['status'] ==1){
                $gradeList[$key]['status'] = '已启用';
            }else{
                $gradeList[$key]['status'] = '已禁用';
            }
            
        }
        $this->view->assign('gradeList',$gradeList);
        $this->view->assign('count',$count);
        //渲染管理员列表模板
        return $this->view->fetch('grade_list');

    }

    //班级状态变更
    public function setStatus(Request $request){
        $grade_id=$request->param('id');
        $result=GradeModel::get($grade_id);
        if($result->getData('status')==1){
            GradeModel::update(['status'=>0],['id'=>$grade_id]);
        }else{
            GradeModel::update(['status'=>1],['id'=>$grade_id]);
        }
    }

    //渲染添加班级信息界面
    public function gradeAdd(){
        $this->view->assign('title','添加班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        $teacherList=Db::name('Teacher')->field('name')->where('grade_id',0)->select();

        $this->view->assign('teacherList',$teacherList);
        return $this->view->fetch('grade_add');
    }

    //添加操作
    public function addGradeInfo(Request $request){
        $getData=$request->param();

        $data['name'] = $getData['name'];
        $data['length'] = $getData['length'];
        $data['status'] = $getData['status'];
        $data['price'] = $getData['price'];
        $data['create_time']=$getData['create_time'];
        $dataTeacher['teacher']=$getData['teacher'];
        $status=0;
        $message='添加失败，请重新添加';

        $teacherName = $dataTeacher['teacher'];
        
        //根据教师名称查询教师表中相关字段
        $map['name'] = $teacherName;
        $resultList=Db::name('Teacher')->where($map)->find();
        /*
        输出上一条sql语句的完整代码
        p(Db::name('Teacher')->getLastSql());die;
        */

        //将查询出来的教师ID存入$data[]数组中
        $data['teacher_id']=$resultList['id'];

        $result = Db::name('Grade')->insert($data);
        if($result){

            //在将班级信息添加到数据库后再次查询这条存储进去的字段
            $resultGrade=Db::name('Grade')->where('name',$data['name'])->find();
            //提取出id，存入teacher表中
            $grade_id=$resultGrade['id'];
            
            if($resultGrade){
                $status=1;
                $message='添加成功'; 
            }
        }

        return ['status'=>$status,'message'=>$message];
        
        // $rule=[
        //     'name|班级名称'=>"require",//班级名称必填
        //     'length|学制'=>"require", //学制必填
        //     'price|'=>"require", //学费必填
        // ];

        // $msg=[
        //     'name'=>['require'=>'班级名称不能为空，请检查'],
        //     'length'=>['require'=>'学制不能为空，请检查'],
        //     'price'=>['require'=>'学费不能为空，请检查']
        // ];

        // $request=$this->validate($data,$rule,$msg);

        // $result=GradeModel::create($data);


        // if(true==$result && true==$resultTeacher){
        //     $status=1;
        //     $message='添加成功';
        // }
        // return ['status'=>$status,'message'=>$message];
    }

    //删除操作
    public function deleteGrade(Request $request){
        $grade_id=$request->param('id');
        GradeModel::update(['is_delete'=>1],['id'=>$grade_id]);
        GradeModel::destroy($grade_id);
    }


    //渲染编辑班级信息界面
    public function gradeEdit(Request $request){
        //获取要编辑的班级的ID
        $grade_id=$request->param('id');
        $gradeList=GradeModel::get($grade_id);

        //将teacher_id单独取出来
        $teacher_id=$gradeList['teacher_id'];
        $map['id']=$teacher_id;
        //根据teacher_id在teacher表中查询相关字段
        $teacherList=Db::name('Teacher')->where($map)->find();
        //将查询到的教师信息中的教师姓名存入$gradeList()数组
        $gradeList['teacherName']=$teacherList['name'];
        $this->view->assign('title','编辑班级信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        $this->view->assign('grade_info',$gradeList);
        return $this->view->fetch('grade_edit');
    }

    //修改操作
    public function editGradeInfo(Request $request){
        //从提交表单中排除关联字段teacher字段
        $data = $request -> except('teacherName');
        //$data = $request -> param();  //如果全部获取,提交会提示缺少字段teacher

        //设置更新条件
        $condition = ['id'=>$data['id']];

        //更新当前记录
        $result = GradeModel::update($data,$condition);

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