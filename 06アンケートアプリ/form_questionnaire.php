<?php
//アンケート回答項目を扱うための変数
    $sex=["男性","女性"];                      //性別
    $style=["ルアー","フライ","エサ"];          //釣り方
    $mak=["シマノ","ダイワ","アブガルシア","オービス","スコット","ループ","マルキュー"];

    $datafname = "questionnaire.csv";          //アンケート内容を記録するテキストファイル
    $pageToDisplay = 0 ;                       //表示するページ
    $nickname="";                              //送信されたニックネームを格納する変数
    $seibetsu=-1;                              //性別(男or女)の選択インデックス。-1を指定すれば
                                               //配列$sexの要素を何も選択していない状態となる。
    $tsurikata=-1;                             //釣り方の選択インデックス。配列の考え方は$sexと同じ。
    $checkmessage="";                          //入力項目に入力されなかった場合のエラーメッセージを格納する変数。

    $omosiro="";

//アンケートが送られてきたら・・・
    if(isset($_POST['okuru'])==true){          //もしアンケートの送信ボタンが押されたら
        $pageToDisplay = 1;                    //遷移先のページは「1」
        $tsurikata=$_POST["tsurikata"];        //$tsurikataにアンケートから送られてきた"tsurikata"の値を代入
     //性別入力チェック
        if(isset($_POST['seibetsu'])){         //もしアンケートから'seibetsu'が送信されたら
            $seibetsu=$_POST['seibetsu'];      //変数$seibetsuに、送信されてきた'seibetsu'を代入

        }else{                                 //'seibetsu'が送信されてこなかったら
            $pageToDisplay=0;                  //遷移先ページは「0」
            $checkmessage.="性別が選択されていません。<br>";//表示させたいエラーメッセージを$checkmessageに代入
        }

     //ニックネーム入力チェック
        if($_POST["nickname"] !== ""){         //もしニックネームが空っぽじゃなければ
            $nickname=htmlspecialchars($_POST["nickname"],ENT_QUOTES);//送信されてきた'nickname'を安全な表示に変換して
                                                                      //$nicknameに代入
        }else{                                 //もし、ニックネーム欄が空っぽだったら
            $pageToDisplay=0;                  //遷移先ページは「0」
            $checkmessage.="ニックネームが入力されていません。<br>";//エラーメッセージを$checkmessageに代入
        }

     //チェックメッセージの有無確認
        if($checkmessage !== ""){              //何かエラーメッセージがあれば
            $checkmessage = "<font color='red'><b>".$checkmessage;//赤文字でエラーを表示
            $checkmessage.="必要項目を入力してください。</b></font><br><br>";
        }

        if($_POST["maker"]) $maker=$_POST["maker"];
        $omosiro=$_POST["omosiro"];
    }

//訂正ボタンが押されたら
if(isset($_POST['teisei'])=== true){
     $tsurikata=$_POST['tsurikata'];          //訂正前に送信されてきたデータをそのまま各変数に代入
     $seibetsu =$_POST['seibetsu'];
     $nickname =$_POST['nickname'];
     $omosiro = $_POST['omosiro'];
     if(isset($_POST["maker"])) $maker=$_POST['maker'];
}

//保存ボタンが押されたら
if(isset($_POST['hozon'])== true){
     $tsurikata=$_POST['tsurikata'];           //保存前に送信されてきていたデータをそのまま各変数に代入
     $seibetsu =$_POST['seibetsu'];
     $nickname =$_POST['nickname'];
     if(($_POST['maker'])) $maker = $_POST['maker'];
    //  var_dump($maker);
     $omosiro =$_POST['omosiro'];
     $pageToDisplay=2;                         //遷移先ページは「2」
     $fp=fopen($datafname,"a");                //$datafnameという名前のファイルを、読み書き可能状態で開いて
     fprintf($fp,"%s,%s,%s",htmlspecialchars($nickname,ENT_QUOTES),$sex[$seibetsu],$style[$tsurikata]);//「~.~.~\n」というフォーマットで書き込み
     if(isset($maker)){
         fprintf($fp,"");
         $i=0;
        foreach($maker as $m){
             if($i==0) fprintf($fp,",'%s",$mak[$m]);
             else fprintf($fp,",%s",$mak[$m]);
             $i++;
         }
         fprintf($fp,"',");
     }
     fprintf($fp,"'%s'",htmlspecialchars($omosiro,ENT_QUOTES));
     fprintf($fp,"\n");
     fclose($fp);                              //ファイルを閉じる
}

//     $firsttime = false;
//     $nickname  = htmlspecialchars($_POST['nickname'],ENT_QUOTES);
//     $tsurikata = $_POST['tsurikata'];
//     $seibetsu  = $_POST['seibetsu'];
//     $fp = fopen($datafname,"a");
//     fprintf($fp,"%s,%s,%s\r\n",$nickname,$sex[$seibetsu],$style[$tsurikata]);//format print into file
// }else{
//     $firsttime = true;
// }

?>

