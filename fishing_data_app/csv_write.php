<?php
session_start();
require_once "dbc.php";

loginCheck();
// var_dump($_SESSION);

//csvファイルの作成
$file_path="data.csv";
$csv_title=["fish_name","temp","water_temp","longitude","latitude","insert_time"];

foreach($csv_title as $key => $value){
    $header[]=mb_convert_encoding($value,'SJIS-win', 'UTF-8');

}

$file= new SplFileObject($file_path,'w');
$file->fputcsv($header,',');


//DBからデータ転記
$dbo=connectDB();

$sql="SELECT fish_name,temp,water_temp,longitude,latitude,insert_time FROM fishing_db";

$stmt = $dbo->prepare($sql);
$status=$stmt->execute();

if($status = false){
     $error = $stmt->errorInfo();
     exit('sqlError'.$error[2]);
}else{
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

foreach($result as $line){
    // foreach($line as $key){
      
        // for($n=0;$n<count($key);$n++){
            $file->fputcsv($line,',') ;
        // }
       
    
//    print_r($line);
//    print "<br>";
}

$dbo=null;

//分析シートへ
print "<h3>追加データをCSVファイルに追記しました。</h3>";
print "<a href='app_top.php'>トップ画面へ</a>";
print "<a href='statistics.html' style='margin-left:50px'>解析画面へ</a>";