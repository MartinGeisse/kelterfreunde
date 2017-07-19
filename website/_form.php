<?php

if (empty($including)) {
	die();
}

$validationErrors = array();

function handleForm() {
	global $formFields;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		foreach ($_POST as $key => $value) {
			if (array_key_exists($key, $formFields)) {
				$value = trim($value);
				if (!empty($value)) {
					$formFields[$key] = $value;
				}
			}
		}
		return true;
	} else {
		return false;
	}
}

//
// Hilfsfunktionen zur Darstellung
//
function printValidationError($key) {
	global $validationErrors;
	if (!empty($validationErrors[$key])) {
		echo '<div class="feedback-message alert alert-danger">', $validationErrors[$key], '</div>', "\n";
	}
}
