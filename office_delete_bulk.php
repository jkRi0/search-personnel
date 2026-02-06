<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $ids = array_filter($ids);
    if (!empty($ids)) {
        $idList = implode(',', $ids);
        // This may fail if offices are referenced by personnel due to foreign key constraints
        $conn->query("DELETE FROM offices WHERE id IN ($idList)");
    }
}

header('Location: offices_list.php');
exit;
