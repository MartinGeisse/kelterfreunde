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
require('_intro.php');

$montag = getQuerystringMontag(true);
$sonntag = dt_addiereTage($montag, 6);
$eingeloggt = au_checkCookie();

// Sperrfunktion
if ($eingeloggt) {
	$formFields = array('sperre' => null);
	if (handleForm()) {
		if (isset($formFields['sperre'])) {
			dh_setzeVariable('sperre', !empty($formFields['sperre']));
			header('Location: uebersicht.php?jahr='.$montag['jahr'].'&monat='.$montag['monat'].'&tag='.$montag['tag'], true, 302);
			die();
		}
	}
}
$sperre = dh_holeVariable('sperre');

// Daten laden
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

<h1 class="headline">
	<div style="float: left;">
		Keltertermine
	</div>
	<div class="hidden-print" style="float: right">
		<span style="font-size: smaller">
			<?php $datum = dt_addiereTage($montag, -7); ?>
			<a class="glyphicon glyphicon-chevron-left" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
			<?php $datum = dt_addiereTage($montag, 7); ?>
			<a class="glyphicon glyphicon-chevron-right" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
		</span>
	</div>
	<div style="text-align: center">
		<?= $montag['tag'] ?>.<?= $montag['monat'] ?>.<?= $montag['jahr'] ?> - <?= $sonntag['tag'] ?>.<?= $sonntag['monat'] ?>.<?= $sonntag['jahr'] ?>
	</div>
</h1>
<br>
<br class="visible-print-block">
<br class="visible-print-block">
<br class="visible-print-block">
<br class="visible-print-block">
<?php if ($eingeloggt): ?>
	<div class="alert <?= $sperre ? 'alert-danger' : 'alert-success' ?>">
		<form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>" style="float: right">
			<input type="hidden" name="sperre" value="<?= (int)!$sperre ?>">
			<input type="submit" value="<?= $sperre ? 'Jetzt entsperren' : 'Jetzt sperren' ?>">
		</form>
		<span class="glyphicon glyphicon-ok-circle"></span>
		Buchungen durch Besucher sind <?= $sperre ? '' : 'nicht' ?> gesperrt.
	</div>
<?php endif; ?>

<div style="position: relative">
	<?php if ($sperre && !$eingeloggt): ?>
		<div id="sperre-warnfenster" style="position: absolute; top: 50px; bottom: 50px; left: 20px; right: 20px; border: 1px solid #aaa; background-color: #eee; padding: 20px; text-align: center">
			<div style="margin-bottom: 20px; "><?= GESPERRT_TEXT ?></div>
			<a href="#" class="btn btn-primary btn-block" onclick="$('#sperre-warnfenster').hide(); return false;">ausblenden</a>
		</div>
	<?php endif; ?>
	<table class="table table-striped termintabelle">
		<tr>
			<th></th>
			<?php
				$datum = $montag;
				for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++) {
					echo '<th>';
					if ($eingeloggt) {
						echo '<a href="tag.php?jahr=', $datum['jahr'], '&monat=', $datum['monat'], '&tag=', $datum['tag'], '">';
					}
					echo dt_getWochentagAbkuerzungFuerNummer($wochentagnummer), '&nbsp;', $datum['tag'], '.', $datum['monat'];
					if ($eingeloggt) {
						echo '</a>';
					}
					echo '</th>', "\n";
					$datum = dt_addiereTage($datum, 1);
				}
			?>
		</tr>
		<?php for ($blocknummer = 0; $blocknummer < ANZAHL_BLOCKS; $blocknummer++): ?>
			<?php
				if ($blocknummer > 0) {
					echo '<tr style="background-color: #aaa"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>', "\n";
				}
			?>
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
							if ($slot['belegt']) {
								echo '<td class="belegt">';
								if ($eingeloggt) {
									if ($slot['name'] === null) {
										echo '<div>&nbsp;</div>';
										echo '<div>&nbsp;&nbsp;&nbsp;| |</div>';
										echo '<div>&nbsp;</div>';
									} else {
										echo '<div>', $slot['name'], '</div>';
										echo '<div>', $slot['telefonnummer'], '</div>';
										echo '<div>', $slot['zentner'], ' Ztr.</div>';
									}
								} else {
									echo 'belegt';
								}
								echo '</td>', "\n";
							} else {
								echo '<td class="frei">';
								if ($eingeloggt) {
									echo '<a href="', $buchenUrl, '" class="hidden-print">---</a>';
									echo '<br class="hidden-print"><span class="hidden-print">&nbsp;</span>';
									echo '<br class="hidden-print"><span class="hidden-print">&nbsp;</span>';
									echo '<span class="visible-print-block">&nbsp;</span>';
									echo '<span class="visible-print-block">&nbsp;</span>';
								} else if ($sperre) {
									echo '&nbsp;';
								} else {
									echo '<a href="', $buchenUrl, '" class="hidden-print">buchen</a>';
								}
								echo '</td>', "\n";
							}
							$datum = dt_addiereTage($datum, 1);
						}
					?>
				</tr>
			<?php endfor; ?>
		<?php endfor; ?>
	</table>
</div>

<div class="hidden-print">
	<?php if ($eingeloggt): ?>
		<a href="logout.php?<?= $_SERVER['QUERY_STRING'] ?>">logout</a><br>
	<?php else: ?>
		<a href="login.php?<?= $_SERVER['QUERY_STRING'] ?>">login</a><br>
	<?php endif; ?>
</div>

<?php require('_outro.php');