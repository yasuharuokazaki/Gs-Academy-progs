<?php
session_start();
// var_dump($_SESSION);
// var_dump($_FILES) ;
var_dump($_POST);


//画像をupload/imagesフォルダにアップロード

if(!isset($_POST["save"])){
   //pathinfo()は連想配列を返す
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
      //アップロードされたファイルの拡張子が、こちらが指定した拡張子か確認
      $err_msg = '画像以外のファイルはアップロードできません。';
   }elseif(!@getimagesize($_FILES['img']['tmp_name'])){
      //拡張子の偽造対策⇒画像サイズを取得する関数でサイズが取得できるかチェック
      $err_msg = 'ファイルの内容が画像ではありません。';
   }else{
      $src= $_FILES['img']['tmp_name'];
      $dest = mb_convert_encoding($_FILES['img']['name'],'SJIS-WIN','UTF-8');
      if(!move_uploaded_file($src,'upload/images/'.$dest)){
         $err_msg = 'アップロードに失敗しました。';
      }
   }

// exit();





// DBに各種情報を渡す。画像に関しては、保存先のパス情報をDBに格納
  $data = '<ul>' ;
    
    $data.= "<li>対象魚：{$_POST['name']}</li><input name='name' type='hidden' value={$_POST['name']}>";
    $data.= "<li>サイズ：{$_POST['size']}</li><input name='size' type='hidden' value={$_POST['size']}>";
    $data.= "<li>説明：{$_POST['text']}</li><input name='text' type='hidden' value={$_POST['text']}>";
   //  $data.= "<li>{$_POST['img']}</li>";
    $data.= "<li>気温：{$_POST['temp']}</li><input name='temp' type='hidden' value={$_POST['temp']}>";
    $data.= "<li>水温：{$_POST['water_temp']}</li><input name='water_temp' type='hidden' value={$_POST['water_temp']}>";
    $data.= "<li>風向き：{$_POST['win_dir']}</li><input name='win_dir' type='hidden' value={$_POST['win_dir']}>";
    $data.= "<li>風速：{$_POST['win']}</li><input name='win' type='hidden' value={$_POST['win']}>";
    $data.= "<li>画像名：{$_FILES['img']['name']}</li><input name='file_name' type='hidden' value={$_FILES['img']['name']}>";
    $data.= "<input name='file_path' type='hidden' value='upload/images/"."{$_FILES['img']['name']}"."'>";
   //公開許可するか
   if(isset($_POST['op_flag'])){
    $data.= "<li>公開可否：可</li><input name='op_flag' type='hidden' value=1>";
   }else{
      $data.= "<li>公開可否：否</li><input name='op_flag' type='hidden' value=0>";
   }
   //ここまで
    $data.= "<input name='longitude' type='hidden' value={$_POST['longitude']}>";
    $data.= "<input name='latitude' type='hidden' value={$_POST['latitude']}></ul>";
}

// exit("test");

if(isset($_POST['save'])==true){
   // var_dump($_POST);
   // exit();
require_once "./dbc.php";
//DB接続
$dbo=connectDB();


//SQLを作成して実行($_POSTで受け取ったものをそのままSQRの中に埋め込むのは危険！⇒バインドバリューを用いて対応！)
$sql = "INSERT
 INTO fishing_db(id,user_key,fish_name,fish_size,setsumei,temp,water_temp,win_dir,win,file_name,file_path,longitude,latitude,op_flag)
 VALUES(NULL,:user_key,:name,:size,:text,:temp,:water_temp,:win_dir,:win,:file_name,:file_path,:longitude,:latitude,:op_flag)";

$stmt=$dbo -> prepare($sql);//DBインスタンスのprepareメソッド（＝引数として渡したSQRを実行するメソッド）を実施。
$stmt->bindValue(':user_key',$_SESSION['user_key'],PDO::PARAM_INT);
$stmt->bindValue(':name',$_POST['name'],PDO::PARAM_STR);
$stmt->bindValue(':size',$_POST['size'],PDO::PARAM_INT);
$stmt->bindValue(':text',$_POST['text'],PDO::PARAM_STR);
// $stmt->bindValue(':img' ,$_POST['img' ]);
$stmt->bindValue(':temp',$_POST['temp'],PDO::PARAM_INT);
$stmt->bindValue(':water_temp',$_POST['water_temp'],PDO::PARAM_INT);
$stmt->bindValue(':win_dir',$_POST['win_dir'],PDO::PARAM_STR);
$stmt->bindValue(':win',$_POST['win'],PDO::PARAM_INT);
//FILESを書き換えてます！！$_FILES['img']['name']=>$_POST['file_name']
$stmt->bindValue(':file_name',$_POST['file_name'],PDO::PARAM_STR);
//FILESを書き換えてます！'upload/images/'.$_FILES['img']['name']=>$_POST['file_path']
$stmt->bindValue(':file_path',$_POST['file_path'],PDO::PARAM_STR);
$stmt->bindValue(':longitude',$_POST['longitude'],PDO::PARAM_STR);
$stmt->bindValue(':latitude',$_POST['latitude'],PDO::PARAM_STR);
$stmt->bindValue(':op_flag',$_POST['op_flag'],PDO::PARAM_INT);
// $stmt->bindValue(':op_flag',$_POST['op_flag'],PDO::PARAM_INT);
$stmt->execute();//executeメソッド＝実行しろ！っていう命令

// }
unset($dbo);
}


// exit();



?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<style>
   ul{
      list-style: none;
   }
</style>
   <title>保存内容確認</title>
</head>
<body>
<div class="alert alert-success" role="alert">
<form action="" method="post">   
<?php if(!isset($_POST["save"])){
  print "<h4 class='alert-heading'>次の内容で保存します。</h4><p>{$data}</p>";
 }else{
  print "<h2>保存しました</h2>";
 }?>
  
  
  <hr>
  <p class="mb-0">
 
 <?php if(!isset($_POST["save"])){
  print "<input type='submit' name='save' value='保存する'>";}?>
  </form>
  <form action="./app_top.php">
  <input type="submit" name="correct" value="戻る">
  </form>

<!-- 
  <a href="./index.html">戻る</a> -->
  </p>
</div>
</body>
</html>

