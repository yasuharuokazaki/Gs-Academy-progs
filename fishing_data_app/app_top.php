<?php
session_start();

require_once "dbc.php";

loginCheck();
var_dump($_SESSION);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishing Data</title>
    <meta name="descliption" content="a tool for fishing data">
    <meta name="format-detection" contetn="telephone-no"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="css_memo.css">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/ja.js"></script>

<style type="text/css">
  img{height:70px; width:70px}
</style>
</head>

<body class="bg-light">
<h1 style="text-align: left;">Fishing Data</h1>
<span style="size: 5px;">USER:<?=$_SESSION['name']?></span>

<!-- ログイン画面 -->
<div>
  

   <a name="location" target="_blank" href="get_location_test.php"><h2>地図情報</h2></a>
   <a href="logout.php">ログアウト</a>

   <!-- <input type="text" name="username" id="uname" placeholder="user name"> -->
 
</div>


<!-- 記録フォーム -->

  <!-- <input name="test" type="text"> -->
 

  <div class="base-wrapper" >
          <div class="info-wrapper">

          <form action="data_get.php" enctype="multipart/form-data" method="post">
            <!-- 魚の名前・サイズ -->
              <p>Target-name | size</p>
              <div class="input-group">
                 <!-- name -->
                  <input name="name" type="text" id="target-name"  placeholder="target-name" aria-label="First name" class="form-control" flex-basis="70%" required>
                  <!-- size  -->
                  <input name="size" type="text" id="target-size"  placeholder="target-size(cm)"aria-label="Last name" class="form-control" flex-basis="30%">
              </div>
              
              <!-- 説明 -->
              <p>Descliption</p>
              <div class="input-group">
                  <textarea name="text" id="target-descliption" class="form-control" aria-label="descliption"></textarea>
              </div>
              
              

              <!-- 画像 -->
              <p>Image</p>
              <div class="input-group">
                <!-- <form action="data_get.php" endctype="multipart/form-data" method="post"> -->
                  <!-- <input type="hidden" name="max_file_size" value="1000000"> -->
                  <input id="inputGroupFile04" name="img" type="file" value="Upload" class="form-control"  accept="image/*" capure=""  aria-describedby="inputGroupFileAddon04" aria-label="Upload" >
                  <input id="lat" type="hidden" name="latitude" value="">
                  <input id="lon" type="hidden" name="longitude" value="">
              </div>
              
              <div id="image" style="width:70px;height:70px;margin-top:5px">
              </div>
              
          </div>
          
          <!-- フィールド情報 -->
          <div class="env-wrapper">
              <p>Environment</p>

              <!-- 気温<span id="tempSpn">--℃</span> -->
              <label for="customRangeTemp" class="form-label">Temp </label>
                  <input name="temp" type="text" class="form-range" id="customRangeTemp" style="background-color: #ffffff"
                  placeholder=" --℃"> 
              
              <!-- 水温<span id="WtempSpn">--℃</span> -->
              <label for="customRangeWtemp" class="form-label" style="margin-top: 5px;margin-bottom:10px">Water temp</label>
                  <input name="water_temp" type="text" class="form-range" id="customRangeWtemp" style="background-color: #ffffff"
                  placeholder=" --℃">

              <!-- 風<span id="winSpn">-km/h</span>-->
              <label for="customRangewinSpn" class="form-label wind-form" style="margin-top: 10px;margin-bottom:2px">Wind<span class="dirtext"style="margin-top: 10px;">Direction</span>
                <select name="win_dir" class="winDir" id="winDir" style="margin-top: 10px;margin-left:5px">
                  <option value="北">北</option>
                  <option value="東">東</option>
                  <option value="南">南</option>
                  <option value="西">西</option>
                </select>
              </label>
                <div class="wind-form">
                  <input name="win" type="text" class="form-range" id="customRangewinSpn" style="background-color: #ffffff"
                  placeholder=" --km/h">
                </div>
                
          </div>
          
  </div>
  
      <div class="button d-flex justify-content-center">
      
        <button id="btn-save" type="submit" class="btn btn-primary btn-lg">データ保存</button>
         <p style="margin-left: 10px;">公開可否<br>
          <label><input type="checkbox" value="1" name="op_flag" checked>許可</label>
         </p>
      </div>
   </form> 
   

<!-- データベースリンク -->
<h2 style="text-align: left;"><a href="data_base.php"> Data Base</a></h2>
 


<!-- 以下js -->

<!-- load-image  -->
<script src="load_img/load-image.all.min.js"></script>


<script>
  
 const Img = document.querySelector("#inputGroupFile04");
 const imgDiv = document.querySelector("#image");
 const lonData = document.querySelector("#lon");
 const latData = document.querySelector("#lat");

 let LocationInfo;
 let Lon;
 let Lat;
 let longitude;
 let latitude;

 let imgData={};
 let locationData=[];

 Img.onchange = function(){
   loadImage(
     this.files[0],
     function(img,data){
      imgDiv.innerHTML = "";
      imgDiv.appendChild(img);

      let gpsInfo = data.exif && data.exif.get('GPSInfo');
      console.log(data);
      if(gpsInfo){
        
        imgData = gpsInfo.getAll();
       let dmsLon = imgData.GPSLongitude;
       let dmsLat = imgData.GPSLatitude;
       let dmsLonArray = dmsLon.split(",");
       let dmsLatArray = dmsLat.split(",");
       let degLon = Number(dmsLonArray[0])+Number(dmsLonArray[1])/60+Number(dmsLonArray[2])/3600;
       let degLat = Number(dmsLatArray[0])+Number(dmsLatArray[1])/60+Number(dmsLatArray[2])/3600;
       locationData.push(degLon);
       locationData.push(degLat);
       lonData.value=locationData[0];
       latData.value=locationData[1];
      }else{alert("faled to get ")}},
      { 
                  maxWidth:150,
                  maxHeight:150,
                  contain  : true,
                  meta     : true 
              }
     
   )
 }

 

  </script>
 

</body>
</html>