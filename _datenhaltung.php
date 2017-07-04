<?php

if (empty($including)) {
	die();
}

// dev test

function dh_holeBelegung($jahr, $monat, $tag) {
	$result = array();
	$zeit = ersterSlotZeit();
	for ($i=0; $i<SLOTS_PRO_TAG; $i++) {
		array_push($result, array(
			'zeit' => $zeit,
			'belegt' => rand() % 2,
		));
		$zeit = zt_addiereMinuten($zeit, 30);
	}
	return $result;
}
