<?php
error_reporting(0);
switch ($_GET['action']) {
	case 'spiderlist':
		include 'spider/list.php';
		$class = new SpiderList();
		echo json_encode($do->main(urldecode($_GET['url'])));
		break;
	case 'spidercontent':
		include 'spider/content.php';
		$class = new SpiderContent();
		echo json_encode($do->main($_GET['name']));
		break;
	case 'savelist':
}
