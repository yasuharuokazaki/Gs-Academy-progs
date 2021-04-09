<?php
// var_dump($_FILES) ;
// var_dump(phpinfo());

//画像をupload/imagesフォルダにアップロード
$ext = pathinfo($_FILES['img']['name']);
$perm = ['jpg','jpeg','png','gif'];

if($_FILES['img']['error']!== UPLOAD_ERR_OK){
   $msg = [
      UPLOAD_ERR_INI_SIZE=>'upload_max_filesizeの制限を超えています。',
      UPLOAD_ERR_FORM_SIZE=>'HTMLのMAX_FILE_SIZE制限を超えています。',
      UPLOAD_ERR_PARTIAL=>'ファイルが一部しかアップロードされていません。',
      UPLOAD_ERR_NO_FILE=>'ファイルはアップロードされませんでした。',
      UPLOAD_ERR_NO_TMP_DIR=>'一時保存フォルダが存在しません。',
      UPLOAD_ERR_CANT_WRITE=>'ディレクトリへの書き込みに失敗しました。',
      UPLOAD_ERR_EXTENSION=>'拡張モジュールによってアップロードが中断しました。'
   ];
   $err_msg = $msg[$_FILES['img']['error']];
}elseif(!in_array(strtolower($ext['extension']),$perm)){
  $err_msg = '画像以外のファイルはアップロードできません。';
}elseif(!@getimagesize($_FILES['img']['tmp_name'])){
   $err_msg = 'ファイルの内容が画像ではありません。';
}else{
   $src= $_FILES['img']['tmp_name'];
   $dest = mb_convert_encoding($_FILES['img']['name'],'SJIS-WIN','UTF-8');
   if(!move_uploaded_file($src,'upload/images/'.$dest)){
      $err_msg = 'アップロードに失敗しました。';
   }
}
// exit();
// echo "<h2>保存内容</h2>";

require_once "./dbc.php";


// DBに各種情報を渡す。画像に関しては、保存先のパス情報をDBに格納
  $data = '<ul>' ;

    $data.= "<li>{$_POST['name']}</li>";
    $data.= "<li>{$_POST['size']}</li>";
    $data.= "<li>{$_POST['text']}</li>";
   //  $data.= "<li>{$_POST['img']}</li>";
    $data.= "<li>{$_POST['temp']}</li>";
    $data.= "<li>{$_POST['water_temp']}</li>";
    $data.= "<li>{$_POST['win_dir']}</li>";
    $data.= "<li>{$_POST['win']}</li>";
    $data.= "<li>{$_FILES['img']['name']}</li></ul>";
     
 echo $data;

//DB接続
$dbo=connectDB();


//SQLを作成して実行($_POSTで受け取ったものをそのままSQRの中に埋め込むのは危険！⇒バインドバリューを用いて対応！)
$sql = "INSERT INTO fishing_db(id,fish_name,fish_size,setsumei,temp,water_temp,win_dir,win,file_name,file_path) VALUES(NULL,:name,:size,:text,:temp,:water_temp,:win_dir,:win,:file_name,:file_path)";

$stmt=$dbo -> prepare($sql);//DBインスタンスのprepareメソッド（＝引数として渡したSQRを実行するメソッド）を実施。
$stmt->bindValue(':name',$_POST['name']);
$stmt->bindValue(':size',$_POST['size']);
$stmt->bindValue(':text',$_POST['text']);
// $stmt->bindValue(':img' ,$_POST['img' ]);
$stmt->bindValue(':temp',$_POST['temp']);
$stmt->bindValue(':water_temp',$_POST['water_temp']);
$stmt->bindValue(':win_dir',$_POST['win_dir']);
$stmt->bindValue(':win',$_POST['win']);
$stmt->bindValue(':file_name',$_FILES['img']['name']);
$stmt->bindValue(':file_path','upload/images/'.$_FILES['img']['name']);

$stmt->execute();//executeメソッド＝実行しろ！っていう命令
// echo "<script>alert(\"保存しました！\")</script>";
// header("Location:./fishing_data.html");

//直接DBに画像保存
// if($_SERVER['REQUEST_METHOD'] != 'POST'){
//  //画像を取得
// }else{
//  //画像を保存
// //  var_dump($_FILES);
//   if(!empty($_FILES['img']['name'])){
//      $name = $_FILES['img']['name'];
//      $type = $_FILES['img']['type'];
//      $content = file_get_contents($_FILES['img']['tmp_name']);
//      $size = $_FILES['img']['size'];

//      $sql2 = "INSERT INTO my_image(image_id,image_name,image_type,image_content,image_size,created_at) VALUES(NULL,:image_name,:image_type,:image_content,:image_size,now())";
//      $stmt = $db->prepare($sql2);
//      $stmt->bindValue('image_name',$name,PDO::PARAM_STR);   
//      $stmt->bindValue('image_type',$type,PDO::PARAM_STR); 
//      $stmt->bindValue('image_content',$content,PDO::PARAM_STR); 
//      $stmt->bindValue('image_size',$size,PDO::PARAM_INT);   
//      $stmt->execute();
//   }
//   unset($db);
// }
unset($dbo);



// exit();



?>
<a href="./fishing_data.html">戻る</a>
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <p><a href="data/data_base.php">data_base</a></p> 
</body>
</html> -->