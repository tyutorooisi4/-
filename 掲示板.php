   
    <!DOCTYPE html>                


<?php
 echo "白米チームの掲示板byマレイ<br>";
    echo "好きな食べ物について語ろう！<br>";
//dbに接続するよ！
$dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//必要なテーブルが無い時に、それを作成するよ！    
    $sql = 'CREATE TABLE IF NOT EXISTS bulletin(id INT AUTO_INCREMENT PRIMARY KEY,name CHAR(16),comment TEXT,ts DATETIME,password CHAR(16))';
    $stmt = $pdo->query($sql);


//新規投稿フォームから投稿された時、テーブルにレコードのインサートを行うよ！
    if (isset($_POST['name']) && isset($_POST['comment'])) {
        if ($_POST['name'] !== '' && $_POST['comment'] !== '') {
            $sql = $pdo->prepare('INSERT INTO bulletin(name,comment,ts,password) VALUES (:name,:comment,:ts,:password)');
            $sql->bindParam(':name', $name, PDO::PARAM_STR);
            $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql->bindParam(':ts', $ts, PDO::PARAM_STR);
            $sql->bindParam(':password', $password, PDO::PARAM_STR);
            $name = $_POST['name'];
            $comment = $_POST['comment'];
            $ts = date('Y-m-d H:i:s');
            $password = $_POST['password'];
            $sql->execute();
        }
    }
//削除フォームに番号とパスワードが入力された時、以下の処理を行うよ！
   if (isset($_POST['delete']) && isset($_POST['pass_del'])) {
       if ($_POST['delete'] !== '' && $_POST['pass_del'] !== '') {
           //パスワードとidを読み込んで、照合するよ！
            $sql = 'SELECT * FROM bulletin';
           $stmt = $pdo->query($sql);
           $results = $stmt->fetchAll();
           foreach ($results as $row) {
               $pass = $row['password'];
               $num = $row['id'];
            //照合した結果がtrueなら削除を行うよ！
                if ($_POST['pass_del'] === $pass && $_POST['delete'] === $num) {
                    $sql = $pdo->prepare('DELETE FROM bulletin WHERE id=:delete');
                    $sql->bindParam(':delete', $delete, PDO::PARAM_STR);
                    $delete = $_POST['delete'];
                    $sql->execute();
                }
           }
       }
   }
   //編集フォームに番号とパスワードが入力されたとき、以下の処理を行う
   if (isset($_POST['editnum']) && $_POST['editnum'] !== '') {
       if (isset($_POST['pass_edit']) && $_POST['pass_edit'] !== '') {
           $pass_edit = $_POST['pass_edit'];
           $sql = 'SELECT * FROM bulletin';
           $stmt = $pdo->query($sql);
           $results = $stmt->fetchAll();
           foreach ($results as $row) {
               $num = $row['id'];
               $pass = $row['password'];
               $name = $row['name'];
               $comment = $row['comment'];
            //照合した結果がtrueなら編集モードに移行
                if ($_POST['pass_edit'] === $pass && $_POST['editnum'] === $num) {
                    $inputedname = $name;
                    $inputedcomment = $comment;
                    $targetnumber = $num;
                }
           }
       }
   }
   //編集モードのフォームから投稿された時、レコードのUPDATEを行う
   if (isset($_POST['flagnumber'])&&$_POST['flagnumber']!=="") {
                     $flagnumber = $_POST['flagnumber'];
                     $sql = $pdo->prepare('UPDATE bulletin SET name=:name WHERE id=:id ');
                    $sql->bindParam(':name', $editedname, PDO::PARAM_STR);
                    $editedname = $_POST['editedname'];

                    $sql->bindParam(':id', $id, PDO::PARAM_STR);
                    $id = $_POST['flagnumber'];
                    $sql->execute();
                    
                    $sql = $pdo->prepare('UPDATE bulletin SET  comment=:comment WHERE id=:id ');
                    $sql->bindParam(':comment', $editedcomment, PDO::PARAM_STR);
                    $editedcomment = $_POST['editedcomment'];
                    $sql->bindParam(':id', $id, PDO::PARAM_STR);
                    $id = $_POST['flagnumber'];
                    $sql->execute();
                    
   }
    //出来上がったdbを読み込んで表示するよ！
    $sql = 'SELECT * FROM bulletin';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        echo $row['id'].'<>';
        echo $row['name'].'<>';
        echo $row['comment'].'<>';
        echo $row['ts']/*.'<>';
        echo $row['password']*/.'<br>';
        echo '<hr>';
    }


    ?>
    <html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>白米けいじばん</title>
</head>
<body>
     <form action="" method="post" name="SUBMIT">
        <?php
        if (isset($targetnumber) && $targetnumber !== '') {
            ?>
            <form action="" method="post" name="SUBMIT">
            <input type="text" name="editedname" value= <?php echo $inputedname;
            ?>>
            <input type="text" name="editedcomment" value=<?php echo $inputedcomment;
            ?>>
            <input type="hidden" name="flagnumber" value=<?php echo $targetnumber;
            ?>>
            現在、編集モード(/・ω・)/
            <input type="submit" name="submit" value="投稿">
        </form>
         <?php 
        } else {
            ?>
   

    <form action="" method="post" name="SUBMIT">
        <input type="text" name="name" value = '名前を入れてね'> 
        <input type="text" name="comment" value = 'コメントを書いてね' >
        <input type="text" name="password" value = 'パスワードを書いてね' >
        <input type="submit" name="submit" value="投稿">
    </form>
     <?php 
        } ?>
    <form action="" method="post" name="DELETE">
        <input type="text" name="delete" value="番号入れてね" >
        <input type="text" name="pass_del" value = 'パスワードを書いてね' >
        <input type="submit" name="submit" value="削除">
    </form>
    <form action="" method="post" name="EDIT">
        <input type="text" name="editnum" value=<?php if (isset($targetnumber) && $targetnumber !== '') {
    echo $targetnumber;
} else {
    echo '番号入れてね';
}?>>
        <input type="text" name="pass_edit" value = 'パスワードを書いてね' >
        <input type="submit" name="submit" value="編集">
        
    </form>
    </body>
</html>