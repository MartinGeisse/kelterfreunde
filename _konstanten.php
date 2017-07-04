<?php

if (empty($including)) {
	die();
}

define('SLOTS_PRO_TAG', 10);
define('ERSTER_SLOT_STUNDEN', 9);
define('ERSTER_SLOT_MINUTEN', 0);

function ersterSlotZeit() {
	return array(ERSTER_SLOT_STUNDEN, ERSTER_SLOT_MINUTEN);
}
