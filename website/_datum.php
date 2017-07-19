<?php

function dt_letzterTagDesMonats($monat, $jahr) {
	if ($monat < 1 || $monat > 12) {
		die('ungültiger Monat: ' . $monat);
	}
	$tabelle = array(
		31, // jan
		28, // feb
		31, // mär
		30, // apr
		31, // mai
		30, // jun
		31, // jul
		31, // aug
		30, // sep
		31, // okt
		30, // nov
		31, // dev
	);
	if ($monat == 2) {
		if ($jahr % 4 != 0) {
			$schaltjahr = false;
		} else if ($jahr % 100 != 0) {
			$schaltjahr = true;
		} else if ($jahr % 400 != 0) {
			$schaltjahr = false;
		} else {
			$schaltjahr = true;
		}
		if ($schaltjahr) {
			return 29;
		}
	}
	return $tabelle[$monat - 1];
}

function dt_datumValide($datum) {
	if ($datum['tag'] < 1 || $datum['monat'] < 1 || $datum['jahr'] < 2000) {
		return false;
	}
	if ($datum['monat'] > 12 || $datum['jahr'] > 2099) {
		return false;
	}
	if ($datum['tag'] > dt_letzterTagDesMonats($datum['monat'], $datum['jahr'])) {
		return false;
	}
	return true;
}

function dt_wochentag($datum) {
	$timestamp = strtotime($datum['jahr'] . '-' . $datum['monat'] . '-' . $datum['tag'] . ' 12:00:00');
	return date('N', $timestamp);
}

function dt_addiereTage($datum, $tage) {
	$datum['tag'] += $tage;
	while ($datum['tag'] <= 0) {
		$datum['monat']--;
		if ($datum['monat'] == 0) {
			$datum['monat'] = 12;
			$datum['jahr']--;
		}
		$datum['tag'] += dt_letzterTagDesMonats($datum['monat'], $datum['jahr']);
	}
	while (true) {
		$letzter = dt_letzterTagDesMonats($datum['monat'], $datum['jahr']);
		if ($datum['tag'] <= $letzter) {
			break;
		}
		$datum['tag'] -= $letzter;
		$datum['monat']++;
		if ($datum['monat'] == 13) {
			$datum['monat'] = 1;
			$datum['jahr']++;
		}
	}
	return $datum;
}

function dt_montagDerselbenWoche($datum) {
	return dt_addiereTage($datum, 1 - dt_wochentag($datum));
}

function dt_getWochentagNameFuerNummer($wochentagnummer) {
	if ($wochentagnummer < 1 || $wochentagnummer > 7) {
		die('ungültige Wochentagnummer: ' . $wochentagnummer);
	}
	$namen = array(
		'Montag',
		'Dienstag',
		'Mittwoch',
		'Donnerstag',
		'Freitag',
		'Samstag',
		'Sonntag',
	);
	return $namen[$wochentagnummer - 1];
}