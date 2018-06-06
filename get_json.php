<?php
header('content-type:text/html;charset=utf-8');
$username = $_GET['username'];
$age = $_GET['age'];
$arr = [
    "username"=>$username,
    "age"=>$age
];
echo json_encode($arr);
?>