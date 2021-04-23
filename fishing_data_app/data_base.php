<?php
session_start();
require_once "dbc.php";

loginCheck();


$dbo = connectDB();

//SQL作成
$sql = "SELECT * FROM fishing_db";

$stmt = $dbo->prepare($sql);
$status = $stmt->execute();

if($status = false){
    $error = $stmt->errorInfo();
    exit('sqlError'.$erroe[2]);
}else{
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


//アコーディオン生成
   $output ="";
   foreach($result as $record){
    //  $i = 0;  
    $output .= "<div class='card-header' id='heading{$record['id']}'>
                <h2 class='mb-0'>
                <button class='btn btn-link' type='button' data-toggle='collapse'
                    data-target='#collapse{$record['id']}' aria-expanded='true' 
                    aria-controls='collapseOne'>{$record['fish_name']}:{$record['fish_size']}cm&nbsp;&nbsp;&nbsp;{$record['insert_time']}</button><a href='delete.php?id={$record['id']}&訂正'>
                    <button class=\"clear-btn\" id=\"clear-btn{$record['id']}\" name=\"{$record['id']}\">訂正</button></a>
                    <a href='delete.php?id={$record['id']}&削除'>
                    <button class=\"clear-btn\" id=\"clear-btn{$record['id']}\" name=\"{$record['id']}\">削除</button></a></h2></div>
   
                <div class=\"collapse\" id=\"collapse{$record['id']}\" ariaLabelledby=\"heading{$record['id']}\" dataParent=\"#accordionExample\">
                <div class=\"card-wrapper card-wrapper{$record['id']}\" style=\"display:flex\">
                    <div class=\"card-body\">{$record['setsumei']}<br><span class=\"env\">気温:{$record['temp']}℃/ 水温:{$record['water_temp']}℃/風速(風向):{$record['win']}km/h/({$record['win_dir']})</span>
                    </div>
                    <!-- img file from DB -->
                    <div class=\"img-box\"><img id=\"img{$record['id']}\" src=\"./{$record['file_path']}\" alt=\"\" width=\"100%\" height=\"150px\"></div></div></div>";
                   
   }
}

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
        <h3><a href="./app_top.php">fishig_data</a></h3>
   
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
    
<script>

    document.addEventListener("click",function(e){
       
            if(e.target.innerHTML==='削除'){
                console.log(e.target.innerHTML);
                console.log(e.target.name)
                
                let xhr = new XMLHttpRequest();
                xhr.open('POST','delete.php',true);
                // request.responseType ='html';
                // request.addEventListener('load',function(response){
                //     console.log(response);
                // });
                request.send();
            };
    })
</script>
</body>
</html>