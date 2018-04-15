<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/libs/google-api-php-client/vendor/autoload.php');

session_start();

$redirect_uri = 'http://'.$_SERVER['HTTP_HOST'];
$google_client = new Google_Client();
$google_client->setClientId('404463758628-0nk8e4l8fsq0rgkfs4e9v4hv7v7ems4q.apps.googleusercontent.com');
$google_client->setClientSecret('PiSSQqdE0XPUkEybFDz-oNv_');
$google_client->setRedirectUri($redirect_uri);
$google_client->addScope(Google_Service_Sheets::SPREADSHEETS);

if (isset($_GET['code'])) {
	$token = $google_client->authenticate($_GET['code']);
	$_SESSION['token'] = $token;
	header('Location: '.filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

$expired = new DateTime();
$c_time = new DateTime();
$g_time = new DateTime('1899-12-30 00:00:00+00:00');
$u_time = new DateTime('1970-01-01 00:00:00+00:00');

$g_time->diff($u_time);

if (isset($_SESSION['token'])) {
	$expired->setTimestamp($_SESSION['token']['created'] + $_SESSION['token']['expires_in']);
}

if ($c_time < $expired) {
	$google_client->setAccessToken($_SESSION['token']);
} else {
	$authUrl = $google_client->createAuthUrl();

	header('Location: '.$authUrl);
	exit;
}

?>