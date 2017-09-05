<?php

$including = true;
require_once('_konstanten.php');
require('_intro.php');

?>
<h1>Buchungen gesperrt!</h1>
<p><?= GESPERRT_TEXT ?></p>
<div><a class="btn btn-primary" href="uebersicht.php">ok</a></div>
<?php require('_outro.php');