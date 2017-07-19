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
	if (!empty($statement->error_list)) {
		die('Datenbankabfrage fehlgeschlagen');
	}
	$resultSet = $statement->get_result();
	$resultRows = array();
	while ($row = $resultSet->fetch_array(MYSQLI_ASSOC)) {
		array_push($resultRows, $row);
	}
	$statement->close();
	return $resultRows;
}

function db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer) {
	global $databaseConnection;
	$query = 'INSERT INTO `buchungen` (`jahr`, `monat`, `tag`, `blocknummer`, `slotnummer`, `name`, `telefonnummer`) VALUES (?, ?, ?, ?, ?, ?, ?)';
	$statement = $databaseConnection->prepare($query);
	$statement->bind_param('iiiiiss', $jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer);
	$statement->execute();
	$errors = $statement->error_list;
	$statement->close();
	if (count($errors) == 1 && $errors[0]['errno'] == 1062) {
		// duplicate entry
		return false;
	}
	if (!empty($errors)) {
		// other errors
		die('Datenbankänderung fehlgeschlagen');
	}
	return true;
}
