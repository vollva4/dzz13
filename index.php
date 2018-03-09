<?php
error_reporting(E_ALL);
/*$servername = "localhost";
$username = "avolvach";
$password = "neto1512";*/
$connect = require_once ('dbConnect.php');
$pdo = new PDO("mysql:host=$host;dbname=global;charset=utf8", $username, $pass);
if (!$pdo)
{
    die('Could not connect');
}
$select = "SELECT * FROM tasks";
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

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ДЗ к занятию 13 PHP</title>
    <style>
table {
    margin-top: 20px;
            border-collapse: collapse;
        }
        td, th {
    border: 1px solid black;
            padding: 5px 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<h1>Список дел на сегодня</h1>
<form method="post" action="/">
    <input type="hidden" name="id" value="<?= $_GET ? $_GET['id'] : "" ?>">
    <input placeholder="Описание задачи" name="description" value="<?= $_GET ? $description : "" ?>">
    <input type="submit" value="<?= $addButton ?>" name="add">
    <select name="sortBy">
        <option value="description">Описание</option>
        <option value="date_added">Дата добавления</option>
        <option value="is_done">Статус</option>
    </select>
    <input type="submit" value="Отсортировать" name="sort">
</form>
<table>
    <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>

<?php
$stmt = $pdo->prepare($select);
$stmt->execute();
$list = $stmt->fetchAll();
foreach ($list as $row) {
    $id = $row['id'];
    echo '<tr>
        <td>' . $row['description'] . '</td>
        <td>' . $row['date_added'] . '</td>
        <td>';
    if (intval($row['is_done']) === 1) {
        echo '<span style="color: darkgreen">Выполнено</span>';
    } elseif (intval ($row['is_done']) === 0) {
        echo '<span style="color: darkorange">В процессе</span>';
    } else
        echo '<span style="color: red">В неопределенном состоянии</span>';
    echo '</td>
            <td><a href="index.php?id=' . $id . '&action=edit">Редактировать</a>  
              <a href="index.php?id=' . $id . '&action=done">Выполнить</a>  
              <a href="index.php?id=' . $id . '&action=delete">Удалить</a></td>';
}
?>
</table>
</body>
</html>