<?php

if (empty($including)) {
	die();
}

require_once('_db1u0p9_w3l6osc7.php');

$databaseConnection = new mysqli($databaseConfiguration['host'], $databaseConfiguration['user'], $databaseConfiguration['password'], $databaseConfiguration['database']);
if ($databaseConnection->connect_error) {
	die('Verbindung zur Datenbank fehlgeschlagen');
}

function db_beginTransaction() {
	global $databaseConnection;
	// $databaseConnection->begin();
	$databaseConnection->autocommit(false);
}

function db_commitTransaction() {
	global $databaseConnection;
	$databaseConnection->commit();
	$databaseConnection->autocommit(true);
}

function db_rollbackTransaction() {
	global $databaseConnection;
	$databaseConnection->rollback();
	$databaseConnection->autocommit(true);
}

function db_holeBuchungenFuerTag($jahr, $monat, $tag, $felder) {
	global $databaseConnection;
	$query = 'SELECT `' . implode('`, `', $felder) . '` FROM `buchungen` WHERE `jahr` = ? AND `monat` = ? AND `tag` = ?';
	$statement = $databaseConnection->prepare($query);
	if (!$statement) {
		die('Datenbankabfrage fehlgeschlagen (Schritt 1)');
	}
	$statement->bind_param('iii', $jahr, $monat, $tag);
	$statement->execute();
	if (!empty($statement->error_list)) {
		die('Datenbankabfrage fehlgeschlagen (Schritt 2)');
	}
	$resultRows = array();
	$currentRow = array();
	$currentRowIndexed = array();
	$metadata = $statement->result_metadata(); 
	while ($fieldMeta = $metadata->fetch_field()) { 
		$currentRowIndexed[] = &$currentRow[$fieldMeta->name]; 
	}
	call_user_func_array(array($statement, 'bind_result'), $currentRowIndexed); 
	while ($statement->fetch()) {
		$newRow = array();
		foreach ($currentRow as $key => $value) { 
			$newRow[$key] = $value;
		}
		array_push($resultRows, $newRow);
	} 
	$statement->close();
	return $resultRows;
}

function db_fuegeBuchungEin($jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer, $zentner) {
	global $databaseConnection;
	$query = 'INSERT INTO `buchungen` (`jahr`, `monat`, `tag`, `blocknummer`, `slotnummer`, `name`, `telefonnummer`, `zentner`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
	$statement = $databaseConnection->prepare($query);
	if (!$statement) {
		die('Datenbankänderung fehlgeschlagen (Schritt 1)');
	}
	$statement->bind_param('iiiiissi', $jahr, $monat, $tag, $blocknummer, $slotnummer, $name, $telefonnummer, $zentner);
	$statement->execute();
	$errors = $statement->error_list;
	$statement->close();
	if (count($errors) == 1 && $errors[0]['errno'] == 1062) {
		// duplicate entry
		return false;
	}
	if (!empty($errors)) {
		// other errors
		die('Datenbankänderung fehlgeschlagen (Schritt 2)');
	}
	return true;
}

function db_setzeVariable($name, $wert) {
	global $databaseConnection;
	$query = 'INSERT INTO `variablen` (`name`, `wert`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `wert` = ?';
	$statement = $databaseConnection->prepare($query);
	if (!$statement) {
		die('Datenbankänderung fehlgeschlagen (Schritt 1)');
	}
	$statement->bind_param('sss', $name, $wert, $wert);
	$statement->execute();
	$errors = $statement->error_list;
	$statement->close();
	if (!empty($errors)) {
		die('Datenbankänderung fehlgeschlagen (Schritt 2)');
	}
	return true;
}

function db_holeVariable($name) {
	global $databaseConnection;
	$query = 'SELECT `wert` FROM `variablen` WHERE `name` = ?';
	$statement = $databaseConnection->prepare($query);
	if (!$statement) {
		die('Datenbankabfrage fehlgeschlagen (Schritt 1)');
	}
	$statement->bind_param('s', $name);
	$statement->execute();
	if (!empty($statement->error_list)) {
		die('Datenbankabfrage fehlgeschlagen (Schritt 2)');
	}
	$wert = null;
	$statement->bind_result($wert);
	$statement->fetch();
	$statement->close();
	return (string)$wert;
}
