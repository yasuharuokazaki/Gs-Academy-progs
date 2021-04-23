<?php

//DB（file_db）への接続
function dbc(){
    $host = "localhost";
    $dbname = "file_db";
    $user = "root";
    $pwd ="";

    $dns = "mysql:host=$host;dbname=$dbname;charset=utf8;port=3306";

    try{
        $pdo = new PDO($dns,$user,$pwd,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
      
        return $pdo;
    }catch(PDOException $e){
        exit($e->getMessage());
    }
    
}

/*
*ファイルデータを保存
@param string $filename ファイル名
@param string $save_path 保存先のパス
@param bool $result 
*/
function fileSave($filename,$save_path,$description){
     $result = false;

     $sql="INSERT INTO file_table(file_name,file_path,description) VALUE(?,?,?)";
     
     try{
        $stmt=dbc()->prepare($sql);
        $stmt->bindValue(1,$filename);
        $stmt->bindValue(2,$save_path);
        $stmt->bindValue(3,$description);
        $result = $stmt->execute();
        return $result;
     }catch(\Exception $e){
        echo $e->getMessage();
        return $result;
     }

}

/*ファイルデータを取得
@return array $fileData 
*/
function getAllFile(){
    $sql ="SELECT * FROM file_table";

    $fileData=dbc()->query($sql);
    return  $fileData;
}

//データベース接続
function connectDB(){
// // サクラサーバ用DB接続情報(データベース名と、ユーザー名、パスワードを各変数に格納)


// //ローカルホスト用DB接続
$dbn = 'mysql:dbname=gs_db; charset=utf8;port=3306;host=localhost';//portとlocalhostは利用するサーバーによる。
$user='root';
$pwd='';

// DB接続(指定したDBにアクセスしてインスタンス生成⇒変数に格納)
try{
$db = new PDO($dbn,$user,$pwd);
return $db;
}catch(PDOException $e){
   print "接続エラー:{$e->getMessage()}";
}finally{
   // $db=null;
}
}

//loginチェック
function loginCheck(){
    if(!isset($_SESSION["session_id"]) || $_SESSION["session_id"]!=session_id()){
        echo "ログインしていません。";
        echo '<a href="app_register.php">新規登録</a>';
        exit();
    }else{
        session_regenerate_id(true);
        $_SESSION["session_id"]=session_id();
    
    }
}