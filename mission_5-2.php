<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>WEB掲示板</title>
</head>
<body>
<?php
//データベース作成
  $dsn='mysql:dbname='データベース名';host='MySQLホスト名'';
  $user='ユーザー名';
  $password='パスワード';
  $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
//テーブル作成
  $sql="CREATE TABLE IF NOT EXISTS tbtest"
  ." ("
  ."id INT AUTO_INCREMENT PRIMARY KEY,"
  ."name char(32),"
  ."comment TEXT,"
  ."postedAt DATETIME,"
  ."password char(20)"
  .");";
  $stmt=$pdo->query($sql);
//投稿があれば
  if(isset($_POST["name"])&&isset($_POST["comment"])){
//入力フォームのデータを受け取る
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $password=$_POST["newPass"];
//投稿日時取得
    $date=new DateTime();
    $date=$date->format('Y-m-d H:i:s');
    if(empty($_POST["editid"])){
//データベース入力
      $sql=$pdo->prepare("INSERT INTO tbtest(name,comment,postedAt,password) VALUES(:name,:comment,:postedAt,:password)");
      $sql->bindParam(':name',$name,PDO::PARAM_STR);
      $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
      $sql->bindValue(':postedAt',$date,PDO::PARAM_STR);
      $sql->bindParam(':password',$password,PDO::PARAM_STR);
      $sql->execute();
    }else{
      $sql='update tbtest set name=:name,comment=:comment,password=:password where id=:id';
      $stmt=$pdo->prepare($sql);
      $stmt->bindParam(':name',$name,PDO::PARAM_STR);
      $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
      $stmt->bindParam(':id',$_POST["editid"],PDO::PARAM_INT);
      $stmt->bindParam(':password',$password,PDO::PARAM_STR);
      $stmt->execute();
    }
  }
//削除指令があれば
  if(isset($_POST["delete"])){
    $deleteid=$_POST["delete"];
    $sql="SELECT password FROM tbtest where id=$deleteid";
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
      if($_POST["delPass"]==$row["password"]){
        $sql='DELETE from tbtest where id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$deleteid,PDO::PARAM_INT);
        $stmt->execute();
      }
    }
  }
//編集指令があれば
  if(isset($_POST["edit"])){
    $editid=$_POST["edit"];
    $sql="SELECT * FROM tbtest where id=$editid";
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
      if($_POST["editPass"]==$row["password"]){
        $editName=$row["name"];
        $editComment=$row["comment"];
      }
    }
  }
?>
<!--投稿フォーム-->
  <form method="post">
	<div>
    <input type="hidden" id="editid" name="editid" value="<?php if(isset($editid)){echo $editid;} ?>">
	</div>
	<div>
    <label for="name">名前</label>
    <input type="text" id="name" name="name" value="<?php if(isset($editName)){echo $editName;} ?>">
	</div>
	<div>
    <label for="comment">コメント</label>
    <input type="message" id="comment" name="comment" value="<?php if(isset($editComment)){echo $editComment;} ?>">
	</div>
	<div>
    <label for="newPass">パスワード</label>
    <input type="password" id="newPass" name="newPass" value="<?php if(isset($editPass)){echo $editPass;}?>">
	</div>
 	<div>
    <input type="submit" value="送信">
	</div>
  </form>
<!--削除フォーム-->
  <form method="post">
	<div>
  <label for="delete">削除対象番号</label>
  <input type="text" id="delete" name="delete">
	</div>
	<div>
    <label for="delPass">パスワード</label>
    <input type="password" id="delPass" name="delPass">
	</div>
	<div>
  <input type="submit" value="削除">
	</div>
  </form>
  <form method="post">
	<div>
<!--編集フォーム-->
  <label for="edit">編集対象番号</label>
  <input type="text" id="edit" name="edit">
	</div>
	<div>
    <label for="editPass">パスワード</label>
    <input type="password" id="editPass" name="editPass">
	</div>
	<div>
  <input type="submit" value="編集">
	</div>
  </form>
<?php
//ブラウザ上に書き出し
  $sql='SELECT * FROM tbtest';
  $stmt=$pdo->query($sql);
  $results=$stmt->fetchAll();
  foreach ($results as $row){
	 echo $row['id'].',';
	 echo $row['name'].',';
	 echo $row['comment'].',';
	 echo $row['postedAt'].'<br>';
  echo "<hr>";
  }
    /*if(isset($_POST["editNum"])){
      $sql='SELECT * FROM tbtest';
      $stmt=$pdo->query($sql);
      $results=$stmt->fetchAll();
      var_dump($results);
      echo "<hr>";
    }*/
?>