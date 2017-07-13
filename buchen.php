<?php

$including = true;
require('_konstanten.php');
require('_zeit.php');
require('_datenbank.php');
require('_datenhaltung.php');


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
	global $fields, $validationErrors;

	// TODO

	if (empty($fields['name'])) {
		$validationErrors['name'] = 'Bitte geben Sie hier Ihren Namen ein.';
	}
}

function printValidationError($key) {
	global $validationErrors;
	if (!empty($validationErrors[$key])) {
		echo '<div style="color: red">', $validationErrors[$key], '</div>', "\n";
	}
}

?>

<form method="POST" action="buchen.php">
	<?php printValidationError('vonSlot'); ?>
	<div>von slot # <input type="text" name="von" value="<?= htmlspecialchars($fields['vonSlot']) ?>"></div>
	<?php printValidationError('bisSlot'); ?>
	<div>bis slot # <input type="text" name="bis" value="<?= htmlspecialchars($fields['bisSlot']) ?>"></div>
	<?php printValidationError('name'); ?>
	<div>Name <input type="text" name="name" value="<?= htmlspecialchars($fields['name']) ?>"></div>
	<div><input type="submit" value="buchen">
</form>
