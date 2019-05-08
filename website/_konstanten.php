<?php

if (empty($including)) {
	die();
}

define('SLOT_DAUER', 20);
define('ANZAHL_BLOCKS', 2);
define('GESPERRT_TEXT', 'Zur Zeit ist das Buchungssystem zu Verwaltungszwecken gesperrt.');

function getBlockAnzahlSlots($block) {
	if ($block == 0) {
		return 8;
	} else if ($block == 1) {
		return 9;
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}

function getBlockStartzeit($block) {
	if ($block == 0) {
		return array(9, 40);
	} else if ($block == 1) {
		return array(13, 40);
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}

function getSlotsFuerZentner($zentner) {
	return floor(($zentner - 1) / 3) + 1;
}
