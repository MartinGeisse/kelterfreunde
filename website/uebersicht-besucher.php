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

// TODO
$jahr = $montag['jahr'];
$monat = $montag['monat'];
$tag = $montag['tag'];

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
				<?php if (!$slot['belegt']): ?>&nbsp;&nbsp;&nbsp;<a href="<?= $buchenUrl ?>">buchen</a><?php endif; ?>
			</li>
		</ul>
	<?php endforeach; ?>
<?php endforeach; ?>

<?php require('_outro.php');