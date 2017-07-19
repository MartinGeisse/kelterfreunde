<?php

if (empty($including)) {
	die();
}

/**
 * These functions generate a CSS class attribute value for visibility.
 *
 * The $kind should be 'block', 'inline', 'inline-block' or null (hide) that determines what happens
 * when the column conditions are met.
 *
 * The $columnConfiguration should be an associative array that maps the screen size names (xs, sm, md, lg) to
 * the number of columns for that screen size. The $columnIndex is the index of the current column, 0-based.
 *
 * Note that the notion of a "last" column does not take a potential semi-filled last row into consideration.
 * That is, the last filled column of the last row is only treated as the last column by these methods of
 * that row is "full". If needed, fill up the last row with empty cells.
 */

$rs_screenSizeNames = array('xs', 'sm', 'md', 'lg');

function _rs_columnHelper($kind, $columnConfiguration, $columnIndex, $matchFirst, $matchOnEqual) {
	global $rs_screenSizeNames;
	$result = '';
	foreach ($rs_screenSizeNames as $screenSizeName) {
		$columnCount = $columnConfiguration[$screenSizeName];
		$relativeColumnIndex = $columnIndex % $columnCount;
		$equal = ($relativeColumnIndex == ($matchFirst ? 0 : $columnCount - 1));
		if ($equal == $matchOnEqual) {
			$result .= ' ' . ($kind === null ? 'hidden' : 'visible') . '-' . $screenSizeName;
			if ($kind !== null) {
				$result .= '-' . $kind;
			}
		}
	}
	return $result;
}

function rs_forFirstColumn($kind, $columnConfiguration, $columnIndex) {
	return _rs_columnHelper($kind, $columnConfiguration, $columnIndex, true, true);
}

function rs_forNonFirstColumn($kind, $columnConfiguration, $columnIndex) {
	return _rs_columnHelper($kind, $columnConfiguration, $columnIndex, true, false);
}

function rs_forLastColumn($kind, $columnConfiguration, $columnIndex) {
	return _rs_columnHelper($kind, $columnConfiguration, $columnIndex, false, true);
}

function rs_forNonLastColumn($kind, $columnConfiguration, $columnIndex) {
	return _rs_columnHelper($kind, $columnConfiguration, $columnIndex, false, false);
}
