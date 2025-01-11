<?php
include('../functions.php');
$dblink = db_connect("documents");

if (isset($_GET['file_id'])) {
    $fileId = intval($_GET['file_id']);

    // Fetch file data from the database
    $sql = "SELECT file_name, file_content FROM file_data WHERE auto_id = $fileId";
    $result = $dblink->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $fileName = $row['file_name'];
        $fileContent = $row['file_content'];

        // Clear any previous output buffer to avoid corrupting the file output
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Set headers to display the PDF inline
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($fileContent));

        // Output the PDF content
        echo $fileContent;
    } else {
        echo "File not found or could not be retrieved.";
    }
} else {
    echo "Invalid request.";
}
?>
