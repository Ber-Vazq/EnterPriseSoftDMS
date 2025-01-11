<?php
include('../functions.php');
$dblink = db_connect("documents");

// SQL query
$sql = "SELECT 
            fd.doc_type_id, 
            dt.name AS doc_type_name, 
            COUNT(*) AS total_count
        FROM 
            file_data fd
        LEFT JOIN 
            docTypes dt
        ON 
            fd.doc_type_id = dt.id
        WHERE 
            fd.upload_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
        GROUP BY 
            fd.doc_type_id, dt.name";

// Execute the query
$result = $dblink->query($sql);

if ($result) {
    // Fetch and display the results
    echo "<table border='1'>";
    echo "<tr><th>Doc Type ID</th><th>Doc Type Name</th><th>Total Count</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['doc_type_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['doc_type_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_count']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Error: " . $dblink->error;
}

// Close the database connection
$dblink->close();
?>