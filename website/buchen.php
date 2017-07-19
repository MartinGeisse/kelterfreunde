<?php

$including = true;
require_once('_konstanten.php');
require_once('_zeit.php');
require_once('_datum.php');
require_once('_querystring.php');
require_once('_responsive.php');
require_once('_datenbank.php');
require_once('_datenhaltung.php');
require('_intro.php');

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

//
// lesbare Zeiten berechnen
//
$blockStartzeit = getBlockStartzeit($blocknummer);
$slotStartzeit = zt_addiereMinuten($blockStartzeit, $slotnummer * SLOT_DAUER);
$slotEndezeit = zt_addiereMinuten($slotStartzeit, SLOT_DAUER);

//
// ggf. Formularverarbeitung
//
$validationErrors = array();
$fields = array(
	'name' => '',
	'telefonnummer' => '',
);
$validationErrors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// einlesen der Formularfelder
	//
	foreach ($_POST as $key => $value) {
		if (array_key_exists($key, $fields)) {
			$value = trim($value);
			if (!empty($value)) {
				$fields[$key] = $value;
			}
		}
	}

	//
	// Validierung der Formularfelder
	//
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// Name
		if (empty($fields['name'])) {
			$nameValide = false;
		} else {
			$name = trim($fields['name']);
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
		if (empty($fields['telefonnummer']) || empty(trim($fields['telefonnummer']))) {
			$validationErrors['telefonnummer'] = 'Bitte geben Sie hier Ihre Telefonnummer ein.';
		}
		$telefonnummer = trim($fields['telefonnummer']);
		$telefonnummer = strtr($telefonnummer, '/-()', '    ');
		$telefonnummer = str_replace(' ', '', $telefonnummer);
		if (!preg_match('/^[0-9]*$/', $telefonnummer)) {
			$telefonnummer = strtr($telefonnummer, '1234567890', '          ');
			$telefonnummer = str_replace(' ', '', $telefonnummer);
			$validationErrors['telefonnummer'] = 'Die Telefonnummer enthält ungültige Zeichen: ' . substr($telefonnummer, 0, 1);
		}

		// weitere Verarbeitung
		if (empty($validationErrors)) {
			$success = db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer);
			if ($success) {
				header('Location: uebersicht-besucher.php?jahr='.$jahr.'&monat='.$monat.'&tag='.$tag, true, 302);
			} else {
				header('Location: schon-gebucht.php', true, 302);
			}
		}
	}

}

//
// Hilfsfunktionen zur Darstellung
//
function printValidationError($key) {
	global $validationErrors;
	if (!empty($validationErrors[$key])) {
		echo '<div class="feedback-message alert alert-danger">', $validationErrors[$key], '</div>', "\n";
	}
}

//
// Darstellung
//
?>
<h1>Termin Buchen: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?> <?= zt_zeitpunktText($slotStartzeit) ?> - <?= zt_zeitpunktText($slotEndezeit) ?></h1>

<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
<?php /*
	<?php printValidationError('vonSlot'); ?>
	<div>von slot # <input type="text" name="von" value="<?= htmlspecialchars($fields['vonSlot']) ?>"></div>
	<?php printValidationError('bisSlot'); ?>
	<div>bis slot # <input type="text" name="bis" value="<?= htmlspecialchars($fields['bisSlot']) ?>"></div>
*/ ?>

	<div><label for="name">Name</label></div>
	<?php printValidationError('name'); ?>
	<div><input class="form-control" type="text" name="name" value="<?= htmlspecialchars($fields['name']) ?>"></div>
	<br>

	<div><label for="telefonnummer">Telefon</label></div>
	<?php printValidationError('telefonnummer'); ?>
	<div><input class="form-control" type="text" name="telefonnummer" value="<?= htmlspecialchars($fields['telefonnummer']) ?>"></div>
	<br>
	
	<div><input class="btn btn-primary" type="submit" value="buchen"> oder <a href="uebersicht-besucher.php">zurück</a></div>
</form>

<?php require('_outro.php');