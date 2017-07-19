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

?>
<h1>
	Keltertermine <?= $montag['tag'] ?>.<?= $montag['monat'] ?>.<?= $montag['jahr'] ?> - <?= $sonntag['tag'] ?>.<?= $sonntag['monat'] ?>.<?= $sonntag['jahr'] ?>
	&emsp;
	<span style="font-size: smaller">
		<?php $datum = dt_addiereTage($montag, -7); ?>
		<a class="glyphicon glyphicon-chevron-left" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
		<?php $datum = dt_addiereTage($montag, 7); ?>
		<a class="glyphicon glyphicon-chevron-right" href="uebersicht.php?jahr=<?= $datum['jahr'] ?>&monat=<?= $datum['monat'] ?>&tag=<?= $datum['tag'] ?>"></a>
	</span>
</h1>

<?php $datum = $montag; ?>
<?php for ($wochentagnummer = 1; $wochentagnummer <= 7; $wochentagnummer++): ?>
	<?php
		$eingeloggt = au_checkCookie();
		if ($eingeloggt) {
			$belegung = dh_holeBelegungVollstaendig($datum['jahr'], $datum['monat'], $datum['tag']);
		} else {
			$belegung = dh_holeBelegungBitmap($datum['jahr'], $datum['monat'], $datum['tag']);
		}
		$buchenBasisUrl = 'buchen.php?jahr=' . $datum['jahr'] . '&monat=' . $datum['monat'] . '&tag=' . $datum['tag'];
	?>
	<h2><?= dt_getWochentagNameFuerNummer($wochentagnummer) ?>, <?= $datum['tag'] ?>.<?= $datum['monat'] ?>.<?= $datum['jahr'] ?></h2>
	<?php foreach ($belegung as $blocknummer => $block): ?>
		<br />
		<?php foreach ($block as $slotnummer => $slot): ?>
			<?php $buchenUrl = $buchenBasisUrl . '&blocknummer=' . $blocknummer . '&slotnummer=' . $slotnummer; ?>
			<ul>
				<li>
					<?= zt_zeitpunktText($slot['zeit']) ?> - <?= zt_zeitpunktText(zt_addiereMinuten($slot['zeit'], SLOT_DAUER)) ?>:
					<?php
						if ($slot['belegt']) {
							if ($eingeloggt) {
								echo $slot['name'] . ', ' . $slot['telefonnummer'];
							} else {
								echo 'belegt';
							}
						} else {
							echo '---';
							if (!$eingeloggt) {
								echo '&nbsp;&nbsp;&nbsp;<a href="<?= $buchenUrl ?>">buchen</a>';
							}
						}
					?>
				</li>
			</ul>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<?php $datum = dt_addiereTage($datum, 1); ?>
<?php endfor; ?>

<a href="login.php?<?= $_SERVER['QUERY_STRING'] ?>">login</a><br>
<a href="logout.php?<?= $_SERVER['QUERY_STRING'] ?>">logout</a><br>

<?php require('_outro.php');