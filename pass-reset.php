<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワード再発行認証キー入力ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証は不要

// SESSIONに認証キーがあるか確認、なければリダイレクト
// if(empty($_SESSION['auth_key'])){
//   header("Location:./pass-reminder.php");
// }

// ========================================
// 画面処理
// ========================================
// POST送信されていた場合
if(!empty($_POST)){
  debug('POST情報があります');
  debug('POST情報：'.print_r($_POST,true));

  // 変数に認証キーを代入
  $auth_key = $_POST['token'];

  // 未入力チェック
  validRequired($auth_key, 'token');

  if(empty($err_msg)){
    debug('未入力チェックOK');

    // 固定長チェック
    validLength($auth_key, 'token');
    // 半角チェック
    validHalf($auth_key, 'token');

    if(empty($err_msg)){
      debug('バリデーションOK');

      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['token'] = MSG15;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['token'] = MSG16;
      }

      if(empty($err_msg)){
        debug('認証OK');

        $pass = makeRandKey();

        // 例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          $sql= 'UPDATE users SET password = :password WHERE email = :email AND deleted = 0';
          $data = array(':email' => $_SESSION['auth_email'], ':password' => password_hash($pass, PASSWORD_DEFAULT));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          // クエリ成功の場合
          if($stmt){
            debug('クエリ成功');

            // メールを送信
            $from = 'info@shibastagram.com';
            $to = $_SESSION['auth_email'];
            $subject = 'パスワード再発行完了 | Shibastagram';
            $comment = <<<EOT
本メールアドレス宛にパスワードの再発行をいたしました。
下記のURLにて再発行パスワードをご入力のうえ、ログインしてください。
http://localhost:8888/pf0005shibastagram/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードの変更をお願いいたします。

////////////////////////////////////////
Shibastagram カスタマーセンター
URL : https://shibastagram.com/
E-mail : info@shibastagram.com
////////////////////////////////////////
EOT;
            sendMail($from, $to, $subject, $comment);

            // セッション削除
            session_unset();
            $_SESSION['message_success'] = SUC03;
            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header("Location:./login.php");
          }else{
            debug('クエリに失敗しました');
            $err_msg['common'] = MSG07;
          }
        }catch (Exception $e) {
          error_log('エラー発生：' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
?>
<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>パスワードリセット | Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body>
    <?php
      require('./header.php');
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <article class="main-wrapper">
      <div class="site-width2">
        <section id="pass-reminder">
        <form action="" method="post">
            <h2>パスワードリセット</h2>
            <p>ご指定のメールアドレスにお送りした認証キーをご入力ください。</p>
            <div class="message-area">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label class="<?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>">
              <input type="text" placeholder="認証キーを入力" name="token">
            </label>
            <div class="message-area">
            <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
            </div>
            <input type="submit" value="再発行する">
          </form>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>