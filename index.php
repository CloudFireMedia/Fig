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
$categories = array();
$username = '';

foreach ($data->feed->entry as $row) {
	if (empty($username)) {
		$username = $row->{'gsx$name'}->{'$t'};
	}

	$category = trim(strtolower($row->{'gsx$category'}->{'$t'}));
	$row_count = empty($row->{'gsx$count'}->{'$t'}) ? 0 : (int)$row->{'gsx$count'}->{'$t'};

	if (!empty($category)) {
		if (isset($categories[$category])) {
			$categories[$category] = $categories[$category] + $row_count;
		} else {
			$categories = array_merge($categories, array($category => $row_count));
		}
	}

	array_push($buttons, array(
		'title' => $row->{'gsx$allevents'}->{'$t'},
		'category' => $category,
		'status' => trim(strtolower($row->{'gsx$status'}->{'$t'})),
		'count' => $row_count,
		'time' => $row->{'gsx$time'}->{'$t'},
		'place' => $row->{'gsx$place'}->{'$t'},
		'location' => $row->{'gsx$location'}->{'$t'},
		'href' => $row->{'gsx$buttonspecialhref'}->{'$t'}
	));
}

usort($buttons, function($a, $b) {
	return ($a['count'] < $b['count']);
});
arsort($categories);
array_splice($categories, 3);

$smarty->assign('username', $username);
$smarty->assign('buttons', $buttons);
$smarty->assign('categories', $categories);

$smarty->display('index.tmpl');

?>