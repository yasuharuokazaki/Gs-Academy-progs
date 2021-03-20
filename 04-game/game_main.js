//debug
const DEBUG = true;

//size of screen
const SCREEN_W =180;
const SCREEN_H =320;

//size of canvas
const CANVAS_W = SCREEN_W*2;
const CANVAS_H = SCREEN_H*2;

//size of field
const FIELD_W = SCREEN_W*2;
const FIELD_H = SCREEN_H*2;

//number of star
const STAR_MAX = 300;

//game speed
const GAME_SPEED = 1000/50

//canvas get
let can = document.querySelector("#can");
let ctx = can.getContext("2d");

can.width  = CANVAS_W;
can.height = CANVAS_H;

//field(virtual screen) 
let vcan = document.createElement("canvas");
let vctx = vcan.getContext("2d");

vcan.width  = FIELD_W;
vcan.height = FIELD_H;

//camera location
let camera_x = 0;
let camera_y = 0;

//make stars
let star=[];
//生成したEnemyを入れる配列
let enemy=[];
//生成したmyMissileを入れる配列
let missile=[];
//生成した敵のAttackを入れる配列
let attack=[];



//キーボードの状態
let key =[];
document.onkeydown = function(e){
    key[e.keyCode] = true;
    console.log(e.keyCode);
}
document.onkeyup = function(e){
    key[e.keyCode]=false;
}


//戦闘機画像から使いたい画像の位置情報を抜き出すためのクラス
class Ship{
    constructor(x,y,w,h){
        this.x = x;
        this.y = y;
        this.w = w;
        this.h = h;
    }
}

//ミサイル画像から使いたい画像の位置情報を抜き出すためのクラス
class Missile{
    constructor(x,y,w,h){
        this.x = x;
        this.y = y;
        this.w = w;
        this.h = h;
    }
}


//背景の星のクラス
class Star{
    constructor(){
        this.x    = random(0,FIELD_W)<<8; //what does bit-operator do?
        this.y    = random(0,FIELD_H)<<8;
        this.vx   = 0;
        this.vy   = random(30,200);
        this.size = random(1,2);
    }
   
    draw(){
       let x = this.x>>8;
       let y = this.y>>8;
       if(x<camera_x||x>=SCREEN_W+camera_x||
           y<camera_y||y>=SCREEN_H+camera_y){
       return
       }else{
           vctx.fillStyle = random(0,2)!=0?"66f":"#8af";//三項演算子
           vctx.fillRect(this.x>>8,this.y>>8,this.size,this.size);
       }
    }
    
    update(){
       this.x += this.vx;
       this.y += this.vy;
       if(this.y>FIELD_H<<8){
           this.y=0;
           this.x =random(0,FIELD_W)<<8;
       }
    }
   }
   

//キャラクターのベースになるクラス
class CharaBase{
    constructor( snum ,x,y, vx,vy){
        this.sn = snum;
        this.x  = x;
        this.y  = y;
        this.vx = vx;
        this.vy = vy;
        this.kill = false;

    }
    update(){
        this.x += this.vx;
        this.y += this.vy; 
        //画面外に出たら描画を止める
        if(this.x<0 || this.x>FIELD_W<<8 || this.y<0 || this.y>FIELD_H<<8){
            this.kill = true;
        }
    }

    draw(){
        drawMissile( this.sn,this.x,this.y);
    }
}


//敵のクラス
class Enemy extends CharaBase{
    constructor(snum,x,y, vx,vy){
        super(snum,x,y,vx,vy);
        this.w = 20;
        this.h = 20;
        this.flag = false;
    }

    update(){
        super.update();  
        //戦闘機のⅹ座標と自分のｘ座標を比較して、近づくようにする
        if(!this.flag){
            if(Myship.x > this.x && this.vx<120){
                this.vx += 4;
            };
            if(Myship.x < this.x && this.vx>-120){
                this.vx -= 4;
            }
        }else{
            if(Myship.x < this.x && this.vx < 400){
                this.vx += 30;
            };
            if(Myship.x > this.x && this.vx > -400){
                this.vx -= 30;
            }
        }
       
        //自分のｙ座標と戦闘機のｙ座標を比較して接近後留まるようにする
        if( Math.abs( Myship.y - this.y ) < (100<<8) && !this.flag ){
            this.flag = true;
            let angle,cosx,siny;
            angle = Math.atan2( Myship.y - this.y ,Myship.x - this.x )
            cosx  = Math.cos(angle)*1000; //cos/sinは単位円におけるｘ、ｙを求めるものなので、攻撃のスピード1000を掛ける。
            siny  = Math.sin(angle)*1000;
            
            //flagがtrueの時に攻撃＝attack配列に攻撃画像を格納
            attack.push(new Attack(12,this.x,this.y,cosx,siny));
        }
        if( this.flag && this.vy > -800){
            this.vy -= 30;
        }
        
    }

