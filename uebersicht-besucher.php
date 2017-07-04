<?php

$including = true;
require('_konstanten.php');
require('_zeit.php');
require('_datenbank.php');
require('_datenhaltung.php');

foreach (dh_holeBelegung(2017, 01, 01) as $slot) {
	echo zt_zeitpunktText($slot['zeit']), ' - ', $slot['belegt'], "\n";
}
