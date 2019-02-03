<?php
/**
 * User: Miguel
 * Date: 03/02/2019
 * Time: 10:59 AM
 */

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;

require __DIR__ . '/vendor/autoload.php';

putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json' );
$client = new Google_Client;
$client->setApplicationName('My EXCEL PHP App');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->useApplicationDefaultCredentials();

$client->setApplicationName( "PHP Api read sheets" );
$client->setScopes( [ 'https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds' ] );

if ( $client->isAccessTokenExpired() ) {
	$client->refreshTokenWithAssertion();
}

$accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
ServiceRequestFactory::setInstance(
	new DefaultServiceRequest( $accessToken )
);

// Get our spreadsheet
$spreadsheet = ( new SpreadsheetService )
	->getSpreadsheetFeed()
	->getByTitle( 'TRAK' );

//$feed = ( new SpreadsheetService() )->getSpreadsheetFeed();
//print_r( $feed->getId() );

// Get the first worksheet (tab)
//$worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
//$worksheet = $worksheets[0];