    draw(){
        //drawMissile関数を利用
        super.draw();  
    }
}



//ミサイルのクラス 
class myMissile extends CharaBase{
    constructor(x,y, vx,vy){
    //親クラスから承継。引数を親に渡す。
        super(3,x-2550,y-3000,vx,vy); //myMissileの引数はx,y,vx,vyの4つだが、親クラスに渡す引数にはsnumに代入する数が必要。
        this.w = 4;
        this.h = 6;
    }

    update(){
        //親のメソッド承継  let i = obj.length-1; i>=0 ;i--
        super.update();      
        for( let i = 0; i<enemy.length ;i++){
            if( enemy[i].kill === false){
                if(checkHit(
                    this.x ,     this.y ,    this.w ,    this.h,
                enemy[i].x , enemy[i].y ,enemy[i].w ,enemy[i].h
                )){
                    enemy[i].kill = true;
                    this.kill     = true;
                    break;
                }
            }
        }
    }

    draw(){
        //親のメソッド=drawMissile()承継
        super.draw(); 
    }
}

//敵の攻撃のクラス
class Attack extends CharaBase{
    constructor(snum,x,y,vx,vy){
        super(snum,x,y,vx,vy);
        this.w = 4;
        this.h = 6;
    }

    update(){
        super.update();
        if(!Myship.damage && checkHit(
            this.x ,     this.y ,    this.w ,    this.h,
            Myship.x , Myship.y ,Myship.w ,Myship.h
            )){
                this.kill     = true;
                Myship.damage = 10;
            }
        }
    }


//戦闘機のクラス
class myShip{
    //戦闘機のプロパティ
    constructor(){
        this.x =(FIELD_W/2)<<8;
        this.y =(FIELD_H/2)<<8;
        this.speed=512;
        this.anime = 1;
        this.reload= 0;
        this.damage= 0;
        this.w = 20;
        this.h = 20;
    }

    //戦闘機を移動させるためのメソッド
    update(){
        if(this.damage){
            this.damage--;
        }
    //スペースキーを押したら=>missile配列にmyMissileインスタンスを格納
        if( key[32] && this.reload == 0 ){
            missile.push(new myMissile(this.x,this.y,0,0-2000) );
    
    //変数eloadに+5 ⇒ 5回読み読み込むまでmyMissileインスタンス生成を止める
            this.reload=5;
        }

    //変数reloadが0より大きければ、1回読み込み毎に-1する。
        if(this.reload>0) this.reload--;


    //直ぐにミサイル発射できるように、スペースキー以外を押したらreload変数を0にする。    
        if(!key[32]) this.reload = 0;

    //押された矢印キーの方向に戦闘機を移動 
    //左方向への移動   
        if(key[37] && this.x>this.speed*30){
            //画面1回更新するごとに、戦闘機の位置Xから変数speedに代入した数を引いていく
            this.x-=this.speed;
            //戦闘機の左傾き画像を参照するための引数を設定
            this.anime = 1;
        }else{
            //何も押してなければデフォルトの戦闘機画像を参照するための引数を設定
            this.anime = 0;
        };
    
    //上方向への移動
        if(key[38] && this.y>=this.speed*5){
            this.y-=this.speed;
        };
    //右方向への移動
        if(key[39] && this.x <= (FIELD_W<<8)-this.speed){
            this.x+=this.speed;
            this.anime = 2;
        }
    //下方向への移動   
        if(key[40] && this.y< (FIELD_H<<8)-this.speed){
            this.y+=this.speed;
        };

    }

    //戦闘機を仮想キャンバスに描画するためのメソッド
    draw(){
        //drawShip関数で、パラメータで指定した戦闘機画像をパラメータで指定したｘ・ｙ座標に描く
        drawShip( this.anime, this.x ,this.y); 
    }
}  
//myShipインスタンスを作成して変数Myshipに代入
let Myship = new myShip();

