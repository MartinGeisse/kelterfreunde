<?php

if (empty($including)) {
	die();
}

// dev test

function dh_holeBelegungBitmap($jahr, $monat, $tag) {

	// leeres Ergebnis erzeugen
	$result = array();
	for ($i=0; $i<ANZAHL_BLOCKS; $i++) {
		$slotAnzahl = getBlockAnzahlSlots($i);
		$zeit = getBlockStartzeit($i);
		$slots = array();
		for ($j=0; $j<$slotAnzahl; $j++) {
			array_push($slots, array(
				'zeit' => $zeit,
				'belegt' => false,
			));
			$zeit = zt_addiereMinuten($zeit, SLOT_DAUER);
		}
		array_push($result, $slots);
	}

	// mit Daten aus der Datenbank befüllen
	$rows = db_holeBuchungenFuerTag($jahr, $monat, $tag, array('blocknummer', 'slotnummer'));
	foreach ($rows as $row) {
		$blocknummer = $row['blocknummer'];
		$slotnummer = $row['slotnummer'];
		if (!array_key_exists($blocknummer, $result)) {
			continue;
		}
		if (!array_key_exists($slotnummer, $result[$blocknummer])) {
			continue;
		}
		$result[$blocknummer][$slotnummer]['belegt'] = true;
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

function dh_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer) {
	return db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer);
}
