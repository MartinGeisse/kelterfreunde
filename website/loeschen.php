<?php

$including = true;
require_once('_konstanten.php');
require_once('_zeit.php');
require_once('_datum.php');
require_once('_querystring.php');
require_once('_responsive.php');
require_once('_datenbank.php');
require_once('_datenhaltung.php');
require_once('_authorization.php');
require('_intro.php');

$eingeloggt = au_checkCookie();
if (!$eingeloggt) {
	die();
}

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
$zurueckUrl = ($zurueckEinzeltag ? 'tag' : 'uebersicht').'.php?jahr='.$jahr.'&monat='.$monat.'&tag='.$tag;
$belegungBlocks = dh_holeBelegungVollstaendig($datum['jahr'], $datum['monat'], $datum['tag']);
if (empty($belegungBlocks[$blocknummer][$slotnummer])) {
	header('Location: ' . $zurueckUrl, true, 302);
	die();
}
$buchung = $belegungBlocks[$blocknummer][$slotnummer];
$anzahlSlots = 1;
while (true) {
	if (empty($belegungBlocks[$blocknummer][$slotnummer + $anzahlSlots])) {
		break;
	}
	if (isset($belegungBlocks[$blocknummer][$slotnummer + $anzahlSlots]['name'])) {
		break;
	}
	$anzahlSlots++;
}
$blockStartzeit = getBlockStartzeit($blocknummer);
$startzeit = zt_addiereMinuten($blockStartzeit, $slotnummer * SLOT_DAUER);
$endezeit = zt_addiereMinuten($startzeit, $anzahlSlots * SLOT_DAUER);

// Darstellung
?>

<h1 class="headline">Buchung löschen</h1>
<br>
<div>Soll folgende Buchung wirklich gelöscht werden?</div>
<table class="eigenschaften-tabelle">
	<tr><td>Datum:</td><td><?= $datum['tag'] ?>.<?= $datum['monat'] ?>.<?= $datum['jahr'] ?></td></tr>
	<tr><td>Zeit:</td><td><?= zt_zeitpunktText($startzeit) ?> - <?= zt_zeitpunktText($endezeit) ?></td></tr>
	<tr><td>Name:</td><td><?= $buchung['name'] ?></td></tr>
	<tr><td>Telefon:</td><td><?= $buchung['telefonnummer'] ?></td></tr>
	<tr><td>Menge:</td><td><?= $buchung['zentner'] ?> Ztr.</td></tr>
</table>

<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
	<input class="btn btn-primary" type="submit" value="löschen"> oder <a href="<?= $zurueckUrl ?>">zurück</a>
</form>

<?php require('_outro.php');