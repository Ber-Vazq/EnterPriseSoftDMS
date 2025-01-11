<?php

// Function to Connect to the Database
function db_connect($db) {
    $hostname = "localhost";
    $username = "*****";
    $password = "*****";
    $dblink = new mysqli($hostname, $username, $password, $db);

    if ($dblink->connect_error) {
        die("Error connecting to the database: " . $dblink->connect_error);
    }

    return $dblink;
}

// Function to Log Errors to the Database
function log_error($dblink, $error_message, $context = '') {
    $error_message_clean = addslashes($error_message);
    $context_clean = addslashes($context);
    $now = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `logs`.`error_logs` (`error_message`, `context`, `log_time`) VALUES ('$error_message_clean', '$context_clean', '$now')";
    $dblink->query($sql) or die("Logging failed: " . $dblink->error);
}

// Function to Log Events to the Database
function log_event($dblink, $event_message, $sid = '', $context = '') {
    $event_message_clean = addslashes($event_message);
    $context_clean = addslashes($context);
    $sid_clean = addslashes($sid);
    $now = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `logs`.`event_logs` (`event_message`, `context`, `session_id`, `log_time`) VALUES ('$event_message_clean', '$context_clean', '$sid_clean', '$now')";
    $dblink->query($sql) or die("Logging failed: " . $dblink->error);
}

// Function to Validate Loan Number
function validate_loan_number($loanNum) {
    if (empty($loanNum)) {
        return "Loan Number cannot be empty.";
    } elseif (!preg_match("/^[0-9]{1,9}$/", $loanNum)) {
        return "Invalid Loan Number. Please enter up to 9 digits.";
    }
    return "";
}

// Function to Validate File Type
function validate_file_type($file, $expectedType = "application/pdf") {
    if ($file['type'] != $expectedType) {
        return "Only PDF files are allowed.";
    }
    return "";
}

// Function to Upload File to Database
function upload_file($dblink, $loanId, $docTypeId, $file) {
    $fileContent = file_get_contents($file['tmp_name']);
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    $stmt = $dblink->prepare("INSERT INTO file_data (loan_id, doc_type_id, file_content, file_name, file_size, file_type, file_extension, upload_date, upload_type) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'manual')");
    if ($stmt === false) {
        return "Failed to prepare SQL statement: " . $dblink->error;
    }

    $stmt->bind_param("iisssss", $loanId, $docTypeId, $fileContent, $fileName, $fileSize, $fileType, $fileExtension);
    
    if ($stmt->execute()) {
        return "File uploaded successfully.";
    } else {
        return "Failed to upload the file. Please try again.";
    }
}

// Function to Fetch Document Types
function get_document_types($dblink) {
    $sql = "SELECT id, name FROM docTypes";
    $result = $dblink->query($sql);

    $docTypes = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $docTypes[] = $row;
        }
    }
    return $docTypes;
}

// Function to Fetch Existing Loan Numbers
function get_existing_loan_numbers($dblink) {
    $sql = "SELECT DISTINCT loan_id FROM loans";
    $result = $dblink->query($sql);

    $loanNumbers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $loanNumbers[] = $row['loan_id'];
        }
    }
    return $loanNumbers;
}

?>
