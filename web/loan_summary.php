<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../functions.php');
$dblink = db_connect("documents");

// Step 1: Run the Inner Query
$innerQuery = "SELECT loan_id, COUNT(*) AS doc_count, AVG(CAST(file_size AS UNSIGNED)) AS avg_size
               FROM file_data
               WHERE file_size IS NOT NULL AND file_size != '' 
                 AND upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
               GROUP BY loan_id";

$innerResult = $dblink->query($innerQuery);
if (!$innerResult) {
    die("Inner query failed: " . $dblink->error . "\nQuery: " . $innerQuery);
}

// Fetch results into an array
$loanStats = [];
while ($row = $innerResult->fetch_assoc()) {
    $loanStats[] = $row;
}

// Step 2: Calculate Global Averages in PHP
$totalDocs = 0;
$totalSize = 0;
$numLoans = count($loanStats);

foreach ($loanStats as $loan) {
    $totalDocs += $loan['doc_count'];
    $totalSize += $loan['avg_size'] * $loan['doc_count']; // Adjust avg_size to reflect total size for this loan
}

$global_avg_size = $numLoans > 0 ? $totalSize / $totalDocs : 0;
$global_avg_docs = $numLoans > 0 ? $totalDocs / $numLoans : 0;

// Output global averages
echo "<h1>Global Averages</h1>";
echo "Global Average Size: $global_avg_size bytes<br>";
echo "Global Average Documents per Loan: $global_avg_docs<br>";

// Step 3: Output Per-Loan Statistics
echo "<h1>Loan Summary</h1>";

foreach ($loanStats as $loan) {
    $sizeComparison = $loan['avg_size'] > $global_avg_size ? "Above" : "Below";
    $docsComparison = $loan['doc_count'] > $global_avg_docs ? "Above" : "Below";

    echo "Loan: " . htmlspecialchars($loan['loan_id']) . "<br>";
    echo "Total Documents: " . htmlspecialchars($loan['doc_count']) . " ($docsComparison global average)<br>";
    echo "Average Size: " . htmlspecialchars($loan['avg_size']) . " bytes ($sizeComparison global average)<br><br>";
}
?>
