<?php
$addButton = 'Добавить';
if($_GET) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        $delPrep = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $delPrep->execute([$id]);
        $description = $delPrep->fetch()['description'];
    }
    if ($_GET['action'] === 'done') {
        $donePrep = $pdo->prepare("UPDATE tasks SET is_done = TRUE WHERE id = ? LIMIT 1");
        $donePrep->execute([$id]);
        $description = $donePrep->fetch()['description'];
    }
    if ($_GET['action'] === 'edit') {
        $idPrep = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $idPrep->execute([$id]);
        $description = $idPrep->fetch()['description'];
        $addButton = 'Сохранить';
    }
}
if (isset($_POST['add'])) {
    $desc = $_POST['description'];
    $id = $_POST['id'];
    if ($id) {
        $editPrep = $pdo->prepare("UPDATE tasks SET description = ? WHERE id = ? LIMIT 1");
        $editPrep->execute([$desc, $id]);
    } else {
        $addPrep = $pdo->prepare("INSERT INTO tasks (description, is_done, date_added) VALUES (?, ?, CURRENT_TIMESTAMP)");
        $addPrep->execute([$desc, false]);
    }
}
$allowedSort = ['description', 'date_added', 'is_done'];
if (isset($_POST['sort'])) {
    if(array_search($_POST['sortBy'], $allowedSort) !== false) {
        $sortBy = addslashes($_POST['sortBy']);
        $select .= " ORDER BY $sortBy";
    }
}
?>