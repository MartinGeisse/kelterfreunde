<?php

if (empty($including)) {
	die();
}

function getQuerystringIntParameter($key, $min = null, $max = null) {
	if (!array_key_exists($key, $_GET)) {
		die('fehlender URL-Parameter: ' . $key);
	}
	$value = (string)($_GET[$key]);
	if (((string)(int)$value) != $value) {
		die('falsches Parameterformat (int erwartet) für URL-Parameter: ' . $key);
	}
	$value = (int)$value;
	if ($min !== null && $value < $min) {
		die('Wert zu klein für URL-Parameter: ' . $key . ' (Mindestwert: ' . $min . ')');
	}
	if ($max !== null && $value > $max) {
		die('Wert zu groß für URL-Parameter: ' . $key . ' (Maximalwert: ' . $max . ')');
	}
	return $value;
}

function getQuerystringDatum($defaultToday = false) {
	$jahr = array_key_exists('jahr', $_GET) ? getQuerystringIntParameter('jahr', 2017, 2099) : (int)date('Y');
	$monat = array_key_exists('monat', $_GET) ? getQuerystringIntParameter('monat', 1, 12) : (int)date('n');
	$tag = array_key_exists('tag', $_GET) ? getQuerystringIntParameter('tag', 1, 31) : (int)date('j');
	return array(
		'jahr' => $jahr,
		'monat' => $monat,
		'tag' => $tag,
	);
}

function getQuerystringMontag($defaultToday = false) {
	return dt_montagDerselbenWoche(getQuerystringDatum($defaultToday));
}