<!-- following is html -->
<html>
    <body>
       <?php
       //「0」ページ＝最初のページ
       if($pageToDisplay == 0 ){
           print "<h2>フィッシングスタイルアンケート</h2>";
           if($checkmessage !== "") print $checkmessage."\n";//何かしら$checkmessegeがあれば、それを表示
           print "<form method='post'>\n";
            print "<b>1.あなたのニックネームは？</b>\n";
            print "<ul>";
            print "<input type='text' size='30' name='nickname' value='".htmlspecialchars($nickname,ENT_QUOTES)."'required><br>\n";
            print "</ul>\n";

            print "<b>2.あなたの性別は？</b>\n";
            print "<ul>\n";
            for($i=0;$i<2;$i++){
                print "<input type='radio' name='seibetsu' value={$i}";
                if($i==$seibetsu) print " checked";//訂正ボタンで再度入力する際に、訂正前の入力状態を反映
                print " >{$sex[$i]}<br>\n";
            }
            print "</ul>\n";
           
                        
            print "<b>3.あなたの釣りのスタイルは？</b>";
            print "<ul>\n";
            print "<select name='tsurikata'>\n";
            for($i=0;$i<3;$i++){
                print "<option value=".$i;
                 if($i==$tsurikata) print " selected";//訂正ボタンで再度入力する際に、訂正前の入力状態を反映
                print ">".$style[$i]."</option>";
            }
            print "\n";
            print "</select>";
            print "</ul>\n";

            print "<b>４．あなたの好きなメーカーは？(複数選択OK,無選択Ok)</b>\n";
            print "<ul>\n";
            for($i=0 ; $i<7 ; $i++){
                print "<input type='checkbox' name='maker[]' value=".$i;
                if(isset($maker)){
                    foreach($maker as $m) if($i==$m) print " ckecked";
                     ;
                }
                print ">".$mak[$i]."<br>";
            }
            print "</ul>\n";

            print "<b>5.これからチャレンジしてみたい釣りについて教えてください。</b>";
            print "<ul>\n";
            print "<textarea name='omosiro' row='8' cols='60'>".htmlspecialchars($omosiro,ENT_QUOTES)."</textarea>\n";
            print "</ul>\n";

            print "回答が終わったら、送信ボタンを押してね。<br>\n";
            print "<ul>\n";
            print "<input type='submit' name='okuru' value='送信'>\n";
            print "</ul>\n";
            print "</form>\n";

    //遷移先ページ「1」＝入力確認ページ
       }else if($pageToDisplay == 1){
           print $nickname." さんの回答<br><br>";
           print "性別      :  ".$sex[$seibetsu]."<br>\n";
           print "好きな釣り:  ".$style[$tsurikata]."<br>\n";
           print "好きなメーカー:  ";
        //    var_dump($maker);
           if(isset($maker)){
               $i=0;
               foreach($maker as $m){
                   if($i==0)printf("%s",$mak[$m]);
                   else printf(".%s",$mak[$m]);
                   $i++;
               }
           }
           print "<br>\n";

           print "チャレンジしたい釣り:  ".htmlspecialchars($omosiro,ENT_QUOTES)."<br>\n";
           print "<br>\n";
           print "<ul>\n";

           //確定送信
           print "<form method='post'>\n";
           print "<input type='submit' name='hozon' value='確定送信'><br>\n";
           print "<input type='hidden' name='nickname' value='".htmlspecialchars($nickname)."'>\n";
           print "<input type='hidden' name='seibetsu' value='{$seibetsu}'>\n";
           print "<input type='hidden' name='tsurikata' value='{$tsurikata}'>\n";
           if(isset($maker)){
                $i=0;
                foreach($maker as $m){
                    print "<input type='hidden' name='maker[".$i."]' value='".$m."'>\n";
                    $i++;
                }
           } 
           print "<input type='hidden' name='omosiro' value='".htmlspecialchars($omosiro,ENT_QUOTES)."'>\n";
           print "</form>\n";
           
           //キャンセル＝送信する値を何も持たず、このページを再読み込み
           print "<form method='post'>\n";
           print "<input type='submit' name='kyanseru' value='キャンセル'><br>\n";
           print "</form>\n";

           //やり直し
           print "<form method='post'>\n";
           print "<input type='submit' name='teisei' value='やり直し'><br>\n";//訂正が押された場合の処理に飛ぶ
           print "<input type='hidden' name='nickname' value='{$nickname}'>\n";
           print "<input type='hidden' name='seibetsu' value='{$seibetsu}'>\n";
           print "<input type='hidden' name='tsurikata' value='{$tsurikata}'>\n";
           if(isset($maker)){
               $i=0;
               foreach($maker as $m){
                   print "<input type='hidden' name='maker[".$i."]' value='".$m."'>\n";
               }
           }
           print "<input type='hidden' name='omosiro' value='".htmlspecialchars($omosiro,ENT_QUOTES)."' >\n";
           print "<form method='post'>\n";
           print "</ul>\n";
       }else if($pageToDisplay==2){
           print "ファイル ".$datafname." に保存しました。<br><br>\n";
           print "<form method='post'>\n";
           print "<input type='submit' name='ryokai' value='了解'><br>\n";
           print "</form>\n";
       }

        // if($firsttime === true){
        //     print "<h2>アンケート</h2>\n";
            
        // }else{
        //     print $nickname." さんの回答です<br><br>";
        //     print "性別 : ".$sex[$seibetsu]."<br>\n";
        //     print "釣りのスタイル : ".$style[$tsurikata]."<br>\n";
        //     print "<br>\n";
        //     print "ファイル ".$datafname." に保存しました\n";
        // }

       ?> 
    <a href="counter.php">集計結果を見る</a>
    </body>
</html>