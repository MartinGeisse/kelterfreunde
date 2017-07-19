<?php

if (empty($including)) {
	die();
}

function _dh_holeBelegungIntern($jahr, $monat, $tag, $vollstaendig) {

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
	if ($vollstaendig) {
		$fields = array('blocknummer', 'slotnummer', 'name', 'telefonnummer');
	} else {
		$fields = array('blocknummer', 'slotnummer');
	}
	$rows = db_holeBuchungenFuerTag($jahr, $monat, $tag, $fields);
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
		if ($vollstaendig) {
			$result[$blocknummer][$slotnummer]['name'] = $row['name'];
			$result[$blocknummer][$slotnummer]['telefonnummer'] = $row['telefonnummer'];
		}
	}

	return $result;
}

function dh_holeBelegungBitmap($jahr, $monat, $tag) {
	return _dh_holeBelegungIntern($jahr, $monat, $tag, false);
}

function dh_holeBelegungVollstaendig($jahr, $monat, $tag) {
	return _dh_holeBelegungIntern($jahr, $monat, $tag, true);
}

function dh_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer) {
	return db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer);
}
