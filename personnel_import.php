<?php
require_once 'db.php';

$summary = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Please choose a CSV file to upload.';
    } else {
        $tmpName = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmpName, 'r');
        if ($handle === false) {
            $errorMessage = 'Could not read uploaded file.';
        } else {
            $row = 0;
            $inserted = 0;
            $duplicates = 0;
            $skipped = 0;

            // Auto-detect delimiter (comma or semicolon)
            $sampleLine = fgets($handle);
            if ($sampleLine === false) {
                $errorMessage = 'Uploaded CSV file is empty.';
                fclose($handle);
            } else {
                $delimiter = ',';
                if (strpos($sampleLine, ';') !== false && strpos($sampleLine, ',') === false) {
                    $delimiter = ';';
                }

                // Rewind and read using detected delimiter
                rewind($handle);

                // Expect header: Last_Name, First_Name, Middle_Name, Designation, Department
                $header = fgetcsv($handle, 0, $delimiter);

                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row++;
                if (count($data) < 5) {
                    $skipped++;
                    continue;
                }

                $lastName   = trim($data[0]);
                $firstName  = trim($data[1]);
                $middleName = trim($data[2]);
                $designation = trim($data[3]);
                $department  = trim($data[4]);

                if ($lastName === '' && $firstName === '' && $middleName === '') {
                    $skipped++;
                    continue;
                }

                // Build full name: Last_Name, First_Name Middle_Name
                $full_name_parts = [];
                if ($lastName !== '') {
                    $full_name_parts[] = $lastName . ',';
                }
                if ($firstName !== '') {
                    $full_name_parts[] = $firstName;
                }
                if ($middleName !== '') {
                    $full_name_parts[] = $middleName;
                }
                $full_name = trim(implode(' ', $full_name_parts));

                // Ensure department/office exists or create it
                if ($department === '') {
                    $skipped++;
                    continue;
                }

                $office_id = null;
                $stmt = $conn->prepare('SELECT id FROM offices WHERE office_name = ?');
                $stmt->bind_param('s', $department);
                $stmt->execute();
                $stmt->bind_result($office_id);
                if ($stmt->fetch()) {
                    // found
                }
                $stmt->close();

                if ($office_id === null) {
                    $stmt = $conn->prepare('INSERT INTO offices (office_name) VALUES (?)');
                    $stmt->bind_param('s', $department);
                    $stmt->execute();
                    $office_id = $stmt->insert_id;
                    $stmt->close();
                }

                // Check duplicates using same rule as form (full_name + office_id)
                $exists_count = 0;
                $check = $conn->prepare('SELECT COUNT(*) FROM personnel WHERE full_name = ? AND office_id = ?');
                $check->bind_param('si', $full_name, $office_id);
                $check->execute();
                $check->bind_result($exists_count);
                $check->fetch();
                $check->close();

                if ($exists_count > 0) {
                    $duplicates++;
                    continue;
                }

                $stmt = $conn->prepare('INSERT INTO personnel (full_name, designation, office_id) VALUES (?, ?, ?)');
                $stmt->bind_param('ssi', $full_name, $designation, $office_id);
                $stmt->execute();
                $stmt->close();
                $inserted++;
            }

            fclose($handle);

            $summary = "Import finished. Inserted: {$inserted}, Duplicates: {$duplicates}, Skipped/Invalid: {$skipped}.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Import Personnel from CSV</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f7f1e8, #fffaf3); margin:0; padding:20px; }
        .page-wrapper { max-width: 700px; margin: 0 auto; }
        .card { background:#fff; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; }
        .card-header { background:#7b4a2e; color:#fff; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; }
        .card-header h1 { margin:0; font-size:20px; }
        .card-body { padding:20px 24px 24px; }
        .form-group { margin-bottom:14px; }
        label { display:block; font-size:13px; margin-bottom:4px; color:#555; }
        input[type="file"] { width:100%; font-size:13px; }
        .btn { padding:8px 16px; border:none; border-radius:4px; font-size:14px; cursor:pointer; }
        .btn-primary { background:#8b5a2b; color:#fff; }
        .btn-primary:hover { background:#6d4420; }
        .btn-secondary { background:#b23b26; color:#fff; text-decoration:none; display:inline-block; }
        .btn-secondary:hover { background:#8c2f1d; }
        .actions { margin-top:10px; display:flex; gap:8px; }
        .alert-error {
            margin-bottom:14px;
            padding:10px 12px;
            border-radius:4px;
            background:#fde2e1;
            color:#7a1f1a;
            font-size:13px;
            border:1px solid #f5b5b2;
        }
        .alert-success {
            margin-bottom:14px;
            padding:10px 12px;
            border-radius:4px;
            background:#e2f4e8;
            color:#21613b;
            font-size:13px;
            border:1px solid #add9b9;
        }
        .helper-text { font-size:12px; color:#6c757d; margin-top:4px; }
        code { background:#f3e4d4; padding:2px 4px; border-radius:3px; font-size:12px; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="card">
        <div class="card-header">
            <h1>Import Personnel from CSV</h1>
            <a href="personnel_list.php" class="btn btn-secondary">Back to Personnel</a>
        </div>
        <div class="card-body">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <?php if (!empty($summary)): ?>
                <div class="alert-success"><?php echo htmlspecialchars($summary); ?></div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required />
                    <div class="helper-text">
                        Expected columns (header row):
                        <code>Last_Name, First_Name, Middle_Name, Designation, Department</code>.<br />
                        You can create this in Excel and save as <strong>CSV UTF-8 (Comma delimited)</strong>.
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Upload and Import</button>
                    <a href="personnel_list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
