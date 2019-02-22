<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('./auth.php');

// ========================================
// ログイン画面処理
// ========================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');

  // 変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  // emailの形式チェック
  validEmail($email, 'email');
  // emailの最大文字数チェック
  validMaxLen($email, 'email');

  // パスワードのバリデーション
  validPass($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションOKです');

    // 例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT password,id FROM users WHERE email = :email AND deleted = 0';
      $data = array(':email' => $email);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      // クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：'.print_r($result, true));

      // パスワード照合
      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました');

        // ログイン有効期限(デフォルトを1時間とする)
        $sesLimit = 60*60;
        // 最終ログイン日時を現在日時に
        $_SESSION['login_date'] = time();

        // ログイン保持にチェックがある場合
        if($pass_save){
          debug('ログイン保持にチェックがあります');
          // ログイン有効期限を30日にしてセット
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持にチェックはありません');
          // ログイン有効期限を1時間にセット
          $_SESSION['login_limit'] = $sesLimit;
        }
        // ユーザーIDをセッションに格納
        $_SESSION['user_id'] = $result['id'];

        // usersテーブルのlogin_dateを更新
        debug('login_dateを更新します');
        $dbh = dbConnect();
        $sql = 'UPDATE users SET login_date = :login_date WHERE id = :u_id';
        $data = array(':login_date' => date('Y-m-d H:i:s'), ':u_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        debug('login_dateを更新しました');
      }

        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('index.phpへ遷移します');
        header("Location:./index.php");
      }else{
        debug('パスワードがアンマッチです');
        $err_msg['common'] = MSG10;
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>ログイン | Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body class="login">
    <header>
    </header>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('message_success'); ?>
    </p>

    <article class="main-wrapper">
      <div class="site-width">

        <section class="form-container">
          <form action="" method="post">
            <div class="top">
              <h2 class="heading">Shibastagram</h2>
              <div class="message-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                <input type="password" name="pass" placeholder="パスワード (半角英数字6文字以上)" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
              </label>
              <div class="message-area">
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
              </div>
              <label>
                <input type="checkbox" name="pass_save"><span>ログインしたままにする</span>
              </label>
              <input type="submit" value="ログイン">
            </div>
            <div class="bottom">
            <a href="./pass-reminder.php">パスワードを忘れた場合</a>
            </div>
          </form>
        </section>
        <section class="move-to-signup">
          <p>アカウントをお持ちでないですか？<a href="./signup.php">登録する</a></p>
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

    <script src="./js/vendor/jquery-2.2.2.min.js"></script>
    <script>
      $(function(){
        // メッセージ表示
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
          $jsShowMsg.slideToggle('slow');
          setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 3000);
        }
      })
    </script>

  </body>
</html>