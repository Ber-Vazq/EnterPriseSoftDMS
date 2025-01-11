<?php
include('../functions.php');
$dblink = db_connect("documents");

$required_docs = ["Credit", "Closing", "Title", "Financial", "Personal", "Internal", "Legal", "MOU", "Disclosures", "Tax Returns", "PreQs", "References"];

// Query for loans missing required documents
$query = "
SELECT loan_id, GROUP_CONCAT(missing_doc) AS missing_docs
FROM (
  SELECT loans.loan_id, required_docs.name AS missing_doc
  FROM (
    SELECT DISTINCT loan_id 
    FROM file_data 
    WHERE upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
  ) AS loans
  CROSS JOIN (
    SELECT 'Credit' AS name 
    UNION SELECT 'Closing' 
    UNION SELECT 'Title' 
    UNION SELECT 'Financial' 
    UNION SELECT 'Personal' 
    UNION SELECT 'Internal' 
    UNION SELECT 'Legal' 
    UNION SELECT 'MOU' 
    UNION SELECT 'Disclosures' 
    UNION SELECT 'Tax Returns' 
    UNION SELECT 'PreQs' 
    UNION SELECT 'References'
  ) AS required_docs
  LEFT JOIN file_data fd ON loans.loan_id = fd.loan_id
    AND fd.doc_type_id IN (
       SELECT id FROM docTypes 
       WHERE name IN ('Credit', 'Closing', 'Title', 'Financial', 'Personal', 'Internal', 'Legal', 'MOU', 'Disclosures', 'Tax Returns', 'PreQs', 'References')
    )
    AND (SELECT name FROM docTypes WHERE id = fd.doc_type_id) = required_docs.name
  WHERE fd.doc_type_id IS NULL
) AS missing
GROUP BY loan_id;
";

$result = $dblink->query($query);
if(!$result){
    die("Query Failed: " . $dblink->error);
}

echo "<h1>Missing Documents</h1>";
$missing_loans = [];
while ($row = $result->fetch_assoc()) {
    $missing_loans[] = $row['loan_id'];
    echo "Loan: " . htmlspecialchars($row['loan_id']) . "<br>";
    echo "Missing Documents: " . htmlspecialchars($row['missing_docs']) . "<br><br>";
}

// Now find loans that are complete (have all required documents)
// A complete loan is one that has all the required document types
$required_count = count($required_docs);

$queryComplete = "
SELECT loan_id
FROM file_data fd
JOIN docTypes dt ON fd.doc_type_id = dt.id
WHERE dt.name IN ('" . implode("','", $required_docs) . "')
  AND upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
GROUP BY loan_id
HAVING COUNT(DISTINCT dt.name) = $required_count;
";

$completeResult = $dblink->query($queryComplete);
if(!$completeResult){
    die("Query Failed: " . $dblink->error);
}

echo "<h1>Complete Loans</h1>";
if ($completeResult->num_rows > 0) {
    while ($row = $completeResult->fetch_assoc()) {
        // Ensure the loan is not in the missing list
        // (though logically it shouldn't be if the queries are correct)
        if (!in_array($row['loan_id'], $missing_loans)) {
            echo "Loan: " . htmlspecialchars($row['loan_id']) . " has all required documents.<br>";
        }
    }
} else {
    echo "No loans are fully complete with all required documents.<br>";
}
?>
