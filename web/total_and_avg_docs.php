<?php
include('../functions.php');
$dblink = db_connect("documents");

// Fetch document counts per loan
$query = "SELECT loan_id, COUNT(*) AS doc_count
          FROM file_data
          WHERE upload_type = 'cron' AND upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
          GROUP BY loan_id";

$result = $dblink->query($query);
if (!$result) {
    die("Query failed: " . $dblink->error . "\nQuery: " . $query);
}

// Initialize variables
$total_docs = 0;
$loan_counts = [];
$total_loans = 0;

// Loop through the results to calculate total documents and collect per-loan counts
while ($row = $result->fetch_assoc()) {
    $doc_count = $row['doc_count'];
    $total_docs += $doc_count;
    $total_loans++;
    $loan_counts[] = $doc_count;
}

// Calculate average documents per loan
$avg_docs = $total_loans > 0 ? $total_docs / $total_loans : 0;

// Display results
echo "<h1>Total and Average Document Count</h1>";
echo "Total Documents: " . number_format($total_docs) . "<br>";
echo "Average Documents per Loan: " . number_format($avg_docs, 2) . "<br>";
?>
