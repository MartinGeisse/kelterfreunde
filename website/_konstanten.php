<?php

if (empty($including)) {
	die();
}

define('SLOT_DAUER', 20);
define('ANZAHL_BLOCKS', 2);
define('GESPERRT_TEXT', 'Zur Zeit ist das Buchungssystem zu Verwaltungszwecken gesperrt.');

function getBlockAnzahlSlots($block) {
	if ($block == 0) {
		return 10;
	} else if ($block == 1) {
		return 9;
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}

function getBlockStartzeit($block) {
	if ($block == 0) {
		return array(9, 0);
	} else if ($block == 1) {
		return array(13, 40);
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}

function getSlotsFuerZentner($zentner) {
	return floor(($zentner - 1) / 3) + 1;
}

$obstsortenNamen = array(
	'A' => 'Äpfel',
	'B' => 'Birnen',
	'Q' => 'Quitten',
	'T' => 'Trauben'
);

function getObstsortenName($obstsorte) {
	global $obstsortenNamen;
	if (isset($obstsortenNamen[$obstsorte])) {
		return $obstsortenNamen[$obstsorte];
	} else {
		return $obstsorte;
	}
}
