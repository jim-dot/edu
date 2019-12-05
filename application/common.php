<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 输出各种类型的数据，调试程序时打印数据使用。
 * @param mixed	参数：可以是一个或多个任意变量或值
 */
function p() {
	$args = func_get_args(); //获取多个参数
	if (count($args) < 1) {
		Debug::addmsg("<font color='red'>必须为p()函数提供参数!");
		return;
	}
	header("Content-Type: text/html; charset=utf8");
	echo '<div style="width:100%;text-align:left"><pre>';
	//多个参数循环输出
	foreach ($args as $arg) {
		if (is_array($arg)) {  
			print_r($arg);
			echo '<br>';
		} elseif (is_string($arg)) {
			echo $arg.'<br>';
		} else {
			var_dump($arg);
			echo '<br>';
		}
	}
	echo '</pre></div>';	
}

