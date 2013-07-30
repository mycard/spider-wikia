<?php
class SpiderList {
	const BASE_URL = 'http://yugioh.wikia.com/';
	protected $content;

	public function main($url) {
		if (empty($url)) {
			$url = self::BASE_URL.'wiki/Category:Card_Names';
		}
		if (strpos($url, self::BASE_URL) !== 0) {
			return array(
				'state' => false,
				'error' => 'url mismatch'
			);
		}

		if (!$this->content = file_get_contents($url)) {
			return array(
				'state' => false,
				'error' => 'connect failed'
			);
		}
		$next = $this->get_next();
		$cards = $this->get_cards();
		return array(
			'state' => true,
			'next' => $next,
			'data' => $cards
		);
	}

	protected function get_next() {
		// 获取下一页URL
		if (preg_match('#<a href="/([^"]*?)"[^>]*>next 200</a>#', $this->content, $m)) {
			return self::BASE_URL . $m[1];
		}
		return false;
	}

	protected function get_cards() {
		// 获取当页所有卡片名
		$cards = array();
		if (preg_match_all('#href="/wiki/Card_Names:([^"]*)"#', $this->content, $m)) {
			foreach ($m[1] as $card) {
				$cards[] = array(
					'name' => $card,
					'url' => self::BASE_URL . 'wiki/' . $card
				);
			}
		}
		return $cards;
	}
}