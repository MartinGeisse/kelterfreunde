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

function dt_datumValide($tag, $monat, $jahr) {
	if ($tag < 1 || $monat < 1 || $jahr < 2000) {
		return false;
	}
	if ($monat > 12 || $jahr > 2099) {
		return false;
	}
	if ($tag > dt_letzterTagDesMonats($monat, $jahr)) {
		return false;
	}
	return true;
}
