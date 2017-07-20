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

$montag = getQuerystringMontag(true);
$sonntag = dt_addiereTage($montag, 6);

// TODO alle Tage auf einmal laden -- schneller
$eingeloggt = au_checkCookie();
$belegungTage = array();
$datum = $montag;
for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++) {
	if ($eingeloggt) {
		$belegungBlocks = dh_holeBelegungVollstaendig($datum['jahr'], $datum['monat'], $datum['tag']);
	} else {
		$belegungBlocks = dh_holeBelegungBitmap($datum['jahr'], $datum['monat'], $datum['tag']);
	}
	array_push($belegungTage, $belegungBlocks);
	$datum = dt_addiereTage($datum, 1);
}

// Darstellung
?>

<h1>
	Keltertermine <?= $montag['tag'] ?>.<?= $montag['monat'] ?>.<?= $montag['jahr'] ?> - <?= $sonntag['tag'] ?>.<?= $sonntag['monat'] ?>.<?= $sonntag['jahr'] ?>
	<span class="hidden-print">
		&emsp;
		<span style="font-size: smaller">
			<?php $datum = dt_addiereTage($montag, -7); ?>
			<a class="glyphicon glyphicon-chevron-left" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
			<?php $datum = dt_addiereTage($montag, 7); ?>
			<a class="glyphicon glyphicon-chevron-right" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
		</span>
	</span>
</h1>

<table class="table table-striped">
	<tr>
		<th></th>
		<?php
			$datum = $montag;
			for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++) {
				$datum = dt_addiereTage($datum, 1);
				echo '<th>', dt_getWochentagAbkuerzungFuerNummer($wochentagnummer), ' ', $datum['tag'], '.', $datum['monat'], '</th>', "\n";
			}
		?>
	</tr>
	<?php for ($blocknummer = 0; $blocknummer < ANZAHL_BLOCKS; $blocknummer++): ?>
		<?php $anzahlSlots = getBlockAnzahlSlots($blocknummer); ?>
		<?php for ($slotnummer = 0; $slotnummer < $anzahlSlots; $slotnummer++): ?>
			<tr>
				<?php
					$datum = $montag;
					for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++) {
						$slot = $belegungTage[$wochentagnummer - 1][$blocknummer][$slotnummer];
						$buchenUrl = 'buchen.php?jahr=' . $datum['jahr'] . '&monat=' . $datum['monat'] . '&tag=' . $datum['tag'] . '&blocknummer=' . $blocknummer . '&slotnummer=' . $slotnummer;
						if ($wochentagnummer == 1) {
							// echo '<td>', zt_zeitpunktText($slot['zeit']), ' - ', zt_zeitpunktText(zt_addiereMinuten($slot['zeit'], SLOT_DAUER)), '</td>', "\n";
							echo '<td>', zt_zeitpunktText($slot['zeit']), '</td>', "\n";
						}
						echo '<td>';
						if ($slot['belegt']) {
							if ($eingeloggt) {
								echo '<span class="print-line">', $slot['name'], '</span>';
								echo '<span class="hidden-print">, </span>';
								echo '<span class="print-line">', $slot['telefonnummer'], '</span>';
							} else {
								echo 'belegt';
							}
						} else {
							if ($eingeloggt) {
								echo '<span class="hidden-print">---</span>';
								echo '<span class="visible-print-block">&nbsp;</span>';
								echo '<span class="visible-print-block">&nbsp;</span>';
							} else {
								echo '<a href="', $buchenUrl, '" class="hidden-print">buchen</a>';
							}
						}
						echo '</td>', "\n";
						$datum = dt_addiereTage($datum, 1);
					}
				?>
			</tr>
		<?php endfor; ?>
	<?php endfor; ?>
</table>

<div class="hidden-print">
	<?php if ($eingeloggt): ?>
		<a href="logout.php?<?= $_SERVER['QUERY_STRING'] ?>">logout</a><br>
	<?php else: ?>
		<a href="login.php?<?= $_SERVER['QUERY_STRING'] ?>">login</a><br>
	<?php endif; ?>
</div>

<?php require('_outro.php');