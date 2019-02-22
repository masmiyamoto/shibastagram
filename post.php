<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「投稿ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('./auth.php');

// ========================================
// 画面処理
// ========================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  // 変数にユーザー情報を代入
  $posted_text = $_POST['posted_text'];
  // 画像をアップロードし、パスを格納
  $posted_image = (!empty($_FILES['posted_image']['name'])) ? uploadImg($_FILES['posted_image'], 'posted_image') : '';

  // テキストの最大文字数チェック
  validMaxLen($posted_text, 'posted_text');

  if(empty($err_msg)){
    debug('バリデーションOKです');

    // 例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      $sql= 'INSERT INTO posts (posted_user, posted_image, posted_text, posted_at) VALUES (:posted_user, :posted_image, :posted_text, :posted_at)';
      $data = array(':posted_user' => $dbFormData['id'],
                    ':posted_image' => $posted_image,
                    ':posted_text' => $posted_text,
                    ':posted_at' => date('Y-m-d H:i:s'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        debug('投稿成功。index.phpに遷移します');
        header("Location:./index.php");
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
?>

<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>投稿 | Shibastagram</title>
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
        <section id="post">
          <form action="" method="post" enctype="multipart/form-data">
            <h2>新規投稿</h2>
            <h3>1. 写真を選ぶ</h3>
            <div class="image-upload">
              <div class="image-container">
                <label>
                  <input type="file" name="posted_image" class="input-file">
                </label>
              </div>
              <div class="arrow">
                <span></span>
              </div>
              <div>
                <img src="
                  <?php
                    if(!empty($_FILES['posted_image']['name'])){
                    echo $_FILES['posted_image']['name'];
                    }else{
                      echo './img/noimage2.jpg';
                    }
                  ?>" alt="noimage" class="prev-img">
              </div>
            </div>
            <h3>2. キャプションを書く</h3>
            <label>
              <textarea name="posted_text" id="js-count" cols="30" rows="10"></textarea>
            </label>
            <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
            <div class="message-area">
              <?php if(!empty($err_msg['posted_text'])) ?>
            </div>
            <input type="submit" value="投稿する">
          </form>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>