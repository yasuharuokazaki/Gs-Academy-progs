<?php
var_dump($_POST);
$id = $_POST['id'];

require_once "dbc.php";

$dbo = connectDB();

$sql='DELETE FROM fishing_db WHERE id=:id';
$stmt=$dbo->prepare($sql);
$stmt->bindValue(':id',$_POST['id'],PDO::PARAM_INT);
$status=$stmt->execute();

if($status==false){
    $error=$stmt->errorInfo();
    exit("ErrQuery:".$error[2]);
}else{
    header("Location:data_base.php");
    exit;
}
?>