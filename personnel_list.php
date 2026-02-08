<?php
require_once 'db.php';

// Read selected office filter (0 = All) and designation filter (empty = All)
$selectedOfficeId = isset($_GET['office_id']) ? (int)$_GET['office_id'] : 0;
$selectedDesignation = isset($_GET['designation']) ? trim($_GET['designation']) : '';

// Load offices for filter dropdown
$officesFilter = $conn->query("SELECT id, office_name FROM offices ORDER BY office_name");

// Load distinct designations for designation filter
$designationsFilter = $conn->query("SELECT DISTINCT designation FROM personnel WHERE designation <> '' ORDER BY designation");

// Build personnel query with optional filters
$sql = "SELECT p.id, p.full_name, p.designation, o.office_name
        FROM personnel p
        JOIN offices o ON p.office_id = o.id";
// Collect conditions
$conditions = [];
if ($selectedOfficeId > 0) {
    $conditions[] = "p.office_id = " . $selectedOfficeId;
}
if ($selectedDesignation !== '') {
    // Use prepared-like escaping via real_escape_string for safety since we're concatenating
    $escapedDesignation = $conn->real_escape_string($selectedDesignation);
    $conditions[] = "p.designation = '" . $escapedDesignation . "'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY p.full_name";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Personnel Management</title>
    <meta name="theme-color" content="#7b4a2e" />
    <link rel="manifest" href="manifest.json" />
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f7f1e8, #fffaf3); margin:0; padding:20px; }
        .page-wrapper { max-width: 1000px; margin: 0 auto; }
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
        .actions { display:flex; align-items:center; gap:6px; }
        .actions a { font-size:12px; }
        .text-muted { color:#6c757d; font-size:14px; }
        .page-title { text-align:center; margin-bottom:12px; font-size:28px; font-weight:600; color:#5a3a22; }
        .filter-row { margin-bottom:12px; display:flex; align-items:center; gap:8px; font-size:14px; flex-wrap:wrap; }
        .filter-row label { font-weight:600; color:#5a3a22; margin-right:4px; }
        .filter-row select { padding:6px 10px; border-radius:4px; border:1px solid #ced4da; font-size:13px; }
        .bulk-actions { margin-top:8px; margin-bottom:4px; }
        .bulk-actions .btn-danger { background:#b23b26; }
        .bulk-actions .btn-danger:hover { background:#8c2f1d; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="page-title">City Schools Division of Cabuyao</div>
    <div class="card">
        <div class="card-header">
            <h1>Personnel Management</h1>
            <div>
                <a href="personnel_import.php" class="button-link">Import CSV</a>
                <a href="index.php" class="button-link btn-secondary">Search Page</a>
                <a href="personnel_form.php" class="button-link">Add Personnel</a>
            </div>
        </div>
        <div class="card-body">
            <form method="get" action="" class="filter-row">
                <div>
                    <label for="office_id">Filter by Office:</label>
                    <select name="office_id" id="office_id" onchange="this.form.submit()">
                        <option value="0">All Offices</option>
                        <?php if ($officesFilter && $officesFilter->num_rows > 0): ?>
                            <?php while ($of = $officesFilter->fetch_assoc()): ?>
                                <option value="<?php echo $of['id']; ?>" <?php echo ($selectedOfficeId == $of['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($of['office_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="designation">Filter by Designation:</label>
                    <select name="designation" id="designation" onchange="this.form.submit()">
                        <option value="">All Designations</option>
                        <?php if ($designationsFilter && $designationsFilter->num_rows > 0): ?>
                            <?php while ($dg = $designationsFilter->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($dg['designation']); ?>" <?php echo ($selectedDesignation === $dg['designation']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dg['designation']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
            <?php if ($result && $result->num_rows > 0): ?>
                <form method="post" action="personnel_delete_bulk.php" onsubmit="return confirmBulkDelete('personnel');">
                    <div class="bulk-actions">
                        <button type="submit" class="btn btn-secondary btn-danger">Delete Selected</button>
                    </div>
                    <table>
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select_all_personnel" /></th>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Designation</th>
                            <th>Office</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" class="row-check-personnel" /></td>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td><?php echo htmlspecialchars($row['office_name']); ?></td>
                                <td class="actions">
                                    <a href="personnel_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                    <a href="personnel_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary" onclick="return confirm('Delete this record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <p class="text-muted">No personnel records yet. Click <strong>Add Personnel</strong> to create one.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
const selectAllPersonnel = document.getElementById('select_all_personnel');
if (selectAllPersonnel) {
    selectAllPersonnel.addEventListener('change', function () {
        const checks = document.querySelectorAll('.row-check-personnel');
        checks.forEach(ch => ch.checked = selectAllPersonnel.checked);
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
