<!doctype html>
<html>

<head>
    <title>Document Search</title>
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

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .btn {
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
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

        .help-text {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-search"></i> Search Files by Loan
            </div>
            <div class="card-body">
                <form method="post" action="search_proc.php">
                    <div class="form-group">
                        <label for="loanNumber" class="form-label">Select Loan Number</label>
                        <select name="loanNumber" class="form-control">
                            <option value="">Select Loan Number</option>
                            <?php
                            // Include database connection and fetch loan numbers
                            include('../functions.php');
                            $dblink = db_connect("documents");
                            $result = $dblink->query("SELECT DISTINCT loan_id FROM file_data WHERE loan_id IS NOT NULL ORDER BY loan_id ASC");

                            // Populate the dropdown with loan numbers
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . intval($row['loan_id']) . '">' . htmlspecialchars($row['loan_id']) . '</option>';
                            }
                            ?>
                        </select>
                        <small class="help-text">Choose a loan number to filter your search.</small>
                    </div>
                    <div class="form-group">
                        <label for="docType" class="form-label">Document Type (Optional)</label>
                        <select name="docType" class="form-control">
                            <option value="">Select Document Type</option>
                            <?php
                            // Fetch document types from the database
                            $result = $dblink->query("SELECT id, name FROM docTypes");
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                            }
                            ?>
                        </select>
                        <small class="help-text">Optionally select a document type to narrow down your search.</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="startDate" class="form-label">Start Date (Optional)</label>
                            <input type="date" name="startDate" class="form-control">
                            <small class="help-text">Select a start date to filter results.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="endDate" class="form-label">End Date (Optional)</label>
                            <input type="date" name="endDate" class="form-control">
                            <small class="help-text">Select an end date to filter results.</small>
                        </div>
                    </div>
                    <div class="form-group text-center mt-4">
                        <button type="submit" name="search" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="submit" name="listAll" class="btn btn-secondary">
                            <i class="fas fa-list"></i> List All Files
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                <div class="container text-center">
        <!-- Button to go back to the main upload page -->
        <a href="upload.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Go Back to Main Upload Page</a>
    </div>
</body>

</html>
