<?php
define(BASE_URL, 'http://yugioh.wikia.com/');

// 输入
$url = urldecode($_GET['url']);

// 第一页url
if (empty($url)) $url = BASE_URL.'wiki/Category:Card_Names';
if (strpos($url, BASE_URL) !== 0) {
	echo json_encode(array(
		'state' => false,
		'error' => 'url mismatch'
	));
	exit;
}

if (!$content = file_get_contents($url)) {
	echo json_encode(array(
		'state' => false,
		'error' => 'connect failed'
	));
	exit;
}

// 获取下一页URL
if (preg_match('#<a href="/([^"]*?)"[^>]*>next 200</a>#', $content, $m)) {
	$next = BASE_URL . $m[1];
} else {
	$next = false;
}

// 获取当页所有卡片名
$cards = array();
if (preg_match_all('#href="/wiki/Card_Names:([^"]*)"#', $content, $m)) {
	foreach ($m[1] as $card) {
		$cards[] = array(
			'name' => $card,
			'url' => BASE_URL . 'wiki/' . $card
		);
	}
}

// 返回
echo json_encode(array(
	'state' => true,
	'next' => $next,
	'data' => $cards
));