//戦闘機画像を「shipImg」という名前のオブジェクトにする
let shipImg = new Image();
shipImg.src = 'shooting.png';

//ミサイルのイメージ画像を「missileImg」という名前のオブジェクトにする
let missileImg = new Image();
missileImg.src = 'ミサイル.png';

//画像データから抜き出したいものをオブジェクト化して配列に格納
let shape = [
    new Ship(21,6,62,63),       //戦闘機通常表示
    new Ship(93,6,53,63),       //戦闘機面舵
    new Ship(151,6,53,63),      //戦闘機取舵
    new Missile(233,293,21,27), //ミサイル

    new Missile(104,206,35,35), //enemy1
    new Missile(272,230,44,40), //enemy2
    new Missile(211,9,59,56),   //enemy3
    new Missile(8,12,202,94),   //Boss

    new Missile(65,152,18,16),
    new Missile(57,142,34,36),
    new Missile(45,132,57,58),
    new Missile(33,117,83,83), //bom!

    new Missile(268,343,21,48), //beam1
    new Missile(11,224,22,152) //beam2
];


//戦闘機の描画関数
function drawShip( snum,x,y){
    let sx = shape[snum].x;
    let sy = shape[snum].y;
    let sw = shape[snum].w;
    let sh = shape[snum].h;
    
    let px = (x>>8)-sw/2;
    let py = (y>>8)-sh/2;

    //画面外に出たら描画中止
    if(px+sw<camera_x||px-sw>=SCREEN_W+camera_x||
        py+sh<camera_y||py-sh>=SCREEN_H+camera_y){
    return
    }else{
    vctx.drawImage(shipImg,sx,sy,sw,sh,px+2,py,sw*0.5,sh*0.5);
    }
}

//ミサイルの描画関数
function drawMissile( snum,x,y){
    let sx = shape[snum].x;
    let sy = shape[snum].y;
    let sw = shape[snum].w;
    let sh = shape[snum].h;
    
    let px = (x>>8)-sw/2;
    let py = (y>>8)-sh/2;

    if(px+sw<camera_x||px>=SCREEN_W+camera_x||
        py+sh<camera_y||py-sh>=SCREEN_H+camera_y){
    return
    }else{
    vctx.drawImage(missileImg,sx,sy,sw,sh,px,py,sw*0.5,sh*0.5);
    }
}

    
//ランダム数の生成関数
function random(min,max){
    return Math.floor(Math.random()*(max-min+1))+min;
}
//アタリ判定
function checkHit(x1,y1,w1,h1,x2,y2,w2,h2){
    //矩形同士のアタリ判定
    let left1   = x1>>8;
    let right1  = left1+w1;
    let top1    = y1>>8;
    let bottom1 = top1 + h1;

    let left2   = x2>>8;
    let right2  = left2+w2;
    let top2    = y2>>8;
    let bottom2 = top2 + h2;

    return(left1   <= right2 &&
       right1  >= left2 &&
       top1    <= bottom2 &&
       bottom1 >= top2 )
}
//class Star{...}⇒class.jsへ移行

// function gameInit(){...}⇒function.jsへ移行

//gameループ内のオブジェクトupdate関数を纏める
function updateObj( obj ){
    for(let i = obj.length-1; i>=0 ;i--){
            obj[i].update();
            if(obj[i].kill){
                obj.splice(i,1);
            }
         }
}

//gameループ内の描画関数を纏める
function drawObj(obj){
    for(let i = 0; i<obj.length;i++){
        obj[i].draw();
     }
} 

//各オブジェクトのupdate関数を纏めて実行する
function updateAll(){
    for(let i = 0; i<STAR_MAX;i++){
        star[i].update();
     }

     updateObj(enemy);
     updateObj(missile);
     updateObj(attack);
     
}

//各オブジェクトのdraw関数を纏めて実行する
function drawAll(){
    
    if(Myship.dagame === 0){
        vctx.fillStyle = "black";
    }else{
        vctx.fillStyle = "black";
    };
    vctx.fillRect(camera_x,camera_y,SCREEN_W,SCREEN_H);
    
   
    for(let i = 0; i<STAR_MAX;i++){
       star[i].draw();
    };

    drawObj(enemy);
    drawObj(missile);
    drawObj(attack);
     
   }


