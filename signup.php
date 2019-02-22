<?php

// 共通変数・関数ファイル読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザー登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// post送信されていた場合
if(!empty($_POST)){

  // 変数にユーザー情報を代入
  $email = $_POST['email'];
  $user_id = $_POST['user_id'];
  $user_name = $_POST['user_name'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($user_id, 'user_id');
  validRequired($user_name, 'user_name');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)){

    // emailの形式チェック
    validEmail($email, 'email');
    // emailの最大文字数チェック
    validMaxLen($email, 'email');
    // email重複チェック
    validEmailDup($email);

    // user_idの半角英数字チェック
    validHalf($user_id, 'user_id');
    // user_idの重複チェック
    validUserIdDup($user_id);

    // パスワードのバリデーション
    validPass($pass, 'pass');

    // パスワード(再入力)のバリデーション
    validPass($pass_re, 'pass_re');

    if(empty($err_msg)){

      // パスワードとパスワード(再入力)が合っているか
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)){

        // 例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'INSERT INTO users (email, user_id, user_name, password, created_at, login_date) VALUES (:email, :user_id, :user_name, :password, :created_at, :login_date)';
          $data = array(':email' => $email,
                        ':user_id' => $user_id,
                        ':user_name' => $user_name,
                        ':password' => password_hash($pass, PASSWORD_DEFAULT),
                        ':created_at' => date('Y-m-d H:i:s'),
                        ':login_date' => date('Y-m-d H:i:s'));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          // クエリ成功の場合
          if($stmt){
            // ログイン有効期限(デフォルト1時間とする)
            $sesLimit = 60*60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header("Location:./index.php");
          }
        } catch (Exception $e) {
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
    <title>ユーザー登録 | Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body class="signup">
    <header>
    </header>

    <article class="main-wrapper">
      <div class="site-width">

        <section class="left">
          <div class="img-container">
            <img src="./img/signup.jpg" alt="signup">
          </div>
        </section>

        <section class="right">
          <div class="form-container">
            <form action="" method="post">
              <h2 class="heading">Shibastagram</h2>
              <p>柴犬の画像や動画をチェックしよう</p>
              <div class="message-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['user_id'])) echo 'err'; ?>">
                <input type="text" name="user_id" placeholder="ID (半角英数字)" value="<?php if(!empty($_POST['user_id'])) echo $_POST['user_id']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['user_id'])) echo $err_msg['user_id']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['user_name'])) echo 'err'; ?>">
                <input type="text" name="user_name" placeholder="ユーザーネーム" value="<?php if(!empty($_POST['user_name'])) echo $_POST['user_name']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['user_name'])) echo $err_msg['user_name']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                <input type="password" name="pass" placeholder="パスワード (半角英数字6文字以上)" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                <input type="password" name="pass_re" placeholder="パスワード (再入力)" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
              </div>
              <input type="submit" value="登録する">
            </form>
          </div>
          <div class="move-to-login">
            <p>アカウントをお持ちですか？<a href="./login.php">ログインする</a></p>
          </div>
        </section>

      </div>
    </article>

    <footer id="footer">
      <ul>
        <li><a href="">Shibastagramについて</a></li>
        <li>・</li>
        <li><a href="">サポート</a></li>
        <li>・</li>
        <li><a href="">利用規約</a></li>
      </ul>
      <span class="copyright">© 2019 Shibastagram</span>
    </footer>

  </body>
</html>