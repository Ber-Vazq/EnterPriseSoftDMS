<?php
include('functions.php');
date_default_timezone_set('America/Chicago');
$dblink = db_connect("documents");
$log_dblink = db_connect("logs");  // Connect to the logs schema

// Start the script and log the start event
log_event($log_dblink, "Script started", '', "createQueryRequestCRON");

$username = "jkg215";
$password = "Fc*BmKjMdZY8!bpW";
$sid = null; // Initialize session ID

try {
    // Create a session with the remote API
    $data = "username=$username&password=$password";
    $ch = curl_init('https://cs4743.professorvaladez.com/api/create_session');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: ' . strlen($data)
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    $cinfo = json_decode($result, true);

    if (is_null($cinfo) || $cinfo[0] != "Status: OK") {
        throw new Exception("Session creation failed. Response: " . print_r($cinfo, true));
    }

    $sid = $cinfo[2]; // Retrieve session ID
    log_event($log_dblink, "Session created successfully", $sid, "create_session");

    // Query files from the remote API
    $data = "uid=$username&sid=$sid";
    $ch = curl_init('https://cs4743.professorvaladez.com/api/query_files');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: ' . strlen($data)
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    $cinfo = json_decode($result, true);

    if (is_null($cinfo) || !isset($cinfo[1])) {
        throw new Exception("Failed to retrieve query files. Response: " . $result);
    }

    log_event($log_dblink, "Successfully retrieved query files", $sid, "query_files");

    $tmp = explode(":", $cinfo[1]);
    $payload = json_decode($tmp[1], true);
    if (is_null($payload) || !is_array($payload)) {
        throw new Exception("Failed to parse payload JSON or payload is not an array.");
    }

foreach ($payload as $key => $value) {
    $data = "sid=$sid&uid=$username&fid=$value";
    $ch = curl_init('https://cs4743.professorvaladez.com/api/request_file');

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: ' . strlen($data)
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    if (strstr($result, "Status")) {
        log_error($log_dblink, "Error with file: $value. Response: $result", "request_file");
        continue;
    }

    $content = $result;
    if (strlen($content) == 0) {
        log_error($log_dblink, "File $value received zero length.", "file_download");
        continue;
    }

    // Extract loan_id and document type from file_name
    $loan_id = (int) explode("-", $value)[0];  // Extract loan ID before the first hyphen
    $doc_type_name = explode("-", $value)[1];  // Extract the document type after the first hyphen

    // Query to get the doc_type_id from the docTypes table
    $doc_type_id = null;
    $docTypeQuery = "SELECT id FROM docTypes WHERE name = '" . addslashes($doc_type_name) . "' LIMIT 1";
    $docTypeResult = $dblink->query($docTypeQuery);
    if ($docTypeResult && $docTypeResult->num_rows > 0) {
        $row = $docTypeResult->fetch_assoc();
        $doc_type_id = $row['id'];
    } else {
        log_error($log_dblink, "Document type '$doc_type_name' not found in docTypes table.", "document_type_lookup");
        continue;  // Skip this file if document type is not found
    }

    // Insert file data into the database
    $contentClean = addslashes($content);
    $fileName = addslashes($value);
    $fileSize = strlen($content);
    $now = date("Y-m-d H:i:s");

    $sql = "INSERT INTO file_data (file_name, file_size, upload_date, file_content, upload_type, loan_id, doc_type_id) 
            VALUES ('$fileName', '$fileSize', '$now', '$contentClean', 'cron', '$loan_id', '$doc_type_id')";
    
    if (!$dblink->query($sql)) {
        log_error($log_dblink, "Failed to insert file data for file $value. SQL Error: " . $dblink->error, "database_insertion");
        continue;
    }

    log_event($log_dblink, "File $value written to filesystem", $sid, "database_insertion");
}


} catch (Exception $e) {
    log_error($log_dblink, $e->getMessage(), "createQueryRequestCRON");
} finally {
    // Always attempt to close the session, regardless of errors
    if ($sid) {
        $data = "sid=$sid";
        $ch = curl_init('https://cs4743.professorvaladez.com/api/close_session');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($data)
        ));
        curl_exec($ch);
        curl_close($ch);
        log_event($log_dblink, "Session Closed", $sid, "close_session");
    }
}
?>
