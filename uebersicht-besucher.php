<?php

$including = true;
require('_konstanten.php');
require('_zeit.php');
require('_datenbank.php');
require('_datenhaltung.php');
require('_besucherschluessel.php');

foreach (dh_holeBelegungBitmap(2017, 01, 01) as $slot) {
	echo zt_zeitpunktText($slot['zeit']), ' - ', $slot['belegt'], "\n";
}

echo '--------------------------', "\n";

foreach (dh_holeBelegungVollstaendig(2017, 01, 01) as $slot) {
	echo zt_zeitpunktText($slot['zeit']), '  ';
	if ($slot['belegt']) {
		echo $slot['name'];
	} else {
		echo '---';
	}
	echo "\n";
}

echo '--------------------------', "\n";

echo bs_erzeugeBesucherschluessel(), "\n";
