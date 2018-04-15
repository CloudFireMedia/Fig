<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/google_client.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/libs/smarty/Smarty.class.php');

$smarty = new Smarty();
$smarty->template_dir = $_SERVER['DOCUMENT_ROOT'].'/tmpl/';
$smarty->compile_dir = $_SERVER['DOCUMENT_ROOT'].'/tmpl/compiled/';

$categories = array();
$events = array(
	'upcoming' => array(
		'events' => array(),
		'count' => 0
	),
	'scheduled' => array(
		'events' => array(),
		'count' => 0
	),
	'starred' => array(
		'events' => array(),
		'count' => 0
	),
	'past' => array(
		'events' => array(),
		'count' => 0
	)
);

$gss = new Google_Service_Sheets($google_client);
$spreadsheet_id = '1FEjVqDZwES6G10GnmuhWntNF7Y45jqVZMHuthf4n36U';
$spreadsheet = $gss->spreadsheets->get($spreadsheet_id);
$username = substr($spreadsheet->properties->title, strlen('Live_Fig_Database_User_'));
$row_id = 2;
$range = 'Events!A2:J';
$options = array('valueRenderOption' => 'UNFORMATTED_VALUE');
$rows = $gss->spreadsheets_values->get($spreadsheet_id, $range, $options);

foreach ($rows['values'] as $row) {
	$category = trim(strtolower($row[2]));
	$event_follows = empty($row[4]) ? 0 : (int)$row[4];
	$status = trim(strtolower($row[3]));
	$e_time = new DateTime('1970-01-01 00:00:00+00:00');
	$e_time->setTimestamp(intval($row[5]*60*60*24) + intval($g_time->format('U')));

	$event = array(
		'id' => $row[0],
		'title' => $row[1],
		'category' => $category,
		'status' => $status,
		'follows' => $event_follows,
		'fulldate' => $e_time,
		'date' => $e_time->format('l, F d'),
		'time' => $e_time->format('h:i A'),
		'address' => $row[6]
	);

	if ($c_time >= $e_time) {
		$range = 'Events!D'.$row_id.':D'.$row_id;
		$values = array([
			'Past'
		]);
		$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
		$options = array('valueInputOption' => 'RAW');
		$results = $gss->spreadsheets_values->update($spreadsheet_id, $range, $body, $options);

		array_push($events['past']['events'], $event);
		$events['past']['count']++;
	} else {
		if (!empty($category)) {
			if (isset($categories[$category])) {
				$categories[$category]['follows'] += $event_follows;
				$categories[$category]['count']++;
			} else {
				$categories = array_merge($categories, array($category => array(
					'follows' => $event_follows,
					'count' => 1
				)));
			}
		}

		array_push($events['upcoming']['events'], $event);
		$events['upcoming']['count']++;

		switch ($status) {
			case 'scheduled':
				array_push($events['scheduled']['events'], $event);
				$events['scheduled']['count']++;
				break;
			case 'starred':
				array_push($events['starred']['events'], $event);
				$events['starred']['count']++;
				break;
		}
	}

	$row_id++;
}
/*
foreach ($events as &$info) {
	uasort($info['events'], function($a, $b) {
		return ($a['fulldate'] > $b['fulldate']);
	});
}
*/
uasort($categories, function($a, $b) {
	return ($a['follows'] < $b['follows']);
});
//array_splice($categories, 3);

$range = 'Interests!B2:B';
$options = array('valueRenderOption' => 'UNFORMATTED_VALUE');
$rows = $gss->spreadsheets_values->get($spreadsheet_id, $range, $options);
$interests = array();

foreach ($rows['values'] as $row) {
	array_push($interests, $row[0]);
}

$smarty->assign('username', $username);
$smarty->assign('categories', $categories);
$smarty->assign('events', $events);
$smarty->assign('interests', $interests);

$smarty->display('index_old.tmpl');

?>