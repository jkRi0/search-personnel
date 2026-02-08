<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$office_name = '';
$errorMessage = '';

if ($id > 0) {
    $stmt = $conn->prepare("SELECT office_name FROM offices WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($office_name);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $office_name = trim($_POST['office_name']);
    // Check for duplicate office name
    if ($id > 0) {
        $check = $conn->prepare("SELECT COUNT(*) FROM offices WHERE office_name = ? AND id <> ?");
        $check->bind_param('si', $office_name, $id);
    } else {
        $check = $conn->prepare("SELECT COUNT(*) FROM offices WHERE office_name = ?");
        $check->bind_param('s', $office_name);
    }

    $check->execute();
    $check->bind_result($exists_count);
    $check->fetch();
    $check->close();

    if ($exists_count > 0) {
        $errorMessage = 'This office name already exists.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE offices SET office_name = ? WHERE id = ?");
            $stmt->bind_param('si', $office_name, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO offices (office_name) VALUES (?)");
            $stmt->bind_param('s', $office_name);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: offices_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $id > 0 ? 'Edit Office' : 'Add Office'; ?></title>
    <meta name="theme-color" content="#7b4a2e" />
    <link rel="manifest" href="manifest.json" />
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f7f1e8, #fffaf3); margin:0; padding:20px; }
        .page-wrapper { max-width: 550px; margin: 0 auto; }
        .card { background:#fff; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; }
        .card-header { background:#7b4a2e; color:#fff; padding:16px 24px; }
        .card-header h1 { margin:0; font-size:20px; }
        .card-body { padding:20px 24px 24px; }
        .form-group { margin-bottom:14px; }
        label { display:block; font-size:13px; margin-bottom:4px; color:#555; }
        input[type="text"] { width:100%; padding:8px 10px; font-size:14px; border-radius:4px; border:1px solid #ced4da; outline:none; }
        input[type="text"]:focus { border-color:#8b5a2b; box-shadow:0 0 0 2px rgba(139,90,43,0.15); }
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
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="card">
        <div class="card-header">
            <h1><?php echo $id > 0 ? 'Edit Office' : 'Add Office'; ?></h1>
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
                    <label for="office_name">Office Name</label>
                    <input type="text" id="office_name" name="office_name" required value="<?php echo htmlspecialchars($office_name); ?>" />
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="offices_list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('service-worker.js');
    });
}
</script>
</body>
</html>
