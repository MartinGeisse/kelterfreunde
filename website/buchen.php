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

?>
<h1>Termin Buchen: <?= $tag ?>.<?= $monat ?>.<?= $jahr ?> <?= zt_zeitpunktText($slotStartzeit) ?> - <?= zt_zeitpunktText($slotEndezeit) ?></h1>

<form method="POST" action="buchen.php">
	<div>Name: <input type="text" name="name"></div>
	<br>
	<div><input type="submit" value="buchen"> oder <a href="uebersicht-besucher.php">zurück</a></div>
</form>

<?php require('_outro.php');