<?php
$time_start = microtime( true );

/**
 * Compartir el documento con el email de la claves de cuenta de servicio
 * https://stackoverflow.com/a/49965912
 *
 */

// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName( "Client_Library_Examples" );
$client->setScopes( Google_Service_Sheets::SPREADSHEETS );

//obtener al crear una Claves de cuenta de servicio del tipo json
$client->setAuthConfig( 'credentials.json' );
$client->setAccessType( 'offline' );
$client->setPrompt( 'select_account consent' );
$client->setDeveloperKey( "AIzaSyB43oMvX4bBt-r1aojI_qID_K_sF4oR6j4" );

$service = new Google_Service_Sheets( $client );

$spreadsheetId = '1-gWwWVMnC_F9OwFAeZtY5KKVYoQt_KXqYdLAq_ko5hY';
//$range = 'Params!A1:A5';
//$response = $service->spreadsheets_values->get( $spreadsheetId, $range );

// The first row contains the column titles, so lets start pulling data from row 2
$currentRow = 2;
$updateRange = 'A' . $currentRow;
$updateBody = new \Google_Service_Sheets_ValueRange( [
	'range'          => $updateRange,
	'majorDimension' => 'ROWS',
	'values'         => [ 'values' => "111" ],
] );
$service->spreadsheets_values->update(
	$spreadsheetId,
	$updateRange,
	$updateBody,
	[ 'valueInputOption' => 'USER_ENTERED' ]
);

$time_end = microtime( true );
//dividing with 60 will give the execution time in minutes otherwise seconds
//$execution_time = ( $time_end - $time_start ) / 60;
$execution_time = ( $time_end - $time_start );

//execution time of the script
echo '<b>Total Execution Time:</b> ' . $execution_time . ' segs';
// if you get weird results, use number_format((float) $execution_time, 10)