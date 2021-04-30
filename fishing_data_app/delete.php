<?php
session_start();
// var_dump(key(array_slice($_GET,1)));
$id=$_GET['id'];
$syori=key(array_slice($_GET,1));

// echo $id;
// exit;

    require_once 'dbc.php';
    loginCheck();
    $dbo = connectDB();
    
    //DBから、削除or変更対象選択
    $sql="SELECT * FROM fishing_db WHERE id=:id";
    $stmt=$dbo->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $status = $stmt->execute();
    
    $view="";
    if($status===false){
        $error = $stmt->errorInfo();
        exit("ErrorQuery:".$error[2]);
    }else{
        //idで取得しているので、返ってくるのは１データ。よって、fetchで対応
       $row = $stmt->fetch();
    
    }
    // var_dump($row);




// exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除or編集</title>
</head>
<body>
  <?php
    if($syori=='訂正'){
        print '<form method="post" action="update.php">';
    }elseif($syori=='削除'){
        print '<form method="post" action="sakujo.php">';
    }
  ?>  
 <form method="post" action='update.php'>
     <h1>内容の削除or編集</h1>
    <fieldset>
        <legend>削除or編集</legend>
        <label>fish_name:<input type="text" name="fish_name" value="<?=$row['fish_name']?>"></label><br>
        <label>fish_size:<input type="text" name="fish_size" value="<?=$row['fish_size']?>"></label><br>
        <label for='setsumei'>説明:</label>
        <textarea name='setsumei' row='4' cols='40'><?=$row['setsumei']?></textarea><br>
        <label for='open'>公開設定:</label><br>
        <?php
        if($row['op_flag']==1)
        {print "<input type='radio' name='op_flag' value=1 checked>公開";
         print "<input type='radio' name='op_flag' value=0>非公開";}else{
            print "<input type='radio' name='op_flag' value=1>公開";
            print "<input type='radio' name='op_flag' value=0 checked>非公開";
         }?>
        <?php
        if($syori=='訂正'){
            print "<input type='hidden' name='id' value='".$row['id']."'>";
            print '<input type="submit" value="訂正">';
        }elseif($syori=='削除'){
            print "<input type='hidden' name='id' value='".$row['id']."'>";
            print '<input type="submit" value="削除">';
        }
        ?>
    </fieldset>
 </form>
</body>
</html>