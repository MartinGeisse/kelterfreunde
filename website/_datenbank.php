<?php

if (empty($including)) {
	die();
}

require_once('_db1u0p9_w3l6osc7.php');

$databaseConnection = new mysqli($databaseConfiguration['host'], $databaseConfiguration['user'], $databaseConfiguration['password'], $databaseConfiguration['database']);
if ($databaseConnection->connect_error) {
	die('Verbindung zur Datenbank fehlgeschlagen');
}

function db_holeBuchungenFuerTag($jahr, $monat, $tag, $felder) {
	global $databaseConnection;
	$query = 'SELECT `' . implode('`, `', $felder) . '` FROM `buchungen` WHERE `jahr` = ? AND `monat` = ? AND `tag` = ?';
	$statement = $databaseConnection->prepare($query);
	$statement->bind_param('iii', $jahr, $monat, $tag);
	$statement->execute();
	$resultSet = $statement->get_result();
	$resultRows = array();
	while ($row = $resultSet->fetch_array(MYSQLI_ASSOC)) {
		array_push($resultRows, $row);
	}
	$statement->close();
	return $resultRows;
}
