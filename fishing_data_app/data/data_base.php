<?php
require_once "../dbc.php";
$dbo = connectDB();
// //DB接続に必要な引数作成
// $db  = 'mysql:dbname=gs_db;charset=utf8;port=3306;host=localhost';
// $user='root';
// $pwd ='';

// //DBに接続
// try{
//  $dbo = new PDO($db, $user, $pwd);
// }catch( PDOException $e){
//  echo json_encode(["db error" => "{$e->getMessage()}"]);
//  exit();
// }

//SQL作成
$sql = "SELECT * FROM fishing_db";

$stmt = $dbo->prepare($sql);
$status = $stmt->execute();

if($status = false){
    $error = $stmt->errorInfo();
    exit('sqlError'.$erroe[2]);
}else{
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    var_dump($result);
//    $wrapper_num = count($result);
//    echo "$wrapper_num";

//画像パス取得

// $Files=array();
// $files = getAllFile();
// $path_arr=[];
// foreach($files as $file){
//    array_push($path_arr,($file["file_path"]));
// };
// print_r($path_arr);

//アコーディオン生成
   $output ="";
   foreach($result as $record){
    //  $i = 0;  
    $output .= "<div class='card-header' id='heading{$record['id']}'>
                <h2 class='mb-0'>
                <button class='btn btn-link' type='button' data-toggle='collapse'
                    data-target='#collapse{$record['id']}' aria-expanded='true' 
                    aria-controls='collapseOne'>{$record['fish_name']}:{$record['fish_size']}cm&nbsp;&nbsp;&nbsp;{$record['insert_time']}</button>
                    <button class=\"clear-btn\" id=\"clear-btn{$record['id']}\" name=\"{$record['id']}\">削除</button></h2></div>
   
                <div class=\"collapse\" id=\"collapse{$record['id']}\" ariaLabelledby=\"heading{$record['id']}\" dataParent=\"#accordionExample\">
                <div class=\"card-wrapper card-wrapper{$record['id']}\" style=\"display:flex\">
                    <div class=\"card-body\">{$record['setsumei']}<br><span class=\"env\">気温:{$record['temp']}℃/ 水温:{$record['water_temp']}℃/風速(風向):{$record['win']}km/h/({$record['win_dir']})</span>
                    </div>
                    <!-- img file from DB -->
                    <div class=\"img-box\"><img id=\"img{$record['id']}\" src=\"../{$record['file_path']}\" alt=\"\" width=\"100%\" height=\"150px\"></div></div></div>";
                   
   }
}
// print($output);

?>

<!-- 以下HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data base</title>
    <meta name="descliption" content="a tool for fishing data">
    <meta name="format-detection" contetn="telephone-no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="css_memo.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/ja.js"></script>
</head>
<body>
        <!-- ここから、データベース反映 -->
    
        <h1 style="text-align: left;">Data Base</h1>
        <h3><a href="../fishing_data.html">fishig_data</a></h3>
   
    <div id="data-wrapper" class="database-wrapper" style="background-color:cadetblue">
    
     <!-- Div -->
        <div class="memo-wrapper" id="memo-wrapper">
            <div class="accordion" id="accordionExample">
                <div class="card" >
                    <!-- データ数に応じてアコーディオン挿入 -->
                    <?=$output?>


                </div> 
            </div>
        </div>          
    </div>
    
<!-- <script src="js_memo.js"></script> -->

  
            
            <!-- // const Div = document.createElement("div");
            // Div.className="card";
            // Div.innerHTML=`<div class='card-header' id='heading${i}'>
            //                 <h2 class='mb-0'><button class='btn btn-link' type='button' data-toggle='collapse' data-target='#collapse${i}' aria-expanded='true' aria-controls='collapseOne'>${datalist[i].name}:${datalist[i].size}\u3000\u3000\u3000${moment(datalist[i].day).format("YYYY年M月D日 hh:mmA")}</button>
            //                 <button class="clear-btn" id="clear-btn${i}" name="${i}">削除</button></h2></div>`;
            // Div.style.color="white";
    
            // const Div2 = document.createElement("div");
            // Div2.className="collapse ";
            // Div2.id=`collapse${i}`;
            // Div2.ariaLabelledby=`heading${i}`;
            // Div2.dataParent = "#accordionExample";
            // Div2.innerHTML=`<div class="card-wrapper card-wrapper${i}" style="display:flex"><div class="card-body">${datalist[i].desc}
            // <br><span class="env">気温:${datalist[i].temp}℃\u3000 水温:${datalist[i].wtemp}\u3000 風速(風向):${datalist[i].wind}km/h(${datalist[i].windir})</span></div><div class="img-box">
            // <img id="img{i}" src="" alt="" width="100%" height="150px">
            // </div></div>`;
            // //img-tag src退避${makeImg(datalist[i].img,i)}
            // dataWrapper.prepend(Div2);
            // dataWrapper.prepend(Div);
            
            // i++
            // }
            <div class="card">
                    <div class='card-header' id='heading1'>
                        <h2 class='mb-0'>
                            <button class='btn btn-link' type='button' data-toggle='collapse' data-target='#collapse1' aria-expanded='true' aria-controls='collapse1'>データ表示</button>
                            <button class="clear-btn" id="clear-btn1" name="1">削除</button>
                        </h2>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Div2 -->
        <!-- <div class="collapse" id="collapse1" ariaLabelledby="heading1" dataParent="#accordionExample">
            <div class="card-wrapper card-wrapper1" style="display:flex">
                <div class="card-body">
                     
                   
                    
                  <br><span class="env">気温:--℃/ 水温:--℃/風速(風向):--km/h/(windir)</span>
                </div>
                < img file from PHP -->
                <!-- <div class="img-box">
                    <img id="img1" src="" alt="" width="100%" height="150px">
                </div>
            </div>   
        </div>
    </div> -->


</body>
</html>