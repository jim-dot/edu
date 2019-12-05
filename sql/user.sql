/*
SQLyog  v12.2.6 (64 bit)
MySQL - 5.5.40 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `user` (
	`id` int (11),
	`name` varchar (150),
	`password` varchar (96),
	`email` varchar (765),
	`role` tinyint (2),
	`status` int (2),
	`create_time` int (11),
	`update_time` int (11),
	`delete_time` int (11),
	`login_time` int (11),
	`login_count` int (11),
	`is_delete` int (11)
); 
insert into `user` (`id`, `name`, `password`, `email`, `role`, `status`, `create_time`, `update_time`, `delete_time`, `login_time`, `login_count`, `is_delete`) values('1','admin','123456','152121@qq.com','1','1',NULL,NULL,NULL,NULL,'0','0');
insert into `user` (`id`, `name`, `password`, `email`, `role`, `status`, `create_time`, `update_time`, `delete_time`, `login_time`, `login_count`, `is_delete`) values('2','dong','111111','21245@qq.com','1','1',NULL,NULL,NULL,NULL,'0','0');
