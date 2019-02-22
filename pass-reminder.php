<?php

// 共通変数・関数ファイル読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワード再発行ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証は不要

// ========================================
// 画面処理
// ========================================
// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));

  // 変数にPOST情報を代入
  $email = $_POST['email'];

  // 未入力チェック
  validRequired($email, 'email');

  if(empty($err_msg)){
    debug('未入力チェックOK');

    // emailの形式チェック
    validEmail($email, 'email');
    // emailの最大文字数チェック
    validMaxLen($email, 'email');

    if(empty($err_msg)){
      debug('バリデーションOK');

      // 例外処理
      try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND deleted = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // EmailがDBに登録されている場合
        if($stmt && array_shift($result)){
          debug('クエリ成功。DB登録あり');
          $_SESSION['msg_success'] = SUC03;

          $auth_key = makeRandKey(); // 認証キー生成

          // メールを送信
          $from = 'info@shibastagram.com';
          $to = $email;
          $subject = 'パスワード再発行認証 | Shibastagram';
          $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8888/pf0005shibastagram/pass-reset.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行される場合は下記ページよりお願いいたします。
http://localhost:8888/pf0005shibastagram/pass-reminder.php

////////////////////////////////////////
Shibastagram カスタマーセンター
URL : https://shibastagram.com/
E-mail : info@shibastagram.com
////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);

          // 認証に必要な情報をセッションへ保存
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key_limit'] = time()+(60*30);
          debug('セッション変数の中身：'.print_r($_SESSION,true));

          header("Location:./pass-reset.php");

        }else{
          debug('クエリに失敗したかDBに登録のないEmailが入力されました');
          $err_msg['common'] = MSG07;
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
    <title>パスワード再発行 | Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body>
    <?php
      require('./header.php');
    ?>

    <article class="main-wrapper">
      <div class="site-width2">
        <section id="pass-reminder">
          <form action="" method="post">
            <h2>パスワード再発行</h2>
            <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>
            <div class="message-area">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              <input type="text" placeholder="メールアドレスを入力" name="email">
            </label>
            <div class="message-area">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <input type="submit" value="送信する">
          </form>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>