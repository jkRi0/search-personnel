<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $ids = array_filter($ids);
    if (!empty($ids)) {
        $idList = implode(',', $ids);
        $conn->query("DELETE FROM personnel WHERE id IN ($idList)");
    }
}

header('Location: personnel_list.php');
exit;
