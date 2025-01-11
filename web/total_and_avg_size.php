<?php
include('../functions.php');
$dblink = db_connect("documents");

// Fetch all documents within the date range
$query = "SELECT CAST(file_size AS UNSIGNED) AS file_size
          FROM file_data
          WHERE upload_type = 'cron' AND upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
            AND file_size IS NOT NULL AND file_size != ''";

$result = $dblink->query($query);
if (!$result) {
    die("Query failed: " . $dblink->error . "\nQuery: " . $query);
}

// Initialize variables
$total_size = 0;
$total_files = 0;

// Loop through the results to calculate total size
while ($row = $result->fetch_assoc()) {
    $file_size = $row['file_size'];
    $total_size += $file_size;
    $total_files++;
}

// Calculate average size
$avg_size = $total_files > 0 ? $total_size / $total_files : 0;

// Display results
echo "<h1>Total and Average Document Size</h1>";
echo "Total Size: " . number_format($total_size, 2) . " bytes<br>";
echo "Average Size: " . number_format($avg_size, 2) . " bytes<br>";
?>
