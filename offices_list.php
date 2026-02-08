<?php
require_once 'db.php';

$result = $conn->query("SELECT id, office_name FROM offices ORDER BY office_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Office Management</title>
    <meta name="theme-color" content="#7b4a2e" />
    <link rel="manifest" href="manifest.json" />
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f7f1e8, #fffaf3); margin:0; padding:20px; }
        .page-wrapper { max-width: 800px; margin: 0 auto; }
        .card { background:#fff; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; }
        .card-header { background:#7b4a2e; color:#fff; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; }
        .card-header h1 { margin:0; font-size:22px; }
        .card-body { padding:20px 24px 24px; }
        a.button-link, .btn { display:inline-block; padding:7px 14px; background:#8b5a2b; color:#fff; text-decoration:none; border-radius:4px; font-size:13px; }
        a.button-link:hover, .btn:hover { background:#6d4420; }
        .btn-secondary { background:#b23b26; }
        .btn-secondary:hover { background:#8c2f1d; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { padding:8px 10px; border-bottom:1px solid #edf0f4; font-size:14px; }
        th { background:#f3e4d4; text-align:left; font-weight:600; color:#5a3a22; }
        tr:nth-child(even) td { background:#fcfdff; }
        .actions a { margin-right:6px; font-size:12px; }
        .text-muted { color:#6c757d; font-size:14px; }
        .bulk-actions { margin-top:4px; margin-bottom:4px; }
        .bulk-actions .btn-danger { background:#b23b26; }
        .bulk-actions .btn-danger:hover { background:#8c2f1d; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="card">
        <div class="card-header">
            <h1>Office Management</h1>
            <div>
                <a href="personnel_list.php" class="button-link btn-secondary">Back to Personnel</a>
                <a href="office_form.php" class="button-link">Add Office</a>
            </div>
        </div>
        <div class="card-body">
            <?php if ($result && $result->num_rows > 0): ?>
                <form method="post" action="office_delete_bulk.php" onsubmit="return confirmBulkDelete('office');">
                    <div class="bulk-actions">
                        <button type="submit" class="btn btn-secondary btn-danger">Delete Selected</button>
                    </div>
                    <table>
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select_all_offices" /></th>
                            <th>#</th>
                            <th>Office Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" class="row-check-office" /></td>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['office_name']); ?></td>
                                <td class="actions">
                                    <a href="office_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                    <a href="office_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary" onclick="return confirm('Delete this office? It cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <p class="text-muted">No offices yet. Click <strong>Add Office</strong> to create one.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
const selectAllOffices = document.getElementById('select_all_offices');
if (selectAllOffices) {
    selectAllOffices.addEventListener('change', function () {
        const checks = document.querySelectorAll('.row-check-office');
        checks.forEach(ch => ch.checked = selectAllOffices.checked);
    });
}

function confirmBulkDelete(type) {
    const selector = type === 'personnel' ? '.row-check-personnel' : '.row-check-office';
    const checks = document.querySelectorAll(selector + ':checked');
    if (checks.length === 0) {
        alert('Please select at least one record to delete.');
        return false;
    }
    return confirm('Delete selected records? This action cannot be undone.');
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('service-worker.js');
    });
}
</script>
</body>
</html>
