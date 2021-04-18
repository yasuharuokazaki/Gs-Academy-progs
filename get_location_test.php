<?php
require_once "dbc.php";
$imgs="";
$dbo=connectDB();

$sql = "SELECT * FROM fishing_db ORDER BY id DESC ";
$stmt=$dbo->prepare($sql);
$stmt->execute();
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    $imgs .="<tr><td>".$row["fish_name"]."</td><td>".$row["file_name"]."</td></tr>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

               <!-- JavaScript-Loading-Image -->
               <!-- https://github.com/blueimp/JavaScript-Load-Image -->
    <!-- load-image  -->
    <script src="load_img/load-image.all.min.js"></script>
    <!-- required to parse Exif tags and cross-browser image orientation -->
    <script src="load_img/load-image-exif.js"></script>
    <!-- required to display text mappings for Exif tags -->
    <script src="load_img/load-image-exif-map.js"></script> 

              <!-- Bing-Map -->
              <!-- https://docs.microsoft.com/en-us/bingmaps/v8-web-control/creating-and-hosting-map-controls/?toc=https%3A%2F%2Fdocs.microsoft.com%2Fen-us%2Fbingmaps%2Fv8-web-control%2Ftoc.json&bc=https%3A%2F%2Fdocs.microsoft.com%2Fen-us%2FBingMaps%2Fbreadcrumb%2Ftoc.json -->
    <!-- refer Bing-Map -->
    <script type='text/javascript' src='http://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=Ar6rtSJI8k1ecF2oV15DK_0sod9DgO7ytoCh919_LgSpD_IjmFtqidoXH0RjqtpT' async defer></script>
    <!-- refer Bmap-file -->
    <script src="load_img/BmapQuery.js"></script>
    <style type="text/css">
      img{height:70px; width:70px}
    </style>
    <title>get lon&lat</title>
</head>
<body>
  <section style="display:flex;flex-flow: row-reverse">
   <div id="img_wrap" style="width:350px">
    <!-- get img file -->
    <form action="">
    <input id="getfile" type="file">
    <button type="submit"></button>
    </form>
    <!-- space to display img -->
    <!-- <div id="image" style="width:70px;height:70px;border:2px solid"></div> -->
    <table>
      <?= print $imgs ?>
    </table>

    <!-- table to display lat&lon -->
    <!-- <table> -->
        <!-- <tr>
           <th>緯度(latitude)</th>
           <td id="lat">"latitude is here"</td>
        </tr>
        <tr>
            <th>経度(longitude)</th>
            <td id="lon">"longitude is here"</td>
        </tr> -->
    <!-- </table> -->
    </div>
    <!-- space to display map -->
     <div id="myMap" style='position:relative;width:600px;height:400px;'>
    
  </div> 
    
  </section>
 
    <!-- Script is follow -->

<script>

    //set elements into valiables
      // const lonTable = document.querySelector("#lon");
      // const latTable = document.querySelector("#lat");
      const Img    = document.querySelector("#getfile");
      const imgDiv = document.querySelector("#image");
      const imgWrap= document.querySelector("#img_wrap");
    //make valiables for accomodate some infomation
      let Locationinfo; 
      let Lon;
      let Lat;

    // valiables to accomodate the valiables that DMS(60進法) is changed into DEG（10進法）
      let longitude; //= Number(Lon[0])+Number(Lon[1])/60+Number(Lon[2])/3600;
      let latitude;  //= Number(Lat[0])+Number(Lat[1])/60+Number(Lat[2])/3600;

    // object-val to accomodate locationinfo
      let imgData={};      

    //use load-img(=fileAPI) at the time Img is changed => get lat & lon
      Img.onchange = function(){
         loadImage(
              this.files[0], //parameter for selected img
              //get img-obj & data including lon&lat
              function(img,data){
                  // imgDiv.innerHTML = "";  
                // imgDiv.appendChild(img)
                // imgWrap.appendChild(imgDiv) ;
                  //get exifdata of data-obj,then set the value into gpsInfo
                  let gpsInfo = data.exif && data.exif.get('GPSInfo');
                  console.log(data);
                  //if gpsInfo is return,get the values.if not, return alert "none".
                  if(gpsInfo){
                      imgData = gpsInfo.getAll();  
                      
                      //save lon and lat to localstrage  
                        localStorage.setItem("locationInfo",
                        JSON.stringify({lon:`${imgData.GPSLongitude}`,lat:`${imgData.GPSLatitude}`})
                        )
                  }else{alert("none")};

                    //get new lon&lat
                      let n = getLonLat();
                      Mapchange(n);
              },
              { 
                  maxWidth:150,
                  maxHeight:150,
                  contain  : true,
                  meta     : true 
              }
          )
      }

  //function to get lon&lat  
  function getLonLat(){
   
      Locationinfo=JSON.parse(localStorage.getItem("locationInfo"));
      Lon = Locationinfo.lon.split(",");
      Lat = Locationinfo.lat.split(",");
      longitude = Number(Lon[0])+Number(Lon[1])/60+Number(Lon[2])/3600;
      latitude  = Number(Lat[0])+Number(Lat[1])/60+Number(Lat[2])/3600;
       return [latitude,longitude];
   }
   let targetLat = getLonLat()[0];
   let targetLon = getLonLat()[1];

   

  //The following represents call-back-function for Bing-Map
    let map;
    let pin=[];
    let infobox;

  //when HTTP-respnse is returned,this function is ran.
   function GetMap(){
     // make map instance
     map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
       center: new Microsoft.Maps.Location(43.1489024,141.3840896),
       mapTypeId: Microsoft.Maps.MapTypeId.aerial,
       zoom:10
     });
    
  }

  //function to change map and make pins
  function Mapchange(n){
     //map change to target-area
      map.setView({
        mapTypeId:Microsoft.Maps.MapTypeId.aerial,
        center: new Microsoft.Maps.Location(n[0],n[1]),
        zoom:10
      });
     
     //make pushpins with message-box
     var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), null);
     var infobox = new Microsoft.Maps.Infobox(map.getCenter(), { title: 'Map Center', description: 'Seattle', visible: false });
      infobox.setMap(map);
      Microsoft.Maps.Events.addHandler(pushpin, 'click', function () {
          infobox.setOptions({ visible: true });
      });
      map.entities.push(pushpin);
    
   }
    

    </script>
   
</body>
</html>
