<?php
include('../functions.php');
$dblink = db_connect("logs");

// Unified query for disconnects and extended response times
$query = "SELECT 
            log_time,
            context,
            error_message,
            TIMESTAMPDIFF(SECOND, LAG(log_time) OVER (ORDER BY log_time), log_time) AS time_diff,
            CASE 
                WHEN error_message LIKE '%disconnect%' THEN 'Disconnect Error'
                WHEN TIMESTAMPDIFF(SECOND, LAG(log_time) OVER (ORDER BY log_time), log_time) > 3600 THEN 'Extended Response Time'
                ELSE NULL
            END AS issue_type
          FROM logs.error_logs
          WHERE log_time BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'";

$result = $dblink->query($query);
if (!$result) {
    die("Query failed: " . $dblink->error);
}

$disconnects = 0;
$extended_responses = 0;

// Output results
echo "<h1>Log Analysis: Disconnects and Extended Response Times</h1>";

while ($row = $result->fetch_assoc()) {
    if ($row['issue_type'] === 'Disconnect Error') {
        $disconnects++;
        echo "<b>Disconnect Error:</b><br>";
    } elseif ($row['issue_type'] === 'Extended Response Time') {
        $extended_responses++;
        echo "<b>Extended Response Time:</b><br>";
    } else {
        continue;
    }

    echo "Log Time: " . htmlspecialchars($row['log_time']) . "<br>";
    echo "Context: " . htmlspecialchars($row['context']) . "<br>";
    echo "Error Message: " . htmlspecialchars($row['error_message']) . "<br>";
    echo "Time Difference: " . htmlspecialchars($row['time_diff']) . " seconds<br><br>";
}

// Summary
echo "<h2>Summary</h2>";
echo "Total Disconnect Errors: $disconnects<br>";
echo "Total Extended Response Times: $extended_responses<br>";
?>
