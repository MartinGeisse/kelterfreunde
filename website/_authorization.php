<?php

if (empty($including)) {
	die();
}

function au_generateSignature($timestamp) {
	return hash_hmac('sha256', $timestamp, $authorizationConfiguration['secret']);
}

function au_generateTokenFromExpirationTimestamp($timestamp) {
	return $timestamp . '|' .au_generateSignature($timestamp);
}

function au_generateNewToken() {
	return au_generateTokenFromExpirationTimestamp(time() + 2 * 3600);
}

function au_validateToken($token) {
	if (strlen($token) > 300) {
		return false;
	}
	$segments = explode('|', $token);
	if (count($segments) != 2) {
		return false;
	}
	return au_generateSignature($segments[0]) == $segments[1];
}

function au_sendCookie() {
	setcookie('authorization', au_generateNewToken());
}

function au_checkCookie() {
	return isset($_COOKIE['authorization']) && au_validateToken(_COOKIE['authorization']);
}

function au_clearCookie() {
	setcookie('authorization', '-');
}
