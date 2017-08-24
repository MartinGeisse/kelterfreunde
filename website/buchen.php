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
	}
	$telefonnummer = trim($formFields['telefonnummer']);
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
			header('Location: '.($zurueckEinzeltag ? 'tag' : 'uebersicht').'.php?jahr='.$jahr.'&monat='.$monat.'&tag='.$tag, true, 302);
		} else {
			header('Location: schon-gebucht.php', true, 302);
		}
		die();
	}

}

//
// Darstellung
//
require('_intro.php');
?>
<h1>Termin Buchen: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?> <?= zt_zeitpunktText($slotStartzeit) ?> - <?= zt_zeitpunktText($slotEndezeit) ?></h1>

<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
<?php /*
	<?php printValidationError('vonSlot'); ?>
	<div>von slot # <input type="text" name="von" value="<?= htmlspecialchars($formFields['vonSlot']) ?>"></div>
	<?php printValidationError('bisSlot'); ?>
	<div>bis slot # <input type="text" name="bis" value="<?= htmlspecialchars($formFields['bisSlot']) ?>"></div>
*/ ?>

	<div><label for="name">Name</label></div>
	<?php printValidationError('name'); ?>
	<div><input class="form-control" type="text" name="name" value="<?= htmlspecialchars($formFields['name']) ?>"></div>
	<br>

	<div><label for="telefonnummer">Telefon</label></div>
	<?php printValidationError('telefonnummer'); ?>
	<div><input class="form-control" type="text" name="telefonnummer" value="<?= htmlspecialchars($formFields['telefonnummer']) ?>"></div>
	<br>
	
	<div><input class="btn btn-primary" type="submit" value="buchen"> oder <a href="<?= ($zurueckEinzeltag ? 'tag' : 'uebersicht') ?>.php?jahr=<?= $jahr ?>&monat=<?= $monat ?>&tag=<?= $tag ?>">zurück</a></div>
</form>

<?php require('_outro.php');