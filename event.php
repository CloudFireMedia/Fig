<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/google_client.php');

if (isset($_GET['action']) && isset($_GET['id'])) {
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
					$status = trim(strtolower($row[2]));
					$category = trim(strtolower($row[9]));
					$event_follows = empty($row[4]) ? 0 : (int)$row[4];

					$e_time = new DateTime('1970-01-01 00:00:00+00:00');
					$e_time->setTimestamp(intval($row[5]*60*60*24) + intval($g_time->format('U')));

					$event = array(
						'id' => $row[0],
						'title' => $row[3],
						'category' => $category,
						'status' => $status,
						'follows' => $event_follows,
						'fulldate' => $e_time,
						'date' => $e_time->format('l, F d'),
						'time' => $e_time->format('h:i A'),
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
					'name' => 'B',
					'status' => 'C',
					'title' => 'D',
					'count' => 'E',
					'time' => 'F',
					'address' => 'G',
					'category' => 'J'
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
	}
}

?>