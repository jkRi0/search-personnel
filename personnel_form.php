<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$full_name = '';
$designation = '';
$office_id = '';
$errorMessage = '';

// Load offices for dropdown
$offices = $conn->query("SELECT id, office_name FROM offices ORDER BY office_name");

// If editing, load existing data
if ($id > 0) {
    $stmt = $conn->prepare("SELECT full_name, designation, office_id FROM personnel WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($full_name, $designation, $office_id);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $full_name = trim($_POST['full_name']);
    $designation = trim($_POST['designation']);
    $office_id = (int)$_POST['office_id'];
    // Check for duplicate (same full name and office)
    if ($id > 0) {
        $check = $conn->prepare("SELECT COUNT(*) FROM personnel WHERE full_name = ? AND office_id = ? AND id <> ?");
        $check->bind_param('sii', $full_name, $office_id, $id);
    } else {
        $check = $conn->prepare("SELECT COUNT(*) FROM personnel WHERE full_name = ? AND office_id = ?");
        $check->bind_param('si', $full_name, $office_id);
    }

    $check->execute();
    $check->bind_result($exists_count);
    $check->fetch();
    $check->close();

    if ($exists_count > 0) {
        $errorMessage = 'This personnel with the selected office already exists.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE personnel SET full_name = ?, designation = ?, office_id = ? WHERE id = ?");
            $stmt->bind_param('ssii', $full_name, $designation, $office_id, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO personnel (full_name, designation, office_id) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $full_name, $designation, $office_id);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: personnel_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $id > 0 ? 'Edit Personnel' : 'Add Personnel'; ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f7f1e8, #fffaf3); margin:0; padding:20px; }
        .page-wrapper { max-width: 600px; margin: 0 auto; }
        .card { background:#fff; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; }
        .card-header { background:#7b4a2e; color:#fff; padding:16px 24px; }
        .card-header h1 { margin:0; font-size:20px; }
        .card-body { padding:20px 24px 24px; }
        .form-group { margin-bottom:14px; }
        label { display:block; font-size:13px; margin-bottom:4px; color:#555; }
        input[type="text"], select { width:100%; padding:8px 10px; font-size:14px; border-radius:4px; border:1px solid #ced4da; outline:none; }
        input[type="text"]:focus, select:focus { border-color:#8b5a2b; box-shadow:0 0 0 2px rgba(139,90,43,0.15); }
        .btn { padding:8px 16px; border:none; border-radius:4px; font-size:14px; cursor:pointer; }
        .btn-primary { background:#8b5a2b; color:#fff; }
        .btn-primary:hover { background:#6d4420; }
        .btn-secondary { background:#b23b26; color:#fff; text-decoration:none; display:inline-block; }
        .btn-secondary:hover { background:#8c2f1d; }
        .actions { margin-top:10px; display:flex; gap:8px; }
        .small-link { font-size:12px; margin-top:4px; display:inline-block; }
        .small-link a { color:#8b5a2b; text-decoration:none; }
        .small-link a:hover { text-decoration:underline; }
        .alert-error {
            margin-bottom:14px;
            padding:10px 12px;
            border-radius:4px;
            background:#fde2e1;
            color:#7a1f1a;
            font-size:13px;
            border:1px solid #f5b5b2;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="card">
        <div class="card-header">
            <h1><?php echo $id > 0 ? 'Edit Personnel' : 'Add Personnel'; ?></h1>
        </div>
        <div class="card-body">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($full_name); ?>" />
                </div>

                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($designation); ?>" />
                </div>

                <div class="form-group">
                    <label for="office_id">Office</label>
                    <select id="office_id" name="office_id" required>
                        <option value="">-- Select Office --</option>
                        <?php if ($offices && $offices->num_rows > 0): ?>
                            <?php while ($o = $offices->fetch_assoc()): ?>
                                <option value="<?php echo $o['id']; ?>" <?php echo ($office_id == $o['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($o['office_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <div class="small-link">
                        <a href="offices_list.php" target="_blank">Manage office names</a>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="personnel_list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
