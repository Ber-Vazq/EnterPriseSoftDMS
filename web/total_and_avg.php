<?php
include('../functions.php');
$dblink = db_connect("documents");

// SQL Query to calculate total and average size
$query = "SELECT SUM(file_size) AS total_size, AVG(file_size) AS avg_size 
          FROM file_data 
          WHERE upload_type = 'API' AND upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'";
$result = $dblink->query($query);
$row = $result->fetch_assoc();

echo "<h1>Total and Average Document Size</h1>";
echo "Total Size: " . htmlspecialchars($row['total_size']) . " bytes<br>";
echo "Average Size: " . htmlspecialchars($row['avg_size']) . " bytes<br>";
?>
