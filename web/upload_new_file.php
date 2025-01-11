<?php
include('../functions.php');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loanNum'])) {
    $loanNum = trim($_POST['loanNum']);
    $docType = isset($_POST['docType']) ? $_POST['docType'] : null;
    $file = $_FILES['userfile'];
    $errors = [];

    // Validate Loan Number
    if (empty($loanNum)) {
        $errors[] = "Loan Number cannot be empty.";
    } elseif (!preg_match("/^[0-9]{1,9}$/", $loanNum)) {
        $errors[] = "Invalid Loan Number. Please enter up to 9 digits.";
    }

    // Validate File Type
    if ($file['type'] != "application/pdf") {
        $errors[] = "Only PDF files are allowed.";
    }

    // If no errors, handle file upload
    if (empty($errors)) {
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            $success = "File uploaded successfully.";
        } else {
            $errors[] = "Failed to upload the file. Please try again.";
        }
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Upload to New Loan</title>
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
        <!-- Upload to New Loan Form Card -->
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-upload"></i> Upload a New File to Database
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
                        <label for="loanNum" class="control-label">Loan Number</label>
                        <input type="text" name="loanNum" class="form-control" maxlength="9" pattern="[0-9]{1,9}" required>
                    </div>
                    <div class="form-group">
                        <label for="docType" class="control-label">Document Type</label>
                        <select class="form-control" name="docType" required>
                            <option value="">Select Document Type</option>
                            <?php
                            $dblink = db_connect("documents");
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
                <!-- Button to go back to the main upload page -->
        <a href="upload.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Go Back to Main Upload Page</a>
    </div>
</body>

</html>
