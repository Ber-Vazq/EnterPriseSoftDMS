<?php
include('../functions.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadType = isset($_POST['loanType']) ? $_POST['loanType'] : '';
    if ($uploadType == "new") {
        header("Location: upload_new_file.php");
        exit();
    } elseif ($uploadType == "existing") {
        header("Location: upload_existing.php");
        exit();
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Upload Main Page</title>
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
            text-align: center;
            display: inline-block;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            padding: 15px 30px;
            font-size: 15px;
            border-radius: 20px;
            margin-bottom: 15px;
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
        <!-- Upload Main Form Card -->
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-upload"></i> Main Upload Page
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="loanType" class="control-label">Upload to New Loan or Existing Loan:</label>
                        <select class="form-control" name="loanType" required>
                            <option value="">Select Loan Option</option>
                            <option value="new">New Loan</option>
                            <option value="existing">Existing Loan</option>
                        </select>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" name="submit" value="submit" class="btn btn-success">
                            <i class="fas fa-arrow-right"></i> Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
                <!-- Button to go to Search Section -->
        <a href="search.php" class="btn btn-primary btn-back"><i class="fas fa-search"></i> Go to Search Section</a>
    </div>
</body>

</html>
