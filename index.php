<?php
error_reporting(E_ALL);
print_r($_POST);
print_r($_GET);
require_once ('dbConnect.php');
$select = "SELECT * FROM tasks";
require_once ('form.php')
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
<form method="post" action="index.php">
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