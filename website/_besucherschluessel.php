<?php

if (empty($including)) {
	die();
}

function bs_erzeugeBesucherschluessel() {
	$payload = hash_hmac('md5', time() . '/' . rand(), '2-=s92OqnvWU ._2sj', true);
	return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
}
