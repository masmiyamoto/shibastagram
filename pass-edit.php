<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワード変更ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('./auth.php');

// ========================================
// 画面処理
// ========================================
// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));

  // 変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  // 未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(empty($err_msg)){
    debug('未入力チェックOK');

    // 新旧パスワードの入力チェック
    validPass($pass_old, 'pass_old');
    validPass($pass_new, 'pass_new');

    // 古いパスワードとDB内のパスワードを照合
    if(!password_verify($pass_old, $userData['password'])){
      $err_msg['pass_old'] = MSG12;
    }

    // 新しいパスワードが古いパスワードと異なるかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG13;
    }

    // 新パスワードと新パスワード(再入力)が合っているかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
      debug('バリデーションOK');

      // 例外処理
      try {

        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :password WHERE id = :id';
        $data = array(':password' => password_hash($pass_new, PASSWORD_DEFAULT), ':id' => $_SESSION['user_id']);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if($stmt){
          $_SESSION['msg_success'] = SUC01;

          // メールを送信
          $username = ($userData['user_name']) ? $userData['user_name'] : 'Shibastagramユーザー';
          $from = 'info@shibastagram.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知 | Shibastagram';
          $comment = <<<EOT
{$username} さま
パスワードが変更されました。

////////////////////////////////////////
Shibastagram カスタマーセンター
URL : https://shibastagram.com/
E-mail : info@shibastagram.com
////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);

          header('Location:./userpage.php');
        }
      } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
?>
<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>パスワードを変更 | Shibastagram</title>
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
            <li><span class="active">パスワードを変更</span></li>
            <li><a href="./unsubscribe.php">退会</a></li>
          </ul>
        </section>

        <section class="right-list">
          <div class="pass-edit">
            <form action="" method="post">
              <h2>パスワード変更</h2>
              <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>
              <div class="message-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <label>
                <input type="password" placeholder="旧パスワードを入力" name="pass_old" value="<?php if(!empty($_POST['pass_old'])) echo $_POST['pass_old']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old']; ?>
              </div>
              <label>
                <input type="password" placeholder="新パスワードを入力" name="pass_new">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new']; ?>
              </div>
              <label>
                <input type="password" placeholder="新パスワードを入力 (再入力)" name="pass_new_re">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re']; ?>
              </div>
              <input type="submit" value="変更する">
            </form>
          </div>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>