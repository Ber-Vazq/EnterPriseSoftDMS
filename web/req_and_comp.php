<?php
include('../functions.php');

// Database connections for documents and logs databases
$dblink = db_connect("documents");
$log_dblink = db_connect("logs");

// API credentials
$username = "jkg215";
$password = "Fc*BmKjMdZY8!bpW";

try {
    // Create a new API session
    $ch = curl_init('https://cs4743.professorvaladez.com/api/create_session');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&password=$password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $sessionResponse = curl_exec($ch);
    curl_close($ch);

    $sessionData = json_decode($sessionResponse, true);
    if (is_null($sessionData) || $sessionData[0] !== "Status: OK") {
        throw new Exception("Failed to create session. Response: " . $sessionResponse);
    }

    // Retrieve the session ID
    $sid = $sessionData[2];

    // Request all available loans from the API
    $ch = curl_init('https://cs4743.professorvaladez.com/api/request_all_loans');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "sid=$sid&uid=$username");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $loanResponse = curl_exec($ch);
    curl_close($ch);

    $allLoanIds = json_decode($loanResponse, true);
    if (is_null($allLoanIds)) {
        throw new Exception("Failed to retrieve loan data. Response: " . $loanResponse);
    }

    // Close the API session
    $ch = curl_init('https://cs4743.professorvaladez.com/api/close_session');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "sid=$sid");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // Query to find all loans that have documents in the specified date range
    $docQuery = "
        SELECT DISTINCT loan_id 
        FROM file_data 
        WHERE upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
    ";
    $docResult = $dblink->query($docQuery);
    if (!$docResult) {
        throw new Exception("Document query failed: " . $dblink->error);
    }

    // Store all documented loans in an array
    $documentedLoanIds = [];
    while ($row = $docResult->fetch_assoc()) {
        $documentedLoanIds[] = $row['loan_id'];
    }

    // Find loans that have zero documents
    $loansWithNoDocs = array_diff($allLoanIds, $documentedLoanIds);

    echo "<html><head><title>Loans With No Documents</title></head><body style='font-family: Arial, sans-serif;'>";
    echo "<h1>Loan Document Check Report</h1>";
    echo "<p>This report compares all available loans from the API to those that have documents uploaded between November 1, 2024 and November 20, 2024. Any loan that does not appear in the database during this period is considered to have no documents.</p>";

    if (empty($loansWithNoDocs)) {
        // Every loan has at least one document
        echo "<h2>All Loans Have Documents</h2>";
        echo "<p>Every loan ID returned by the API has at least one associated document in the given date range.</p>";
    } else {
        // Some loans have no documents
        $countNoDocs = count($loansWithNoDocs);
        echo "<h2>Loans with No Documents Found</h2>";
        echo "<p>Number of loans with no documents: <strong>" . $countNoDocs . "</strong></p>";
        echo "<p>The following loan IDs did not have any documents uploaded in the specified timeframe:</p>";

        // Print each loan ID on its own line
        echo implode("<br>", array_map('htmlspecialchars', $loansWithNoDocs));
    }

    echo "</body></html>";

} catch (Exception $e) {
    // Handle any errors by displaying a user-friendly message
    echo "<html><head><title>Error</title></head><body style='font-family: Arial, sans-serif;'>";
    echo "<h1>An Error Occurred</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</body></html>";
}
?>
