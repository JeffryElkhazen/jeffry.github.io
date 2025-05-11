<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load credentials JSON file and initialize Google Client
$client = new Client();
$client->setAuthConfig('credentials.json'); // Ensure this path is correct
$client->setScopes([Sheets::SPREADSHEETS]);

// Initialize Google Sheets service
$service = new Sheets($client);
$spreadsheetId = '16trLynI2VAgWFvuJ9z6ubp_Bspjc3sKjJR3l-nbFLsg'; // Replace with your actual spreadsheet ID
$range = 'Sheet1!A1:C1'; // Adjust range as needed

// Verify if data is received from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $attendees = isset($_POST['noofattendees']) ? trim($_POST['noofattendees']) : '';
    $willAttend = isset($_POST['willattend']) ? trim($_POST['willattend']) : '';

    // Validate that all fields have values
    if (!empty($name) && !empty($attendees) && !empty($willAttend)) {
        $values = [[$name, $attendees, $willAttend]];

        $body = new Google\Service\Sheets\ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        try {
            $result = $service->spreadsheets_values->append(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'RSVP Submitted Successfully!'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Submission failed: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: Please fill in all fields.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>