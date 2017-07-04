<?php

if (empty($including)) {
	die();
}

// dev test

function dh_holeBelegungBitmap($jahr, $monat, $tag) {
	$result = array();
	$zeit = ersterSlotZeit();
	for ($i=0; $i<SLOTS_PRO_TAG; $i++) {
		array_push($result, array(
			'zeit' => $zeit,
			'belegt' => rand() % 2,
		));
		$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
	}
	return $result;
}

function dh_holeBelegungVollstaendig($jahr, $monat, $tag) {
	$result = array();
	$zeit = ersterSlotZeit();
	for ($i=0; $i<SLOTS_PRO_TAG; $i++) {
		
		if (rand() % 2) {
			array_push($result, array(
				'zeit' => $zeit,
				'belegt' => 0,
			));
			$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
			continue;
		}

		$buchstaben = range('a', 'z');
		$name = '';
		for ($j=0; $j<5; $j++) {
			$name .= $buchstaben[array_rand($buchstaben)];
		}

		array_push($result, array(
			'zeit' => $zeit,
			'belegt' => 1,
			'name' => $name,
		));

		$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
	}
	return $result;
}
