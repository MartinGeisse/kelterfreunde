<?php

if (empty($including)) {
	die();
}

// dev test

function dh_holeBelegungBitmap($jahr, $monat, $tag) {

	$rows = db_holeBuchungenFuerTag($jahr, $monat, $tag, array('blocknummer', 'slotnummer'));
	echo '<pre>'; var_dump($rows);
	die();

	$result = array();
	for ($i=0; $i<ANZAHL_BLOCKS; $i++) {
		$slotAnzahl = getBlockAnzahlSlots($i);
		$zeit = getBlockStartzeit($i);
		$slots = array();
		for ($j=0; $j<$slotAnzahl; $j++) {
			array_push($slots, array(
				'zeit' => $zeit,
				'belegt' => rand() % 2,
			));
			$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
		}
		array_push($result, $slots);
	}
	return $result;
}

function dh_holeBelegungVollstaendig($jahr, $monat, $tag) {
	$result = array();
	for ($i=0; $i<ANZAHL_BLOCKS; $i++) {
		$slotAnzahl = getBlockAnzahlSlots($i);
		$zeit = getBlockStartzeit($i);
		$slots = array();
		for ($j=0; $j<$slotAnzahl; $j++) {
			
			// nicht belegt
			if (rand() % 2) {
				array_push($slots, array(
					'zeit' => $zeit,
					'belegt' => 0,
				));
				$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
				continue;
			}

			// Name
			$buchstaben = range('a', 'z');
			$name = '';
			for ($k=0; $k<5; $k++) {
				$name .= $buchstaben[array_rand($buchstaben)];
			}

			// Slot
			array_push($slots, array(
				'zeit' => $zeit,
				'belegt' => 1,
				'name' => $name,
			));

			$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
		}
		array_push($result, $slots);
	}
	return $result;
}
