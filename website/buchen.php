<?php

$including = true;
require_once('_konstanten.php');
require_once('_zeit.php');
require_once('_datum.php');
require_once('_querystring.php');
require_once('_form.php');
require_once('_responsive.php');
require_once('_datenbank.php');
require_once('_datenhaltung.php');
require_once('_authorization.php');

$sperre = dh_holeVariable('sperre');
$eingeloggt = au_checkCookie();
if ($sperre && !$eingeloggt) {
	header('Location: gesperrt.php', true, 302);
	die();
}

//
// Verarbeitung der Querystring-Parameter
//
$jahr = getQuerystringIntParameter('jahr', 2017, 2099);
$monat = getQuerystringIntParameter('monat', 1, 12);
$tag = getQuerystringIntParameter('tag', 1, 31);
$datum = array(
	'jahr' => $jahr,
	'monat' => $monat,
	'tag' => $tag,
);
if (!dt_datumValide($datum)) {
	die('ungültiges Datum: ' . $tag . '.' . $monat . '.' . $jahr);
}
$blocknummer = getQuerystringIntParameter('blocknummer', 0, ANZAHL_BLOCKS - 1);
$slotnummer = getQuerystringIntParameter('slotnummer', 0, getBlockAnzahlSlots($blocknummer) - 1);
$zurueckEinzeltag = !empty($_GET['zurueckInfo']);
$zurueckUrl = ($zurueckEinzeltag ? 'tag' : 'uebersicht') . '.php?jahr=' . $jahr . '&monat=' . $monat . '&tag=' . $tag;
if (!dh_istTagFreigeschaltet($jahr, $monat, $tag) && !$eingeloggt) {
	header('Location: ' . $zurueckUrl, true, 302);
	die();
}

//
// lesbare Zeiten berechnen
//
$blockStartzeit = getBlockStartzeit($blocknummer);
$slotStartzeit = zt_addiereMinuten($blockStartzeit, $slotnummer * SLOT_DAUER);
$slotEndezeit = zt_addiereMinuten($slotStartzeit, SLOT_DAUER);

//
// ggf. Formularverarbeitung
//
$formFields = array(
	'name' => '',
	'telefonnummer' => '',
	'zentner' => '',
);
if (handleForm()) {

	//
	// Validierung der Formularfelder
	//

	// Name
	if (empty($formFields['name'])) {
		$nameValide = false;
	} else {
		$name = trim($formFields['name']);
		if (strlen($name) < 3) {
			$nameValide = false;
		} else if (strpos($name, ' ') === false) {
			$nameValide = false;
		} else {
			$nameValide = true;
		}
	}
	if (!$nameValide) {
		$validationErrors['name'] = 'Bitte geben Sie hier Ihren Vor- und Nachnamen ein.';
	}

	// Telefonnummer
	if (empty($formFields['telefonnummer']) || empty(trim($formFields['telefonnummer']))) {
		$validationErrors['telefonnummer'] = 'Bitte geben Sie hier Ihre Telefonnummer ein.';
	} else {
		$telefonnummer = trim($formFields['telefonnummer']);
		$telefonnummer = strtr($telefonnummer, '/-()', '    ');
		$telefonnummer = str_replace(' ', '', $telefonnummer);
		if (!preg_match('/^[0-9]*$/', $telefonnummer)) {
			$telefonnummer = strtr($telefonnummer, '1234567890', '          ');
			$telefonnummer = str_replace(' ', '', $telefonnummer);
			$validationErrors['telefonnummer'] = 'Die Telefonnummer enthält ungültige Zeichen: ' . substr($telefonnummer, 0, 1);
		}
	}

	// Zentner Äpfel
	if (empty($formFields['zentner']) || empty(trim($formFields['zentner']))) {
		$validationErrors['zentner'] = 'Bitte geben Sie hier an, wieviele Zentner Äpfel gekeltert werden sollen.';
	} else {
		$zentnerText = trim($formFields['zentner']);
		$zentner = (float)$zentnerText;
		if ($zentnerText != (string)$zentner) {
			$validationErrors['zentner'] = 'Bitte geben Sie nur eine Zahl ein.';
		} else {
			$zentner = ceil($zentner);
			if ($zentner < 1) {
				$validationErrors['zentner'] = 'Bitte geben Sie eine positive Zahl ein.';
			}
		}
	}

	// weitere Verarbeitung
	if (empty($validationErrors)) {

		// die Anzahl an Slots für die Anzahl Zentner berechnen
		$anzahlSlots = round($zentner / 3);
		if ($anzahlSlots < 1) {
			$anzahlSlots = 1;
		}

		// Prüfen, ob die Termine schon belegt sind. Diese Prüfung ist nicht Teil der Transaktion, da uns das ohne ein definiertes 
		// Isolationslevel nicht helfen würde. Eine Parallele Buchung wird stattdessen über einen Unique Key abgefangen.
		$belegung = dh_holeBelegungBitmap($jahr, $monat, $tag);
		$slotsFuerDiesenBlock = $belegung[$blocknummer];
		if ($slotnummer + $anzahlSlots > count($slotsFuerDiesenBlock)) {
			if ($blocknummer == ANZAHL_BLOCKS - 1) {
				$validationErrors['zentner'] = 'Diese Menge ist zu groß für den späten Termin kurz vor Tagesende.';
			} else {
				$validationErrors['zentner'] = 'Die Zeit für diese Menge überschneidet sich mit der Pause.';
			}
		} else {
			for ($i = 0; $i < $anzahlSlots; $i++) {
				if ($slotsFuerDiesenBlock[$slotnummer + $i]['belegt']) {
					$validationErrors['zentner'] = 'Die Zeit für diese Menge überschneidet sich mit einem anderen Termin.';
				}
			}
			if (empty($validationErrors)) {
				$success = dh_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $anzahlSlots, $name, $telefonnummer, $zentner);
				if ($success) {
					header('Location: ' . $zurueckUrl, true, 302);
				} else {
					header('Location: schon-gebucht.php', true, 302);
				}
				die();
			}
		}

	}

}

//
// Darstellung
//
require('_intro.php');
?>
<h1>Termin Buchen: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?> <?= zt_zeitpunktText($slotStartzeit) ?> - <?= zt_zeitpunktText($slotEndezeit) ?></h1>

<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">

	<div><label for="name">Name</label></div>
	<?php printValidationError('name'); ?>
	<div><input class="form-control" type="text" name="name" value="<?= htmlspecialchars($formFields['name']) ?>"></div>
	<br>

	<div><label for="telefonnummer">Telefon</label></div>
	<?php printValidationError('telefonnummer'); ?>
	<div><input class="form-control" type="text" name="telefonnummer" value="<?= htmlspecialchars($formFields['telefonnummer']) ?>"></div>
	<br>

	<div><label for="zentner">Zentner Äpfel</label></div>
	<?php printValidationError('zentner'); ?>
	<div><input class="form-control" type="text" name="zentner" value="<?= htmlspecialchars($formFields['zentner']) ?>"></div>
	<br>
	
	<div><input class="btn btn-primary" type="submit" value="buchen"> oder <a href="<?= $zurueckUrl ?>">zurück</a></div>
</form>

<?php require('_outro.php');