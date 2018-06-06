<?php
header('content-type:text/html;charset=utf-8');
$username = $_POST['username'];
$age = $_POST['age'];
$arr = [
    "username" => $username,
    "age" => $age
];
echo json_encode($arr);
?>