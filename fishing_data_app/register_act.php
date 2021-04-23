<?php
//  var_dump($_POST);
//  exit();

 require_once "dbc.php";
 $username = $_POST["username"];
$password = $_POST["password"];

$dbo =connectDB();
$sql = 'SELECT COUNT(*) FROM users_table WHERE name=:username';

$stmt = $dbo->prepare($sql);
$stmt->bindValue(':username',$username,PDO::PARAM_STR);
$status = $stmt->execute();

if($status == false){
   $error = $stmt->errorInfo();
  echo json_encode(["error_msg:" => "{$error[2]}"]);
  exit();
}elseif($stmt->fetchColumn() > 0){
    echo "<p>すでに登録されているユーザです．</p>";
    echo '<a href="app_login.php">login画面へ戻る</a>';
    exit();

}

    $sql ='INSERT INTO users_table(id,name,password,admin,deleted,created_at,updated_at)VALUES (NULL,:username,:password,0,0,sysdate(),sysdate())';
    $stmt= $dbo->prepare($sql);
    $stmt->bindValue(':username',$username,PDO::PARAM_STR);
    $stmt->bindValue(':password',$password,PDO::PARAM_STR);
    $status = $stmt->execute();

    if($status==false){
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg:" => "{$error[2]}"]);
        exit();
    }else{
        echo "<p>登録しました！</p>";
        echo "<a href='app_login.php'>ログイン画面へ戻る</a>";
       
    }
    
?>