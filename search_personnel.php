<?php
require_once 'db.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($search !== '') {
    $sql = "SELECT p.id, p.full_name, o.office_name
            FROM personnel p
            JOIN offices o ON p.office_id = o.id
            WHERE (p.full_name LIKE ?
                   OR o.office_name LIKE ?
                   OR SOUNDEX(p.full_name) = SOUNDEX(?))
            ORDER BY p.full_name";

    $stmt = $conn->prepare($sql);
    $like = "%" . $search . "%";
    $stmt->bind_param('sss', $like, $like, $search);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Personnel Search</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f7f1e8, #fffaf3);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .header-bar {
            background: #7b4a2e;
            color: #ffffff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .header-bar h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .header-actions a {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            color: #7b4a2e;
            background: #ffffff;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 500;
        }
        .header-actions a:hover {
            background: #f3e4d4;
        }
        .content {
            padding: 20px 24px 24px;
        }
        .search-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .search-row label {
            font-size: 13px;
            color: #555;
            flex-basis: 100%;
        }
        form {
            margin-bottom: 16px;
        }
        .search-input-wrapper {
            display:flex;
            flex:1;
            min-width:220px;
            align-items:center;
        }
        .search-input-wrapper input[type="text"] {
            flex: 1;
            padding: 8px 10px;
            font-size: 14px;
            border-radius: 4px 0 0 4px;
            border: 1px solid #ced4da;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            border-right: none;
        }
        .clear-btn {
            padding: 8px 10px;
            border: 1px solid #ced4da;
            border-left: none;
            background:#f3e4d4;
            color:#5a3a22;
            cursor:pointer;
            border-radius:0 4px 4px 0;
            font-size:13px;
        }
        .clear-btn:hover {
            background:#e4d0b9;
        }
        .search-input-wrapper input[type="text"]:focus {
            border-color: #8b5a2b;
            box-shadow: 0 0 0 2px rgba(139, 90, 43, 0.15);
        }
        button {
            padding: 8px 18px;
            background: #8b5a2b;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
        }
        button:hover {
            background: #6d4420;
            box-shadow: 0 2px 6px rgba(109, 68, 32, 0.35);
        }
        .helper-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }
        .search-summary {
            font-size: 14px;
            margin: 8px 0 0;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 11px;
            border-radius: 999px;
            background: #f3e4d4;
            color: #5a3a22;
            margin-left: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            padding: 9px 10px;
            border-bottom: 1px solid #edf0f4;
            font-size: 14px;
        }
        th {
            background: #f3e4d4;
            text-align: left;
            font-weight: 600;
            color: #5a3a22;
        }
        tr:nth-child(even) td {
            background: #fcfdff;
        }
        .no-results {
            margin-top: 10px;
            color: #6c757d;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .content {
                padding: 16px;
            }
            th, td {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="container">
        <div class="header-bar">
            <h1>Personnel Directory Search</h1>
            <div class="header-actions">
                <a href="personnel_list.php">Manage Personnel</a>
            </div>
        </div>
        <div class="content">
            <form method="get" action="" onsubmit="return validateSearch();">
                <div class="search-row">
                    <label for="searchInput">Search by personnel name or office name</label>
                    <div class="search-input-wrapper">
                        <input type="text" name="q" id="searchInput" placeholder="e.g. Juan, IT Department, Human Resources" value="<?php echo htmlspecialchars($search); ?>" />
                        <button type="button" class="clear-btn" onclick="clearSearch()">×</button>
                    </div>
                    <button type="submit">Search</button>
                </div>
                <p class="helper-text">Tip: You can type part of a name or office, the system will find close matches.</p>
            </form>

            <?php if ($search === ''): ?>
                <p class="no-results">Start by entering a name or office above, then click <strong>Search</strong>.</p>
            <?php else: ?>
                <p class="search-summary">Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong><span class="badge">search</span></p>
                <?php if ($results && $results->num_rows > 0): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Office</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['office_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-results">No personnel found. Try using a shorter keyword or a different name/office.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function validateSearch() {
    const input = document.getElementById('searchInput');
    if (input.value.trim() === '') {
        alert('Please type something to search.');
        input.focus();
        return false;
    }
    return true;
}

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        input.focus();
    }
}
</script>
</body>
</html>
