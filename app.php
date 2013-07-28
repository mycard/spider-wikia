<?php
switch ($_GET['action']) {
	case 'spiderlist':
		include 'spider/list.php';
		break;
	case 'spidercontent':
		include 'spider/content.php';
		break;
	case 'savelist':
}