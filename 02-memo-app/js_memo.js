
// 環境情報の取得
const temp = document.querySelector("#customRangeTemp");
let tempSpn =document.querySelector("#tempSpn");
temp.addEventListener("input",function(){
    // console.log(temp.value);
    tempSpn.textContent = temp.value+'℃';
});

const Wtemp = document.querySelector("#customRangeWtemp");
let WtempSpn =document.querySelector("#WtempSpn");
Wtemp.addEventListener("input",function(){
    // console.log(temp.value);
    WtempSpn.textContent = Wtemp.value+'℃';

});

const wind = document.querySelector("#customRangewinSpn");
let winSpn =document.querySelector("#winSpn");
wind.addEventListener("input",function(){
    // console.log(temp.value);
    winSpn.textContent = wind.value+'km/h';
});

// const direction = document.querySelector("#winDir");
// const list = document.querySelector(".dirlist");
// direction.onmouseover = function(){
//  list.style="display:block";
// }

//  list.onmouseout = function(e){
//     if(e.formElement==="ul"){
//         list.style="display:block";
//     }else{
//         list.style="display:none";
//     }
//  } 


// セーブ機能のための変数
const saveButton = document.querySelector("#btn-save");
const targetName = document.querySelector("#target-name");
const targetSize = document.querySelector("#target-size");
const targetDesc = document.querySelector("#target-descliption");
const targetImg = document.querySelector("#img-data");
const imgDataBtn =document.querySelector("#img-data-button");
const enTemp = document.querySelector("#customRangeTemp");
const enWtemp = document.querySelector("#customRangeWtemp");
const enWind = document.querySelector("#customRangewinSpn");
const enWindDir = document.querySelector("#winDir");
let $d = new Date().valueOf();
console.log($d);


// セーブ押下時に入力欄空にする関数
function Clear(){
    targetName.value = "";
    targetSize.value ="";
    targetDesc.value ="";
    tempSpn.textContent ="--℃";
    WtempSpn.textContent ="--℃";
    winSpn.textContent ="--km/h";
    enWindDir.value="";

}

// セーブ押下時の関数＝ストレージに情報保存
saveButton.onclick=function(){

    if(targetName.value==''){
        alert('ターゲットの名前を入れてね！');
    }else{
    const saveData={
        name:targetName.value,
        size:targetSize.value,
        desc:targetDesc.value,
        day:Date().valueOf(),
        temp:enTemp.value,
        wtemp:enWtemp.value,
        wind:enWind.value,
        windir:enWindDir.value
    
    };
    let n = localStorage.length;
    localStorage.setItem(`data-set${n+1}`,JSON.stringify(saveData) );
    n++;
    console.log(saveData)
    // localStorage.setItem("size",targetSize.value);
    // localStorage.setItem("desc",targetDesc.value);
    // console.log(targetImg.getAttribute('src'));
    // console.log(targetName.value);
    // console.log(targetSize.value);
    alert("セーブしました。");
    Clear();
   }
}

// ストレージクリアボタン関数
const delButton = document.querySelector("#btn-clear");
delButton.onclick = function(){
    localStorage.clear();
} 


// ストレージ上のデータ数で、データ表示アコーディオン生成

$("#btn-something").on("click",(e)=>{
    renderData(e);  
});

// アコーディオン生成関数
let dataWrapper = document.getElementById("data-wrapper");
function renderData(){
    if(localStorage.length<=0){
        dataWrapper.innerHTML = "<div style='color:white'>データが登録されていません。まずは魚を釣りましょう！</div>";
    }else{

        let n = localStorage.length;
        let i = 1;
        dataWrapper.innerHTML = "";
        let datalist=[""];
        for(let i =1;i<=n;i++){ 
            let dataSet = localStorage.getItem(`data-set${i}`);
            DataSet = JSON.parse(dataSet);
            datalist.push(DataSet);                                  
        };

        // console.log(datalist);

        while(i<=n){
           
        const Div = document.createElement("div");
        Div.className="card";
        Div.innerHTML=`<div class='card-header' id='heading${i}'>
                        <h2 class='mb-0'><button class='btn btn-link' type='button' data-toggle='collapse' data-target='#collapse${i}' aria-expanded='true' aria-controls='collapseOne'>${datalist[i].name}:${datalist[i].size}\u3000\u3000\u3000${moment(datalist[i].day).format("YYYY年M月D日 hh:mmA")}</button>
                        <button class="clear-btn" id="clear-btn${i}" name="${i}">削除</button></h2></div>`;
        Div.style.color="white";

        const Div2 = document.createElement("div");
        Div2.className="collapse ";
        Div2.id=`collapse${i}`;
        Div2.ariaLabelledby=`heading${i}`;
        Div2.dataParent = "#accordionExample";
        Div2.innerHTML=`<div class="card-wrapper card-wrapper${i}" style="display:flex"><div class="card-body">${datalist[i].desc}
        <br><span class="env">気温:${datalist[i].temp}℃\u3000 水温:${datalist[i].wtemp}\u3000 風速(風向):${datalist[i].wind}km/h(${datalist[i].windir})</span></div><div class="img-box">Images is here.</div></div>`;
        // console.log(Div2);

        // const Div3 = document.createElement("div");
        // Div3.id=`collapseWrap${i}`;
        
        // Div3.innerHTML=Div2;
        // Div2.innerHTML=Div;
        // Div.appendChild(Div2);
        dataWrapper.prepend(Div2);
        dataWrapper.prepend(Div);
        
        i++
        }
    }
}


// 個別データ削除ボタン
const html = document.querySelector("html");
      html.addEventListener("click",function(e){
       let target = e.target;
    //    console.log(target.name);
       if(target.className ==="clear-btn"){
          let targetDiv = target.offsetParent;
          let targetNum = target.name;
          const targetDiv2=document.querySelector(`#collapse${targetNum}`)
                  
          targetDiv.remove();
          targetDiv2.remove(); 

       }
      })




    //   以下実験コード
      "btn-save"
      "btn-something" 
      "btn-clear" 
      
      
      // $("#btn-save").on("click",function(){
      //  let targetName = $("#target-name").val();
      //  let targetSize = $("#target-size").val();
      // localStorage.setItem(key="name",value=targetName) ;
      // localStorage.setItem(key="size",value=targetSize) ;
      // alert("セーブしました。");
      // // })
      
      // $("#btn-clear").on("click",function(){
      //     $("#target-name").val("");
      //     $("#target-size").val("");
      // })
      // let memoWrapper = document.querySelector("#accordionExample");
// let memoCard = document.querySelector("#Card");
// let someBtn = document.querySelector("#btn-something");
// $(document).on("click", ".clear-btn", function(e){
//     // alert("2つ目の要素クリック");
//     console.log(e);
// });
