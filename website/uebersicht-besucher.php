<?php

$including = true;
require_once('_konstanten.php');
require_once('_zeit.php');
require_once('_datum.php');
require_once('_querystring.php');
require_once('_datenbank.php');
require_once('_datenhaltung.php');
require('_intro.php');

$montag = getQuerystringMontag(true);
$sonntag = dt_addiereTage($montag, 6);

?>
<h1>Keltertermine <?= $montag['tag'] ?>.<?= $montag['monat'] ?>.<?= $montag['jahr'] ?> - <?= $sonntag['tag'] ?>.<?= $sonntag['monat'] ?>.<?= $sonntag['jahr'] ?></h1>

<?php $datum = $montag; ?>
<?php for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++): ?>
	<?php $belegung = dh_holeBelegungBitmap($datum['jahr'], $datum['monat'], $datum['tag']); ?>
	<?php $buchenBasisUrl = 'buchen.php?jahr=' . $datum['jahr'] . '&monat=' . $datum['monat'] . '&tag=' . $datum['tag']; ?>
	<h2><?= dt_getWochentagNameFuerNummer($wochentagnummer) ?>, <?= $datum['tag'] ?>.<?= $datum['monat'] ?>.<?= $datum['jahr'] ?></h2>
	<?php foreach ($belegung as $blocknummer => $block): ?>
		<br />
		<?php foreach ($block as $slotnummer => $slot): ?>
			<?php $buchenUrl = $buchenBasisUrl . '&blocknummer=' . $blocknummer . '&slotnummer=' . $slotnummer; ?>
			<ul>
				<li>
					<?= zt_zeitpunktText($slot['zeit']) ?> - <?= zt_zeitpunktText(zt_addiereMinuten($slot['zeit'], SLOT_DAUER)) ?>:
					<?= $slot['belegt'] ? 'belegt' : '---' ?>
					<?php if (!$slot['belegt']): ?>&nbsp;&nbsp;&nbsp;<a href="<?= $buchenUrl ?>">buchen</a><?php endif; ?>
				</li>
			</ul>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<?php $datum = dt_addiereTage($datum, 1); ?>
<?php endfor; ?>

<?php require('_outro.php');