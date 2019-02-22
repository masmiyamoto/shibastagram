<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「退会ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('./auth.php');

// ========================================
// 画面処理
// ========================================
// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET deleted = 1 WHERE id = :u_id';
    $sql2 = 'UPDATE posts SET deleted = 1 WHERE posted_user = :u_id';
    $sql3 = 'UPDATE comments SET deleted = 1 WHERE commented_user = :u_id';
    $sql4 = 'UPDATE favorites SET deleted = 1 WHERE user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id']);
    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);
    $stmt4 = queryPost($dbh, $sql4, $data);

    // クエリ成功の場合
    if($stmt1 && $stmt2 && $stmt3 && $stmt4){
      // セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION, true));
      debug('サインアップページに遷移します');
      header("Location:./signup.php");
    }else{
      debug('クエリが失敗しました');
      $err_msg['common'] = MSG07;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>退会 | Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body>
    <?php
      require('./header.php');
    ?>

    <article class="main-wrapper2">
      <div class="site-width3">

        <section class="left-list">
          <ul>
            <li><a href="./prof-edit.php">プロフィールを編集</a></li>
            <li><a href="./pass-edit.php">パスワードを変更</a></li>
            <li><span class="active">退会</span></li>
          </ul>
        </section>

        <section class="right-list unsubscribe">
          <form action="" method="post">
            <h2>退会はこちら</h2>
            <div class="message-area">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <input type="submit" value="退会する" name="submit">
          </form>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>