//情報の表示
function putInfo(cx,cy){
    if(DEBUG){
        ctx.font="20px 'Impact'";
        ctx.fillStyle="white";
        ctx.fillText("Missile:"+missile.length,cx+20,cy);
        ctx.fillText("Enemy:"+enemy.length,cx+20,cy+20);
        ctx.fillText("Attack:"+attack.length,cx+20,cy+40);
        ctx.fillText("Damage:"+Myship.damage,cx+20,cy+60);
    }
}

//ゲーム初期化
function gameInit(){
    for(let i = 0; i<STAR_MAX;i++){
        star[i]=new Star();
    }
    setInterval( gameLoop , GAME_SPEED );
}

//ゲームのループ
function gameLoop(){

//生成した敵クラスを入れる配列

if( random(0,50)==1){
    enemy.push( new Enemy(4,random(0,FIELD_W)<<8,0,0,random(300,900)));
}


 //アニメーション実行
  updateAll();
  Myship.update();

 //ゲームの描画
  drawAll();
  Myship.draw();

//カメラの移動・描画
 camera_x = (Myship.x>>8)/FIELD_W*(FIELD_W-SCREEN_W)
 camera_y = (Myship.y>>8)/FIELD_H*(FIELD_H-SCREEN_H)
 ctx.drawImage( vcan ,camera_x,camera_y,SCREEN_W,SCREEN_H,
    0,0,CANVAS_W,CANVAS_H);

 //デバック情報の表示
  putInfo( Myship.x>>8,Myship.y-10000>>8);
 
}


//GAMEの初期化
window.onload = function(){
    gameInit();
}









/* ゴミ置き場

/クラス承継で使わなくなったプロパティ
        // this.sn = 3;
        // this.x  = x-2500;
        // this.y  = y;
        // this.vx = vx;
        // this.vy = vy;
        // this.kill = false;

//クラス承継で使わなくなったメソッド
        // this.x += this.vx;
        // this.y += this.vy; 

        // if(this.x<0 || this.x>FIELD_W<<8 || this.y<0 || this.y>FIELD_H<<8){
        //     this.kill = true;
        // }

 //クラス承継で使わなくなったメソッド
        // drawMissile( this.sn,this.x,this.y);

//クラス承継によって使わなくなったクラスプロパティ
        // this.sn = 4;
        // this.x  = x-2500;
        // this.y  = y;
        // this.vx = vx;
        // this.vy = vy;
        // this.kill = false;

 //クラス承継により使わなくなったメソッド
        // this.x += this.vx;
        // this.y += this.vy; 

        // if(this.x<0 || this.x>FIELD_W<<8 || this.y<0 || this.y>FIELD_H<<8){
        //     this.kill = true;
        // }

// //クラス承継により使わなくなったメソッド
        // drawMissile( this.sn,this.x,this.y,0,0);

//make the game loop!
//How to? => invoke "make star function" by 60ps/sec

//draw stars
// for(let i = 0; i<STAR_MAX;i++){
//     star[i].draw();
// }



//  //move stars
//  for(let i = 0; i<STAR_MAX;i++){
//     star[i].update();
//  }

//  updateObj(missile);
//  updateObj(enemy);

// //updateObjにオブジェクトを渡して実行することで、不要となった関数
// //  for(let i = missile.length-1; i>=0 ;i--){
// //     missile[i].update();
// //     if(missile[i].kill){
// //         missile.splice(i,1);
// //     }
// //  }
// //  for(let i = enemy.length-1; i>=0 ;i--){
// //     enemy[i].update();
// //     if(enemy[i].kill){
// //         enemy.splice(i,1);
// //     }
// //  }

//  Myship.update();


 //draw stars
//  vctx.fillStyle = "black";
//  vctx.fillRect(camera_x,camera_y,SCREEN_W,SCREEN_H);

//  //背景の★描画
//  for(let i = 0; i<STAR_MAX;i++){
//     star[i].draw();
//  };

//  //ミサイル・敵の描画
//  drawObj(missile);
//  drawObj(enemy);


//  //関数で纏めた結果使わなくなった関数
// //  for(let i = 0; i<missile.length;i++){
// //     missile[i].draw();
// //  }
// //  for(let i = 0; i<enemy.length;i++){
// //     enemy[i].draw();
// //  }

//  //draw ship on vcan

//  Myship.draw();

//myShip範囲：0～FIELD_W
 //カメラ範囲:0～（FIELD_W-SCREEN_W）



*/

