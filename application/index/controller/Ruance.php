<?php
namespace app\index\controller;
use think\Controller;
class Ruance extends Controller
{
    public function index()
    {
    	$this->view->assign('title','查询年月日');
        return $this->view->fetch();
    }
}
