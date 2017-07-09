<?php

if (empty($including)) {
	die();
}

function zt_addiereMinuten($zeitpunkt, $minuten) {
	$zeitpunkt[1] += $minuten;
	while ($zeitpunkt[1] >= 60) {
		$zeitpunkt[0]++;
		$zeitpunkt[1] -= 60;
	}
	return $zeitpunkt;
}

function zt_zeitpunktText($zeitpunkt) {
	$result = '';
	if ($zeitpunkt[0] < 10) {
		$result .= '0';
	}
	$result .= $zeitpunkt[0];
	$result .= ':';
	if ($zeitpunkt[1] < 10) {
		$result .= '0';
	}
	$result .= $zeitpunkt[1];
	return $result;
}
