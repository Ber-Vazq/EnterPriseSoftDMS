<?php
include('../functions.php');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loanId'])) {
    $loanId = $_POST['loanId'];
    $docType = isset($_POST['docType']) ? $_POST['docType'] : null;
    $file = $_FILES['userfile'];
    $errors = [];

    // Validate File Type
    if ($file['type'] != "application/pdf") {
        $errors[] = "Only PDF files are allowed.";
    }

    // If no errors, handle file upload
    if (empty($errors)) {
        $fileContent = file_get_contents($file['tmp_name']);
        if ($fileContent !== false) {
            $dblink = db_connect("documents");

            $stmt = $dblink->prepare("INSERT INTO file_data (doc_type_id, file_content, file_name, file_size, loan_id, upload_date, upload_type) VALUES (?, ?, ?, ?, ?, NOW(), 'manual')");
            $stmt->bind_param("isssi", $docType, $fileContent, $file['name'], $file['size'], $loanId);

            if ($stmt->execute()) {
                $success = "File uploaded successfully.";
            } else {
                $errors[] = "Failed to upload the file. Please try again.";
            }
        } else {
            $errors[] = "Failed to read the file. Please try again.";
        }
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Upload to Existing Loan</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <!--CUSTOM BASIC STYLES-->
    <link href="assets/css/basic.css" rel="stylesheet" />
    <!--CUSTOM MAIN STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES -->
    <link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap-fileupload.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            margin-bottom: 30px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
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

        .form-group {
            margin-bottom: 20px;
        }

        .btn {
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            padding: 15px 30px;
            font-size: 15px;
            border-radius: 20px;
            margin-bottom: 15px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            background-color: #11A4EE;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Upload to Existing Loan Form Card -->
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-upload"></i> Upload a File to Existing Loan
            </div>
            <div class="card-body">
                <?php if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } ?>
                <?php if (isset($success)) {
                    echo "<div class='alert alert-success'>$success</div>";
                } ?>
                <form method="post" enctype="multipart/form-data" action="">
                    <div class="form-group">
                        <label for="loanId" class="control-label">Select Existing Loan Number</label>
                        <select class="form-control" name="loanId" required>
                            <option value="">Select Loan Number</option>
                            <?php
                            $dblink = db_connect("documents");
                            $sql = "SELECT DISTINCT loan_id FROM file_data WHERE loan_id IS NOT NULL";
                            $result = $dblink->query($sql);
                            if ($result) {
                                while ($data = $result->fetch_assoc()) {
                                    echo '<option value="' . $data['loan_id'] . '">' . htmlspecialchars($data['loan_id']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="docType" class="control-label">Document Type</label>
                        <select class="form-control" name="docType" required>
                            <option value="">Select Document Type</option>
                            <?php
                            $sql = "SELECT id, name FROM docTypes";
                            $result = $dblink->query($sql);
                            if ($result) {
                                while ($data = $result->fetch_assoc()) {
                                    echo '<option value="' . $data['id'] . '">' . htmlspecialchars($data['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="userfile" class="control-label">File Upload</label>
                        <input name="userfile" type="file" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" name="submit" value="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                    </div>
                </form>
            </div>
        </div>
            <div class="container text-center">
        <!-- Button to go back to the main upload page -->
        <a href="upload.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Go Back to Main Upload Page</a>
    </div>
    </div>
</body>

</html>
