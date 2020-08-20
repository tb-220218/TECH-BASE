<?php
   ////データベース接続設定////
   //echo "データベースの接続を開始します。<br>";
   //データベースを指定する
   $dsn = 'データベース名';
   //ユーザー名を指定する
   $user = 'ユーザー名';
   //パスワードを設定する
   $password = 'パスワード名';
   //PHP Data Objectsのインスタンス化
   $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
   
   //テーブル作成
   $sql = "CREATE TABLE IF NOT EXISTS mission_5"
   ."("
   . "id INT AUTO_INCREMENT PRIMARY KEY,"
   . "name char(32),"
   . "comment TEXT,"
   . "password char(32),"
   . "date DATETIME"
   .");";
   $stmt = $pdo->query($sql);
   
   //変数初期化
   $name = null;
   $comment = null;
   $pass1 = null;
   $date = null;
   $errors = null;
   
   if(isset($_POST["submit"])){
       if(isset($_POST["name"])){
           $name = $_POST["name"];
       }
       if(mb_strlen($name) > 33){
           $errors = '名前は32文字以内で入力してください';
       }
       if(isset($_POST["comment"])){
           $comment = $_POST["comment"];
       }
       date_default_timezone_set('Asia/Tokyo');
       $date = date("Y/m/d H:i:s");
       if(isset($_POST["password1"])){
           $pass1 = $_POST["password1"];
       }
       if(isset($_POST["editNo"])){
           $editNo = $_POST["editNo"];
       }
       
       if(empty($editNo) &&!empty($name) &&!empty($comment) &&!empty($pass1)){
           $sql = $pdo -> prepare("INSERT INTO mission_5 (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
           $sql -> bindParam(':name', $name, PDO::PARAM_STR);
           $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
           $sql -> bindParam(':password', $pass1, PDO::PARAM_STR);
           $sql -> bindParam(':date', $date, PDO::PARAM_STR);
           $sql -> execute();
       }else if(!empty($editNo) &&!empty($name) &&!empty($comment) &&!empty($pass1)){
           $sql = $pdo->prepare('UPDATE mission_5 SET name=:name,comment=:comment,password=:password WHERE id=:editNo');
           $sql -> bindParam(':name', $name, PDO::PARAM_STR);
           $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
           $sql -> bindParam(':password', $pass1, PDO::PARAM_STR);
           $sql -> bindParam(':editNo', $editNo, PDO::PARAM_INT);
           $sql -> execute();
       }else{
           $errors = "名前またはコメントまたはパスワードを入力してください";
            }
       }
       
       if(isset($_POST["edit"])){
           if(isset($_POST["editNo"])){
               $editNo = $_POST["editNo"];
           }
           if(isset($_POST["password2"])){
               $pass2 = $_POST["password2"];
           }
           if(!empty($editNo)){
               if(!empty($pass2)){
                   $sql = $pdo->prepare('SELECT * FROM mission_5 WHERE id=:editNo');
                   $sql -> bindParam(':editNo', $editNo, PDO::PARAM_INT);
                   $sql -> execute();
                   $selectedRows = $sql->fetchAll();
                   if(!empty($selectedRows[0])){
                       $getContents = $selectedRows[0];
                       if($pass2==$getContents['password']){
                           $name = $getContents['name'];
                           $comment = $getContents['comment'];
                           $pass1 = $getContents['password'];
                       }else{
                           $errors = "正しいパスワードを入力してください";
                       }
                       }else{
                           $errors = "編集対象番号がありません";
                       }
                       }else{
                           $errors = "パスワードを入力してください";
                       }
                       }else{
                           $errors = "編集対象番号を入力してください";
                       }
           }
           
           if(isset($_POST["delete"])){
               if(isset($_POST["password3"])){
                   $pass3 = $_POST["password3"];
               }
               if(isset($_POST["deleteNo"])){
                   $deleteNo = $_POST["deleteNo"];
               }
               if(!empty($deleteNo)){
                   if(!empty($pass3)){
                        $sql = $pdo->prepare('SELECT password FROM mission_5 WHERE id=:deleteNo');
                        $sql -> bindParam(':deleteNo', $deleteNo, PDO::PARAM_INT);
                        $sql -> execute();
                        $selectedRows = $sql->fetchAll();
                        if(!empty($selectedRows[0])){
                            $getContents = $selectedRows[0];
                            if($pass3==$getContents['password']){
                               $sql = 'delete from mission_5 where id=:deleteNo';
                               $stmt = $pdo->prepare($sql);
                               $stmt->bindParam(':deleteNo',$deleteNo,PDO::PARAM_INT);
                               $stmt->execute();
                            }else{
                                $errors = "正しいパスワードを入力してください";
                            }
                        }else{
                            $errors = "削除対象番号がありません";
                        }
                   }else{
                       $errors = "パスワードを入力してください";
                   }
               }else{
                   $errors = "削除対象番号を入力してください";
               }
           }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>
<body>
    <h1>投稿内容</h1>
  <br>
    <form action=""method="post">
                 <input type="text" name="postNum" value="<?php if(isset($editNo)){echo $editNo;}?>"> <br>
       投稿者名：<input type="text" name="name" value="<?php if(isset($name)){echo $name;}?>"> <br>
       コメント：<input type="text" name="comment" value="<?php if(isset($comment)){echo $comment;}?>"><br>
       パスワード：<input type="text" name="password1" value="<?php if(isset($pass1)){echo $pass1;}?>"> <br>
        <input type="submit" name="submit"><br><br>
    </form>
    <form action=""method="post">
    <!--削除フォーム-->
    【 削除フォーム 】
        <br>
        削除対象番号：<input type="number" name="deleteNo"><br>
        パスワード：<input type="text" name="password3"><br>
    <input type="submit" name="delete" value="削除"><br><br>
    </form>
    <form action=""method="post">
    <!--編集番号指定用フォーム-->
    【 編集フォーム 】
        <br>
        投稿番号：<input type="number" name="editNo" value="<?php if(isset($editNo)){echo $editNo;}?>"><br>
        パスワード：<input type="text" name="password2"><br>
        <input type="submit" name="edit" value="編集"><br><br>
    </form>
    <br>
    <h1>Web掲示板</h1>
    <br>
    

    <?php
    //入力したデータレコードを抽出しブラウザに表示する
    $sql = 'SELECT * FROM mission_5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].':';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
        echo "<hr>";
    }
    if(!empty($errors)){
        echo $errors;
    }
   ?>
</body>
</html>