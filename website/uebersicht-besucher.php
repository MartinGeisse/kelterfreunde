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
$belegung = dh_holeBelegungBitmap($jahr, $monat, $tag);
$buchenBasisUrl = 'buchen.php?jahr=' . $jahr . '&monat=' . $monat . '&tag=' . $tag;

?>
<h1>Keltertermine <?= $tag ?>.<?= $monat ?>.<?= $jahr ?></h1>

<?php foreach ($belegung as $blocknummer => $block): ?>
	<br />
	<?php foreach ($block as $slotnummer => $slot): ?>
		<?php $buchenUrl = $buchenBasisUrl . '&blocknummer=' . $blocknummer . '&slotnummer=' . $slotnummer; ?>
		<ul>
			<li>
				<?= zt_zeitpunktText($slot['zeit']) ?> - <?= zt_zeitpunktText(zt_addiereMinuten($slot['zeit'], SLOT_DAUER)) ?>:
				<?= $slot['belegt'] ? 'belegt' : '---' ?>
				<?php if (!$slot['belegt']): ?>&nbsp;&nbsp;&nbsp;<a href="buchen.php">buchen</a><?php endif; ?>
			</li>
		</ul>
	<?php endforeach; ?>
<?php endforeach; ?>

<?php require('_outro.php');