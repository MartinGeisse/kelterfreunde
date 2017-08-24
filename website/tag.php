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

$datum = getQuerystringDatum(true);
$belegungBlocks = dh_holeBelegungVollstaendig($datum['jahr'], $datum['monat'], $datum['tag']);

// Darstellung
?>

<h1 class="headline">
	Keltertermine <?= $datum['tag'] ?>.<?= $datum['monat'] ?>.<?= $datum['jahr'] ?>
</h1>
<br>
<div class="hidden-print">
	<a href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"><span class="glyphicon glyphicon-chevron-left"></span> zurück zur Woche</a>
	<br>
</div>
<br><br>

<table class="table table-striped" style="width: auto">
	<?php for ($blocknummer = 0; $blocknummer < ANZAHL_BLOCKS; $blocknummer++): ?>
		<?php $anzahlSlots = getBlockAnzahlSlots($blocknummer); ?>
		<?php for ($slotnummer = 0; $slotnummer < $anzahlSlots; $slotnummer++): ?>
			<tr>
				<?php
					$slot = $belegungBlocks[$blocknummer][$slotnummer];
					$buchenUrl = 'buchen.php?jahr=' . $datum['jahr'] . '&monat=' . $datum['monat'] . '&tag=' . $datum['tag'] . '&blocknummer=' . $blocknummer . '&slotnummer=' . $slotnummer . '&zurueckInfo=1';
					echo '<td>', zt_zeitpunktText($slot['zeit']), '</td>';
					if ($slot['belegt']) {
						echo '<td class="belegt">';
						echo '<span class="print-line">', $slot['name'], '</span>';
						echo '<br class="hidden-print">';
						echo '<span class="print-line">', $slot['telefonnummer'], '</span>';
						echo '</td>', "\n";
					} else {
						echo '<td class="frei">';
						echo '<a href="', $buchenUrl, '" class="hidden-print">---</a><br class="hidden-print"><span class="hidden-print">&nbsp;</span>';
						echo '<span class="visible-print-block">&nbsp;</span>';
						echo '<span class="visible-print-block">&nbsp;</span>';
						echo '</td>', "\n";
					}
				?>
			</tr>
		<?php endfor; ?>
	<?php endfor; ?>
</table>

<?php require('_outro.php');