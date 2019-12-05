<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
class Base extends Controller
{
  protected function _initialize(){
  	parent::_initialize();  //继承父类中的初始化操作
  	define('USER_ID',Session::get('user_id'));

  }
	
  //判断用户是否登录，放在后台的入口:index/index
  protected function isLogin(){
  	$userId =USER_ID;
  	if(empty($userId)){
  		$this->error('用户未登录，无权访问',url('user/login'));
  	}
  }

  //防止用户重复登录  user/login
  protected function alreadyLogin(){
  	$userId =USER_ID;
  	if(!empty($userId)){
  		$this->error('用户已经登录，请勿重复登录',url('index/index'));
  	}
  }
}
