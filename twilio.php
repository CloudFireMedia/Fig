<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/google_client.php');

$gss = new Google_Service_Sheets($google_client);
$spreadsheet_id = '1FEjVqDZwES6G10GnmuhWntNF7Y45jqVZMHuthf4n36U';
$options = array('valueInputOption' => 'RAW');

if (isset($_GET['user_name'])) {
	$range = 'Events!D1:D1';
	$values = array([
		$_GET['user_name'].'\'s RSVP Status'
	]);
	$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
	$results = $gss->spreadsheets_values->update($spreadsheet_id, $range, $body, $options);
}
/*
if (isset($_GET['q'])) {
	$range = 'Events!B2:B2';
	$values = array([
		$_GET['q']
	]);
	$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
	$results = $gss->spreadsheets_values->update($spreadsheet_id, $range, $body, $options);
}
*/
?>