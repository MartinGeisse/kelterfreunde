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
		$fields = array('id', 'blocknummer', 'slotnummer', 'name', 'telefonnummer', 'zentner');
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
			$result[$blocknummer][$slotnummer]['id'] = $row['id'];
			$result[$blocknummer][$slotnummer]['name'] = $row['name'];
			$result[$blocknummer][$slotnummer]['telefonnummer'] = $row['telefonnummer'];
			$result[$blocknummer][$slotnummer]['zentner'] = $row['zentner'];
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

function dh_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummerStart, $anzahlSlots, $name, $telefonnummer, $zentner) {
	db_beginTransaction();
	if (!db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummerStart, $name, $telefonnummer, $zentner)) {
		db_rollbackTransaction();
		return false;
	}
	for ($i = 1; $i < $anzahlSlots; $i++) {
		if (!db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummerStart + $i, null, null, null)) {
			db_rollbackTransaction();
			return false;
		}
	}
	db_commitTransaction();
	return true;
}

function dh_loescheBuchung($jahr, $monat, $tag, $blocknummer, $slotnummerStart) {
	$belegung = dh_holeBelegungVollstaendig($jahr, $monat, $tag);
	if (empty($belegung[$blocknummer][$slotnummerStart]['name'])) {
		return;
	}
	$block = $belegung[$blocknummer];
	$ids = array($block[$slotnummerStart]['id']);
	for ($i = 1; !empty($block[$slotnummerStart + $i]['id']); $i++) {
		if (!empty($block[$slotnummerStart + $i]['name'])) {
			break;
		}
		$ids[] = $block[$slotnummerStart + $i]['id'];
	}
	db_loescheBuchungen($ids);
}
	
function dh_setzeVariable($name, $wert) {
	db_setzeVariable($name, $wert);
}

function dh_holeVariable($name) {
	return db_holeVariable($name);
}

function dh_istTagFreigeschaltet($jahr, $monat, $tag) {
	return db_istTagFreigeschaltet($jahr, $monat, $tag);
}

function dh_setzeObTagFreigeschaltet($jahr, $monat, $tag, $freigeschaltet) {
	db_setzeObTagFreigeschaltet($jahr, $monat, $tag, $freigeschaltet);
}
