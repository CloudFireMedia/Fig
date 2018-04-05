<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/libs/smarty/Smarty.class.php');

$smarty = new Smarty();
$smarty->template_dir = $_SERVER['DOCUMENT_ROOT'].'/tmpl/';
$smarty->compile_dir = $_SERVER['DOCUMENT_ROOT'].'/tmpl/compiled/';

/*
$question = '** The topic of conversation and interest you expressed to Fig will appear here **';

$mysqli = new mysqli("aaca260bqz22y3.coz9cd49podg.us-east-2.rds.amazonaws.com:3306", "twilio", "8ZthYm4L", "ebdb");

if (mysqli_connect_errno()) {
	printf("Подключение не удалось: %s\n", mysqli_connect_error());
	exit();
}

$query = 'SELECT id, message FROM questions ORDER BY id DESC LIMIT 1';
$result = $mysqli->query($query);
if ($result) {
	while ($row = $result->fetch_assoc()) {
		$question = $row['message'];
	}
	$result->free();
}
$mysqli->close();
*/

$gdoc_key = '1FEjVqDZwES6G10GnmuhWntNF7Y45jqVZMHuthf4n36U';
$gdoc_url = 'https://spreadsheets.google.com/feeds/list/'.$gdoc_key.'/1/public/values?alt=json';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $gdoc_url);

$result = curl_exec($ch);

curl_close($ch);

$data = json_decode($result);
$buttons = array();
$username = '';

foreach ($data->feed->entry as $row) {
	if (empty($username)) {
		$username = $row->{'gsx$username'}->{'$t'};
	}

	array_push($buttons, array(
		'title' => $row->{'gsx$title'}->{'$t'},
		'count' => $row->{'gsx$count'}->{'$t'},
		'time' => $row->{'gsx$time'}->{'$t'},
		'place' => $row->{'gsx$place'}->{'$t'},
		'lat' => $row->{'gsx$latitude'}->{'$t'},
		'lng' => $row->{'gsx$longitude'}->{'$t'},
		'href' => $row->{'gsx$buttonspecialhref'}->{'$t'}
	));
}

$smarty->assign('username', $username);
$smarty->assign('buttons', $buttons);

$smarty->display('index.tmpl');

?>