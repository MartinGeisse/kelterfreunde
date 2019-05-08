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
$anzahlSlots = getQuerystringIntParameter('anzahlSlots', 1);
$zentner = getQuerystringIntParameter('zentner', 1);
$obstsorte = $_GET['obstsorte'];
$zurueckEinzeltag = !empty($_GET['zurueckInfo']);
$zurueckUrl = ($zurueckEinzeltag ? 'tag' : 'uebersicht') . '.php?jahr=' . $jahr . '&monat=' . $monat . '&tag=' . $tag;

//
// lesbare Zeiten berechnen
//
$blockStartzeit = getBlockStartzeit($blocknummer);
$buchungStartzeit = zt_addiereMinuten($blockStartzeit, $slotnummer * SLOT_DAUER);
$buchungEndezeit = zt_addiereMinuten($buchungStartzeit, $anzahlSlots * SLOT_DAUER);


//
// Darstellung
//
require('_intro.php');
?>
<h1>Ihr Termin wurde verbindlich gebucht</h1>

<div>Datum: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?></div>
<div>Uhrzeit: <?= zt_zeitpunktText($buchungStartzeit) ?> - <?= zt_zeitpunktText($buchungEndezeit) ?></div>
<div>Menge: <?= ($zentner * 50) ?>kg (<?= $zentner ?> Zentner)</div>
<div>Obstsorte: <?= getObstsortenName($obstsorte) ?></div>
<br>
<div><a href="<?= $zurueckUrl ?>" class="btn btn-primary"> Zurück zur Übersicht</a></div>

<?php require('_outro.php');