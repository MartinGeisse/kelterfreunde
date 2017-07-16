<?php

$including = true;
require('_konstanten.php');
require('_zeit.php');
require('_datenbank.php');
require('_datenhaltung.php');
require('_intro.php');

$jahr = 2017;
$monat = 2;
$tag = 23;
$blockNummer = 0;
$slotNummer = 3;

$blockStartzeit = getBlockStartzeit($blockNummer);
$slotStartzeit = zt_addiereMinuten($blockStartzeit, $slotNummer * SLOT_DAUER);
$slotEndezeit = zt_addiereMinuten($slotStartzeit, SLOT_DAUER);

$fields = array(
	'vonSlot' => '',
	'bisSlot' => '',
	'name' => '',
);
foreach ($_REQUEST as $key => $value) {
	if (array_key_exists($key, $fields)) {
		$fields[$key] = $value;
	}
}

$validationErrors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// TODO

	if (empty($fields['name'])) {
		$validationErrors['name'] = 'Bitte geben Sie hier Ihren Namen ein.';
	}

	if (empty($validationErrors)) {
		// TODO buchen
		header('Location: uebersicht-besucher.php', true, 302);
	}
}

function printValidationError($key) {
	global $validationErrors;
	if (!empty($validationErrors[$key])) {
		echo '<div class="feedback-message alert alert-danger">', $validationErrors[$key], '</div>', "\n";
	}
}

?>
<h1>Termin Buchen: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?> <?= zt_zeitpunktText($slotStartzeit) ?> - <?= zt_zeitpunktText($slotEndezeit) ?></h1>

<form class="form" method="POST" action="buchen.php">
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
	<div><input class="btn btn-primary" type="submit" value="buchen"> oder <a href="uebersicht-besucher.php">zurück</a></div>
</form>

<?php require('_outro.php');