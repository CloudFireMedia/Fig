<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/google_client.php');

if (isset($_GET['action'])) {
	$gss = new Google_Service_Sheets($google_client);
	$spreadsheet_id = '1FEjVqDZwES6G10GnmuhWntNF7Y45jqVZMHuthf4n36U';
	$range = 'Events!A2:J';
	$options = array('valueRenderOption' => 'UNFORMATTED_VALUE');
	$rows = $gss->spreadsheets_values->get($spreadsheet_id, $range, $options);

	switch ($_GET['action']) {
		case 'get':
			$event = array();

			$g_time = new DateTime('1899-12-30 00:00:00+00:00');
			$u_time = new DateTime('1970-01-01 00:00:00+00:00');

			$g_time->diff($u_time);

			foreach ($rows['values'] as $row) {
				if ($row[0] == $_GET['id']) {
					$e_time = new DateTime('1970-01-01 00:00:00+00:00');
					$e_time->setTimestamp(intval($row[5]*60*60*24) + intval($g_time->format('U')));

					$status = trim(strtolower($row[3]));
					$is_past = ($c_time >= $e_time);
					$category = trim(strtolower($row[2]));
					$event_follows = empty($row[4]) ? 0 : (int)$row[4];

					$c_time->setTime(0, 0, 0);
					$date_diff = $c_time->diff($e_time);
					$date_name = '';

					switch ($date_diff->d) {
						case 0:
							$date_name = 'Today at';
							break;
						case 1:
							$date_name = 'Tomorrow at';
							break;
					}

					$event = array(
						'id' => $row[0],
						'title' => $row[1],
						'category' => $category,
						'status' => $status,
						'is_past' => $is_past,
						'follows' => $event_follows,
						'fulldate' => $e_time,
						'date' => ($date_name != '') ? $date_name : $e_time->format('l, F d'),
						'time' => $e_time->format('h:ia'),
						'address' => $row[6]
					);

					header('Content-Type: application/json');
					echo(json_encode($event));
					exit;
				}
			}
			break;
		case 'set':
			if (isset($_GET['fields'])) {
				$result = array(
					'success' => false,
					'info' => ''
				);
				$fields = array(
					'name' => 'H',
					'status' => 'D',
					'title' => 'B',
					'count' => 'E',
					'time' => 'F',
					'address' => 'G',
					'category' => 'C'
				);
				$row_id = 2;

				foreach ($rows['values'] as $row) {
					if ($row[0] == $_GET['id']) {
						foreach ($_GET['fields'] as $name => $value) {
							$col_id = $fields[$name];
							$cell_name = $col_id.$row_id;
							$range = 'Events!'.$cell_name.':'.$cell_name;
							$values = array([
								ucfirst($value)
							]);
							$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
							$options = array('valueInputOption' => 'RAW');
							$results = $gss->spreadsheets_values->update($spreadsheet_id, $range, $body, $options);
							$result['success'] = true;
							$result['info'] += $results->getUpdatedCells();
						}

						header('Content-Type: application/json');
						echo(json_encode($result));
						exit;
					}

					$row_id++;
				}
			}
			break;
		case 'list':
			$events = array();

			foreach ($rows['values'] as $row) {
				$status = trim(strtolower($row[3]));
				$category = trim(strtolower($row[2]));
				$event_follows = empty($row[4]) ? 0 : (int)$row[4];
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

				if (isset($_GET['status'])) {
					switch ($_GET['status']) {
						case 'upcoming':
							if ($c_time < $e_time) {
								$event['status'] = 'upcoming';
								array_push($events, $event);
							}
							break;
						case 'past':
							if ($c_time >= $e_time) {
								$event['status'] = 'past';
								array_push($events, $event);
							}
							break;
						
						default:
							if (($status == $_GET['status']) && ($c_time < $e_time)) {
								array_push($events, $event);
							}
							break;
					}
				} elseif (isset($_GET['category']) && ($category == $_GET['category'])) {
					array_push($events, $event);
				}
			}

			/*uasort($events, function($a, $b) {
				return ($a['fulldate'] > $b['fulldate']);
			});*/

			header('Content-Type: application/json');
			echo(json_encode($events));
			exit;

			break;
	}
}

?>