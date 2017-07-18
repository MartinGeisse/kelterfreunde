<?php

$including = true;
require_once('_konstanten.php');
require_once('_zeit.php');
require_once('_datenbank.php');
require_once('_datenhaltung.php');
require('_intro.php');

$jahr = 2017;
$monat = 2;
$tag = 23;
$belegung = dh_holeBelegungVollstaendig($jahr, $monat, $tag);

?>
<h1>Keltertermine <?= $tag ?>.<?= $monat ?>.<?= $jahr ?></h1>

<?php foreach ($belegung as $block): ?>
	<br />
	<?php foreach ($block as $slot): ?>
		<ul>
			<li>
                <?= zt_zeitpunktText($slot['zeit']) ?> - <?= zt_zeitpunktText(zt_addiereMinuten($slot['zeit'], SLOT_DAUER)) ?>:
                <?php if ($slot['belegt']): ?>
                    <?= $slot['name'] ?>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </li>
		</ul>
	<?php endforeach; ?>
<?php endforeach; ?>

<?php require('_outro.php');