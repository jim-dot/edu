<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\User as UserModel;
use think\Session;
class User extends Base
{
    //登录界面
    public function login()
    {
        $this->alreadyLogin();  //防止重复登录
        return $this->view->fetch();
    }

    //验证登录($this->validate($data,$rule,$msq))
    public function checkLogin(Request $request)
    {
        //初始返回参数
        $status=0;
        $result='';
        $data=$request->param();

        //创建验证规则
        $rule=[
            'name|用户名'=>'require',//用户名必填
            'password|密码'=>'require',//密码必填
            'verify|验证码'=>'require|captcha',//验证码验证
        ];

        //自定义验证失败提示信息
        $msg=[
            'name'=>['require'=>'用户名不能为空，请检查'],
            'password'=>['require'=>'密码不能为空，请检查'],
            'verify'=>[
            'require'=>'验证码不能为空，请检查',
            'captcha'=>'验证码错误'
            ],
        ];

        //进行验证
        $result=$this->validate($data,$rule,$msg);

        //如果验证通过则执行
        if($result===true){
            //构造查询条件
            $map=[
                'name'=>$data['name'],
                'password'=>$data['password'],
            ];

            //查询用户信息
            $user=UserModel::get($map);
            if($user==null){
                $result='没有找到该用户';
            }else{
                $status=1;
                $result='验证通过，点击【确定】进入';

                //设置用户登录信息，用session
                Session::set('user_id',$user->id);  //用户ID
                Session::set('user_info',$user->getData());  //获取用户所有信息
            }

        }

        return ['status'=>$status,'message'=>$result,'data'=>$data];
    }

    //退出登录
    public function logout()
    {
        //注销Session
        Session::delete('user_id');
        Session::delete('user_info');
        $this->success('注销登录，正在返回','user/login');
    }

    //管理员列表
    public function adminList(){
        $this-> view ->assign('title','管理员列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('desc','教学案例');

        $this->view->count=UserModel::count();

        //判断当前是不是admin用户
        //先通过session获取到用户名
        $userName=Session::get('user_info.name');
        if($userName=='admin'){
            $list=UserModel::all();  //admin用户可以查看所有记录，数据要经过模型获取器处理
        }else{
            $list=UserModel::all(['name'=>$userName]);  //非admin用户只能查看自己的记录
        }
        $this->view->assign('list',$list);
        //渲染管理员列表模板
        return $this->view->fetch('admin_list');

    }

    //管理员状态变更
    public function setStatus(Request $request){
        $user_id=$request->param('id');
        $result=UserModel::get($user_id);
        if($result->getData('status')==1){
            UserModel::update(['status'=>0],['id'=>$user_id]);
        }else{
            UserModel::update(['status'=>1],['id'=>$user_id]);
        }
    }

    //渲染编辑管理员界面
    public function adminEdit(Request $request){
        $user_id=$request->param('id');
        $result=UserModel::get($user_id);
        $this->view->assign('title','编辑管理员信息');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        $this->view->assign('user_info',$result->getData());
        return $this->view->fetch('admin_edit');
    }

    //渲染添加管理员界面
    public function adminAdd(){
        $this->view->assign('title','添加管理员');
        $this->view->assign('keywords','edu.com');
        $this->view->assign('desc','教学管理系统');
        return $this->view->fetch('admin_add');
    }
    //检测用户名是否可用
    public function checkUserName(Request $request){
        $userName=trim($request->param('name'));
        $status=1;
        $message='用户名可用';
        if($userName==null){
            $status=0;
            $message='用户名不能为空，请检查';
        }else{
            if(UserModel::get(['name'=>$userName])){
                //如果在表中查询到该用户名
                $status=0;
                $message='用户名重复，请重新输入~~';
            }else{
                $status=1;
                $message='用户名可用';
            } 
        }
         
        return ['status'=>$status, 'message'=>$message];
    }
    //检查用户邮箱是否可用
    public function checkUserEmail(Request $request){
        $userEmail=trim($request->param('email'));
        $status=1;
        $message='';
        if($userEmail==null){
            $status=0;
            $message='用户邮箱不能为空，请检查';
        }else{
            if(UserModel::get(['email'=>$userEmail])){
                $status=0;
                $message='用户邮箱已被使用，请重新输入';
            }else{
                $status=1;
                $message='用户邮箱可用';
            }
        }
        return ['status'=>$status, 'message'=>$message];
    }
    //添加操作
    public function addUser(Request $request){
        $getData=$request->param();
        $data['name'] = $getData['name'];
        $data['password'] = $getData['password'];
        $data['status'] = $getData['status'];
        $data['email'] = $getData['email'];
        $data['role'] = $getData['role'];
        $status=1;
        $message='添加成功';

        //有逻辑错误，有时间再来修复
        $rule=[
            'name|用户名'=>"require|min:3|max:10",
            'password|密码'=>"require|min:3|max:10",
            'email|邮箱'=>"require|email"
        ];

        $request=$this->validate($data,$rule);
        
        if($request===true){
            $user=UserModel::create($data);
            if($user===null){
                $status=0;
                $message='添加失败';
            }
        }
        return ['status'=>$status,'message'=>$message];
    }

    //删除操作
    public function deleteUser(Request $request){
        $user_id=$request->param('id');
        UserModel::update(['is_delete'=>1],['id'=>$user_id]);
        UserModel::destroy($user_id);
    }

    //修改操作
    public function editUser(Request $request){
        $param = $request -> param();

        //去掉表单中为空的数据,即没有修改的内容
        foreach ($param as $key => $value ){
            if (!empty($value)){
                $data[$key] = $value;
            }
        }

        $condition = ['id'=>$data['id']] ;
        $result = UserModel::update($data, $condition);

        //如果是admin用户,则更改用户信息中角色role,用以调用
        if(Session::get('user_info.name')=='admin'){
            Session::set('user_info.role',$data['role']);
        }

        if(true==$result){
            return ['status'=>1,'message'=>'信息更新成功'];
        }else{
            return ['status'=>0,'message'=>'信息更新失败'];
        }
    }

}