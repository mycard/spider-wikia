<?php
define(BASE_URL, 'http://yugioh.wikia.com/wiki/');
//输入
if (!$name = $_GET['name']) {
	echo json_encode(array(
		'state' => false,
		'error' => 'params miss'
	));
	exit;
}
$url = BASE_URL.$name;
if (!$content = file_get_contents($url)) {
	echo json_encode(array(
		'state' => false,
		'error' => 'connect failed'
	));
	exit;
}

// 获得卡片ID
if (preg_match('#<a[^>]*>(\d{8,8})</a>#', $content, $m)) {
	$id = $m[1];
} else {
	$id = '00000000';
}

// 采集图片
if (preg_match('#cardtable-cardimage[^<]*?<a[^>]*href="([^"]*)"[^>]*>#', $content, $m)) {
	$image = $m[1];
	$ext = array_pop(explode('.', $image));
	$image_data = file_get_contents($image);
	$location_image = "data/$id|$name.$ext";
	@file_put_contents($location_image, $image_data);
} else {
	echo json_encode(array(
		'state' => false,
		'error' => 'not a card page'
	));
	exit;
}

// 返回
echo json_encode(array(
	'state' => true,
	'image' => $location_image,
	'id' => $id
));