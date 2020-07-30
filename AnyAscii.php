<?php

class AnyAscii {

	private static $blocks = array();

	public static function transliterate($utf8) {
		$result = '';
		$i = 0;
		$len = strlen($utf8);
		while ($i < $len) {
			$cp = self::utf8NextCodepoint($utf8, $i);
			if ($cp < 0x80) {
				$result .= chr($cp);
				continue;
			}
			$blockNum = $cp >> 8;
			if (!isset(self::$blocks[$blockNum])) {
				$fileName = sprintf('%s/_data/_%03x.php', __DIR__, $blockNum);
				$block = file_exists($fileName) ? require $fileName : array();
				self::$blocks[$blockNum] = $block;
			} else {
				$block = self::$blocks[$blockNum];
			}
			$lo = ($cp & 0xFF);
			if (isset($block[$lo])) {
				$result .= $block[$lo];
			}
		}
		return $result;
	}

	private static function utf8NextCodepoint($s, &$i) {
		$b1 = ord($s[$i++]);
		if ($b1 < 0x80) return $b1;
		$b2 = ord($s[$i++]);
		if ($b1 < 0xE0) return (($b1 & 0x1F) << 6) | ($b2 & 0x3F);
		$b3 = ord($s[$i++]);
		if ($b1 < 0xF0) return (($b1 & 0xF) << 12) | (($b2 & 0x3F) << 6) | ($b3 & 0x3F);
		$b4 = ord($s[$i++]);
		return (($b1 & 0x7) << 18) | (($b2 & 0x3F) << 12) | (($b3 & 0x3F) << 6) | ($b4 & 0x3F);
	}
}
