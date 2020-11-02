<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
  <body>
    <?php
	    // DB接続設定
	    $dsn = 'データベース名';
	    $user = 'ユーザー名';
	    $password = 'パスワード';
		$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //テーブル作成（tba） 
        $sql = "CREATE TABLE IF NOT EXISTS tba
        (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name char(32),
        comment varchar(256),
        date DATETIME,
        pass INT
        )";
        $stmt = $pdo -> query($sql);

        //Noticeエラーだけ非表示にする。
        error_reporting(E_ALL & ~E_NOTICE);

        if(!empty("submit")){//もしもsubmitボタンが送信されたとき。
            //変数設定
            $checknumber=$_POST["checknumber"];
            $name=$_POST["namea"];
            $comment=$_POST["commenta"];
            $pass=$_POST["passa"];
            $date = new DATETIME();
            $date = $date -> format("Y/m/s, H:i:s");

            if($name!=""&&$comment!=""&&empty($checknumber)){//空欄じゃない、かつ編集申請されてないとき。
            //sqlのテーブルにデータを書き込み
            $sql = $pdo -> prepare("INSERT INTO tba (name, comment, date, pass) VALUES(:name, :comment, :date, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindValue(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_INT);
            $sql -> execute();    
            }

            if($name!=""&&$comment!=""&&$checknumber!=""){//空欄じゃない、かつ編集申請もされているとき。
                //sqlテーブルのデータを編集
                $sql = 'UPDATE tba SET name=:name, comment=:comment, date=:date, pass=:pass WHERE id=:id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt -> bindValue(':date', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':pass', $pass, PDO::PARAM_INT);
                $stmt -> bindParam(':id', $checknumber, PDO::PARAM_INT);
                $stmt -> execute();
            }
        }

        if(!empty("delete")){//もしも削除ボタンが送信されたとき
            //変数設定
            $delnumber=$_POST["delnumber"];
            $delpass=$_POST["passb"];

            if($delnumber!=""&&$delpass!=""){
                //まず、パスワードを抽出するためデータへアクセス。
                $sql = 'SELECT * FROM tba WHERE id=:id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':id',$delnumber, PDO::PARAM_INT);
                $stmt -> execute();
                $results = $stmt -> fetchAll();
                foreach ($results as $row);{
                    $passcheck = $row['pass'];//パスワードを取得。
                }
                if($passcheck==$delpass){//パスワードがあっているなら。
                    $sql = "delete from tba where id=:id";
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam (':id', $delnumber, PDO::PARAM_INT);
                    $stmt -> execute();
                }
            }
        }

        if(!empty("edit")){//もしも編集ボタンが送信されたとき
            //変数設定
            $edtnumber=$_POST["edtnumber"];
            $edtpass=$_POST["passc"];

            if($edtnumber!=""&&$edtpass!=""){//データの中身を取得
                $sql = 'SELECT * FROM tba WHERE id=:id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':id', $edtnumber, PDO::PARAM_INT);//ただしidが編集したい番号の時に限定
                $stmt -> execute();
                $results = $stmt -> fetchAll();
                foreach ($results as $row);{
                    $passcheck = $row['pass'];//パスワードのみ取得
                }
                if($passcheck==$edtpass){//パスワードがあっているなら。
                    //編集したい投稿の中身を、新しい変数に代入。
                    foreach($results as $row){
                        $edtname = $row['name'];
                        $edtcomment = $row['comment'];
                        $newpass=$edtpass;
                        $sentnumber=$edtnumber;
                    }
                }
            }
        }

    ?> 

    <form action="" method="post">
    <input type="hidden" name="checknumber" 
        value="<?php if(isset($sentnumber)){echo $sentnumber;} ?>">
    名前：<input type="text" name="namea" 
        value="<?php if(isset($edtname)){echo $edtname;} ?>"><br>
    パスワード（半角数字）：<input type="number" name="passa"
        value="<?php if(isset($newpass)){echo $newpass;} ?>"><br>
    コメント：<input type="text" name="commenta" 
        value="<?php if(isset($edtcomment)){echo $edtcomment;} ?>"><br>
    <input type="submit" name="submit" value="投稿"><br>
    </form>

    <form action="" method="post">
    削除したい投稿番号：<input type="number" name="delnumber"><br>
    パスワード（半角数字）：<input type="number" name="passb"><br>
    <input type="submit" name="delete" value="削除"><br>
    </form>

    <form action="" method="post">
    編集したい投稿番号：<input type="number" name="edtnumber"><br>
    パスワード（半角数字）：<input type="number" name="passc"><br>
    <input type="submit" name="edit" value="編集申請"><br>
    </form>

    <?php
    //sqlのテーブルの中身を表示させる。
    $sql = 'SELECT * FROM tba';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach ($results as $row){
        echo $row['id']."<br>";
        echo $row['name']."<br>";
        echo $row['comment']."<br>";
        echo $row['date']."<br><br>";
    }
    ?>

</body>
</html>