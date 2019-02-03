<?php

/**
 *
 * https://www.youtube.com/watch?v=MfhvNHY55cQ
 *https://github.com/ivansamofal/google-sheets/blob/master/index.php
 * https://www.twilio.com/blog/2017/03/google-spreadsheets-and-php.html
 * https://www.fillup.io/post/read-and-write-google-sheets-from-php/
 *
 */

require __DIR__ . '/vendor/autoload.php';

/*
 * We need to get a Google_Client object first to handle auth and api calls, etc.
 */
$client = new \Google_Client();
$client->setApplicationName( 'My PHP App' );
$client->setScopes( [ \Google_Service_Sheets::SPREADSHEETS ] );
$client->setAccessType( 'offline' );

/*
 * The JSON auth file can be provided to the Google Client in two ways, one is as a string which is assumed to be the
 * path to the json file. This is a nice way to keep the creds out of the environment.
 *
 * The second option is as an array. For this example I'll pull the JSON from an environment variable, decode it, and
 * pass along.
 */
//$jsonAuth = getenv( 'JSON_AUTH' );
//$client->setAuthConfig( json_decode( $jsonAuth, true ) );
$client->setAuthConfig( 'credentials.json' );
refresh_token( $client );

/**
 * @param Google_Client $client
 * @throws Exception
 */
function refresh_token( $client ) {
	// Refresh the token if possible, else fetch a new one.
	if ( $client->getRefreshToken() ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
	} else {
		// Request authorization from the user.
		$authUrl = $client->createAuthUrl();
		printf( "Open the following link in your browser:\n%s\n", $authUrl );
		print 'Enter verification code: ';
		$authCode = trim( fgets( STDIN ) );

		// Exchange authorization code for an access token.
		$accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );
		$client->setAccessToken( $accessToken );

		// Check to see if there was an error.
		if ( array_key_exists( 'error', $accessToken ) ) {
			throw new Exception( join( ', ', $accessToken ) );
		}
	}
	// Save the token to a file.
	if ( !file_exists( dirname( $tokenPath ) ) ) {
		mkdir( dirname( $tokenPath ), 0700, true );
	}
	file_put_contents( $tokenPath, json_encode( $client->getAccessToken() ) );
}

/*
 * With the Google_Client we can get a Google_Service_Sheets service object to interact with sheets
 */
$sheets = new \Google_Service_Sheets( $client );

/*
 * To read data from a sheet we need the spreadsheet ID and the range of data we want to retrieve.
 * Range is defined using A1 notation, see https://developers.google.com/sheets/api/guides/concepts#a1_notation
 */
$data = [];

// The first row contains the column titles, so lets start pulling data from row 2
$currentRow = 2;

// The range of A2:H will get columns A through H and all rows starting from row 2
//$spreadsheetId = getenv( 'SPREADSHEET_ID' );
$spreadsheetId = "1-gWwWVMnC_F9OwFAeZtY5KKVYoQt_KXqYdLAq_ko5hY";
$range = 'A2:H';
$rows = $sheets->spreadsheets_values->get( $spreadsheetId, $range, [ 'majorDimension' => 'ROWS' ] );
if ( isset( $rows['values'] ) ) {
	foreach ( $rows['values'] as $row ) {
		/*
		 * If first column is empty, consider it an empty row and skip (this is just for example)
		 */
		if ( empty( $row[0] ) ) {
			break;
		}

		$data[] = [
			'col-a' => $row[0],
			'col-b' => $row[1],
			'col-c' => $row[2],
			'col-d' => $row[3],
			'col-e' => $row[4],
			'col-f' => $row[5],
			'col-g' => $row[6],
			'col-h' => $row[7],
		];

		/*
		 * Now for each row we've seen, lets update the I column with the current date
		 */
		$updateRange = 'I' . $currentRow;
		$updateBody = new \Google_Service_Sheets_ValueRange( [
			'range'          => $updateRange,
			'majorDimension' => 'ROWS',
			'values'         => [ 'values' => date( 'c' ) ],
		] );
		$sheets->spreadsheets_values->update(
			$spreadsheetId,
			$updateRange,
			$updateBody,
			[ 'valueInputOption' => 'USER_ENTERED' ]
		);

		$currentRow ++;
	}
}

print_r( $data );
/* Output:
Array
(
    [0] => Array
        (
            [col-a] => 123
            [col-b] => test
            [col-c] => user
            [col-d] => test user
            [col-e] => usertest
            [col-f] => email@domain.com
            [col-g] => yes
            [col-h] => no
        )

    [1] => Array
        (
            [col-a] => 1234
            [col-b] => another
            [col-c] => user
            [col-d] =>
            [col-e] => another
            [col-f] => another@eom.com
            [col-g] => no
            [col-h] => yes
        )

)
 */