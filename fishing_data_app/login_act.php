<?php

$username=$_POST['username'];
$password=$_POST['password'];

session_start();

require_once "dbc.php";

$pdo = connectDB();

$sql='SELECT * FROM users_table WHERE name=:username
AND password = :password AND deleted=0';

$stmt=$pdo->prepare($sql);
$stmt->bindValue(':username',$username,PDO::PARAM_STR);
$stmt->bindValue(':password',$password,PDO::PARAM_STR);
$status = $stmt->execute();

$val = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$val){
  echo "<p>ログイン情報に誤りがあります。</p>";
  echo "<a href='app_login.php'>戻る</a>";
  exit();
}else{
    $_SESSION=array();
    //連想配列に新要素を追加するのに、ブラケット構文を利用
    $_SESSION['session_id']=session_id();
    $_SESSION['admin']=$val['admin'];
    $_SESSION['name']=$val['name'];
    header('Location:app_top.php');
    exit();
}





?>