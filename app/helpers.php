<?php
function startsWith($haystack, $needle)
{
	return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($haystack, $needle)
{
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
function defaultIfEmpty($val, $default) {
	return (isset($val) && !empty($val)) ? $val : $default;
}

function formatCurrency($intCentsParam, $symbol = true, $commas = true, $neg = true) {
	$intCents = 0;
	if (!is_null($intCentsParam)) {
		$intCents = intval($intCentsParam);
	} else {
		$intCents = 0;
	}
	$dollars = $intCents / 100;
	if ($dollars < 0) {
		$sym = "$";
		$result = number_format(($dollars * -1), 2, '.', $commas ? "," : "");
	} else {
		$sym = "$";
		$result = number_format($dollars, 2, '.', $commas ? "," : "");
	}
	return (($neg && $dollars < 0) ? "(" : "") . ( ($symbol) ? $sym . $result : $result ) . (($neg && $dollars < 0) ? ")" : "");
}

function denull($val, $default = "") {
	return $val != null ? $val : $default;
}

function isNullOrEmpty($value) {
	return (empty($value) || (strcasecmp($value, "null") == 0));
}


function generateUUID() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
		mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
}

function array_remove($string, &$array) {
	unset($array[array_search($string,$array)]);
}