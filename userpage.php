<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザーページ');
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
// DBから投稿データを取得
$dbPostData = getPostList($_SESSION['user_id']);

debug('画面表示終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>@<?php echo $dbFormData['user_id']; ?> | Shibastagram</title>
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
        <section id="user-info">
          <div class="user-info-left">
            <img src="<?php echo $dbFormData['prof_icon']; ?>" alt="" class="user-icon">
          </div>
          <div class="user-info-right">
            <div class="user-info-r-top">
              <p class="userid"><?php echo $dbFormData['user_id']; ?></p>
              <p><a href="./prof-edit.php" class="edit-prof">プロフィールを編集</a></p>
            </div>
            <div class="user-info-r-middle">
              <ul>
                <li>投稿<span class="font-weight-bold">123</span>件</li>
                <li><a href="">フォロワー<span class="font-weight-bold">123</span>人</a></li>
                <li><a href=""><span class="font-weight-bold">123</span>人をフォロー中</a></li>
              </ul>
            </div>
            <div class="user-info-r-bottom">
              <h2><?php echo $dbFormData['user_name']; ?></h2>
              <p><?php echo $dbFormData['self_introduction']; ?></p>
            </div>
          </div>
        </section>

        <section id="posted-list">
          <h2>投稿一覧</h2>
          <div class="posted-images">
            <?php
              foreach($dbPostData as $key => $val):
            ?>
            <div class="userpage-image">
              <a href="./post-detail.php<?php echo '?p_id='.$val['post_id']; ?>"><img src="<?php echo sanitize($val['posted_image']); ?>" alt=""></a>
            </div>
              <?php endforeach; ?>
          </div>
        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>