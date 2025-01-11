<?php
include('../functions.php');
$dblink = db_connect("documents");

// SQL Query to fetch unique loan numbers
$query = "SELECT DISTINCT loan_id 
          FROM file_data 
          WHERE upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'";
$result = $dblink->query($query);

$total_unique = $result->num_rows;
echo "<h1>Unique Loan Numbers</h1>";
echo "<p>Total Unique Loans: $total_unique</p>";

while ($row = $result->fetch_assoc()) {
    echo "<p>" . htmlspecialchars($row['loan_id']) . "</p>";
}
?>
