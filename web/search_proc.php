<?php
include('../functions.php');
$dblink = db_connect("documents");
?>

<!doctype html>
<html>

<head>
    <title>Search Results</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 25px;
        }

        .table-container {
            margin-top: 30px;
        }

        .table thead th {
            background-color: #343a40;
            color: #fff;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 5px 15px;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background-color: #5a6268;
            text-decoration: none;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Replace null coalescing operator with isset() for compatibility
    $loanNumber = isset($_POST['loanNumber']) ? $_POST['loanNumber'] : null;
    $docType = isset($_POST['docType']) ? $_POST['docType'] : null;
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;

    // Construct SQL query with filters based on user input
    $sql = "SELECT f.loan_id, f.file_name, f.file_size, dt.name AS doc_type, f.last_access, f.upload_date, f.auto_id
            FROM file_data f
            JOIN docTypes dt ON f.doc_type_id = dt.id
            WHERE 1=1";

    if (!empty($loanNumber)) {
        $loanNumber = intval($loanNumber);
        $sql .= " AND f.loan_id = $loanNumber";
    }

    if (!empty($docType)) {
        $docType = intval($docType);
        $sql .= " AND f.doc_type_id = $docType";
    }

    if (!empty($startDate)) {
        $sql .= " AND f.upload_date >= '$startDate'";
    }

    if (!empty($endDate)) {
        $sql .= " AND f.upload_date <= '$endDate'";
    }

    if (isset($_POST['listAll'])) {
        $sql = "SELECT f.loan_id, f.file_name, f.file_size, dt.name AS doc_type, f.last_access, f.upload_date, f.auto_id
                FROM file_data f
                JOIN docTypes dt ON f.doc_type_id = dt.id";
    }

    $result = $dblink->query($sql);

    if (!$result) {
        echo "<div class='container'><div class='alert alert-danger'>SQL Error: " . htmlspecialchars($dblink->error) . "</div></div>";
    } elseif ($result->num_rows > 0) {
        echo "<div class='container table-container'><div class='card shadow-sm'>";
        echo "<div class='card-header'><i class='fas fa-file-alt'></i> Search Results</div>";
        echo "<div class='card-body'>";
        echo "<table class='table table-striped table-hover table-bordered'>";
        echo "<thead><tr>
                <th>Loan ID</th>
                <th>File Name</th>
                <th>File Size</th>
                <th>Document Type</th>
                <th>Last Access</th>
                <th>View Document</th>
              </tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $viewUrl = "view.php?file_id=" . $row['auto_id'];
            echo "<tr>
                    <td>" . htmlspecialchars($row['loan_id']) . "</td>
                    <td>" . htmlspecialchars($row['file_name']) . "</td>
                    <td>" . htmlspecialchars($row['file_size']) . " bytes</td>
                    <td>" . htmlspecialchars($row['doc_type']) . "</td>
                    <td>" . htmlspecialchars($row['last_access']) . "</td>
                    <td><a href='$viewUrl' target='_blank' class='btn btn-view btn-sm'>
                        <i class='fas fa-eye'></i> View PDF
                        </a>
                    </td>
                  </tr>";
        }

        echo "</tbody></table>";
        echo "</div></div></div>";
    } else {
        echo "<div class='container'><div class='alert alert-warning'>No files found matching the criteria.</div></div>";
    }
}
?>
<div class="container">
    <a href="search.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Go Back to Search</a>
</div>

</body>
</html>
