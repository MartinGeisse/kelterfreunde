<?php

if (empty($including)) {
	die();
}

define('SLOT_DAUER', 30);
define('ANZAHL_BLOCKS', 2);
define('GESPERRT_TEXT', 'Zur Zeit ist das Buchungssystem zu Verwaltungszwecken gesperrt.');

function getBlockAnzahlSlots($block) {
	if ($block == 0) {
		return 6;
	} else if ($block == 1) {
		return 7;
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}

function getBlockStartzeit($block) {
	if ($block == 0) {
		return array(9, 30);
	} else if ($block == 1) {
		return array(13, 30);
	} else {
		die('ungültige Blocknummer: ' . $block);
	}
}
