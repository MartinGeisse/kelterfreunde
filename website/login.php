<?php

$including = true;

require_once('_db1u0p9_w3l6osc7.php');
require_once('_form.php');
require_once('_authorization.php');

$formFields = array('passwort' => '');
if (handleForm()) {

	if (empty($formFields['passwort'])) {
		$validationErrors['passwort'] = 'Bitte geben Sie hier das Passwort ein.';
	} else if ($formFields['passwort'] != $loginConfiguration['passwort']) {
		$validationErrors['passwort'] = 'Das Passwort ist falsch.';
	} else {
		au_sendCookie();
		header('Location: uebersicht.php?' . $_SERVER['QUERY_STRING'], true, 302);
		die();
	}
	
}

require('_intro.php');
?>
<br>
<div class="alert alert-warning" role="alert">
	<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
	Achtung: Dieser Login-Bereich funktioniert aus technischen Gründen nur dann, wenn der Link auf dieses Loginformular
	in einem neuen Tab oder Fenster geöffnet wird. Er funktioniert nicht, wenn das Formular innerhalb der Kelterfreunde-Seite
	geöffnet wird.
</div>
<br>
<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">

	<div><label for="passwort">Passwort</label></div>
	<?php printValidationError('passwort'); ?>
	<div><input class="form-control" type="password" name="passwort"></div>
	<br>

	<div><input class="btn btn-primary" type="submit" value="einloggen"> oder <a href="uebersicht.php?<?= $_SERVER['QUERY_STRING'] ?>">zurück</a></div>
</form>

<?php require('_outro.php');