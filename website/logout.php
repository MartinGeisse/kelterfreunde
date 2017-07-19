<?php

$including = true;

require_once('_db1u0p9_w3l6osc7.php');
require_once('_authorization.php');

au_clearCookie();
header('Location: uebersicht.php?' . $_SERVER['QUERY_STRING'], true, 302);
