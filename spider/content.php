<?php
class SpiderContent {
	const BASE_URL = 'http://yugioh.wikia.com/wiki/';
	protected $content;

	public function main($name) {
		if (empty($name)) {
			return array(
				'state' => false,
				'error' => 'params miss'
			);
		}
		if (!$this->content = file_get_contents(self::BASE_URL.$name)) {
			return array(
				'state' => false,
				'error' => 'connect failed'
			);
		}
		$id = $this->get_card_id();
		if (!$image = $this->get_image($id, $name)) {
			return array(
				'state' => false,
				'error' => 'not a card page'
			);
		}
		return array(
			'state' => true,
			'image' => $image,
			'id' => $id
		);
	}

	protected function get_card_id() {
		// 获得卡片ID
		if (preg_match('#<a[^>]*>(\d{8,8})</a>#', $this->content, $m)) {
			$id = $m[1];
		} else {
			$id = '00000000';
		}
		return $id;
	}

	protected function get_image($id = '00000000', $name) {
		// 采集图片
		if (preg_match('#cardtable-cardimage[^<]*?<a[^>]*href="([^"]*)"[^>]*>#', $this->content, $m)) {
			$image = $m[1];
			$ext = array_pop(explode('.', $image));
			$image_data = file_get_contents($image);
			$location_image = "data/$id|$name.$ext";
			@file_put_contents($location_image, $image_data);
			return $location_image;
		}
		return fales;
	}
}