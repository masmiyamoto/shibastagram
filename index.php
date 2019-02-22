<?php

// 共通変数・関数ファイル読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「投稿一覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('./auth.php');

// ========================================
// 画面処理
// ========================================
// DBから投稿データを取得
$dbPostData = getPostList($_SESSION['user_id']);

debug('画面表示終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="utf-8">
    <title>Shibastagram</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </head>

  <body>
    <?php
      require('./header.php');
    ?>

    <article class="main-wrapper">
      <div class="site-width">
        <section id="main">
          <div>
            <?php
              foreach($dbPostData as $key => $val):
            ?>
              <div class="post">
                <div class="post-user-info">
                  <div class="post-user-icon">
                    <img src="<?php echo $val['posted_image']; ?>" class="user-icon2">
                  </div>
                  <div>
                    <p class="user-id"><?php echo $val['user_id']; ?></p>
                    <p class="user-name"><?php echo $val['user_name']; ?></p>
                  </div>
                </div>
                <img src="<?php echo $val['posted_image']; ?>" alt="<?php echo $val['post_id']; ?>">
                <a href=""><i class="action-icn far fa-heart"></i></a>
                <a href=""><i class="action-icn far fa-bookmark"></i></a>
                <a href=""><i class="action-icn fas fa-share-alt"></i></a>
                <div class="posted-text">
                  <p class="text"><?php echo $val['posted_text']; ?>...</p>
                  <p class="read-more"><a href="./post-detail.php<?php echo '?p_id='.$val['post_id']; ?>">続きを読む</a></p>
                </div>
                <div class="post-info">
                  <p class="comments-number"><a href="">他のコメントを表示</a></p>
                  <p class="posted-date">投稿日時：<?php echo $val['posted_at']; ?></p>
                </div>
                <input type="text" placeholder="コメントを追加..." class="comment-post1">
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="sidebar">
          <div class="sidebar-contents">
            <div class="pop-post-heading">
              <a href="" class="pp-heading">人気の投稿</a>
              <a href="" class="view-all">すべて見る</a>
            </div>
            <div class="pop-post-list">
              <a href=""><img src="./img/prof-icon01.jpg" alt=""></a>
              <div class="list-body">
                <a href="">userid</a>
                <a href="">テキストテキストテキ...</a>
              </div>
            </div>
            <div class="pop-post-list">
              <a href=""><img src="./img/prof-icon01.jpg" alt=""></a>
              <div class="list-body">
                <a href="">userid</a>
                <a href="">テキストテキストテキ...</a>
              </div>
            </div>
            <div class="pop-post-list">
              <a href=""><img src="./img/prof-icon01.jpg" alt=""></a>
              <div class="list-body">
                <a href="">userid</a>
                <a href="">テキストテキストテキ...</a>
              </div>
            </div>
            <div class="pop-post-list">
              <a href=""><img src="./img/prof-icon01.jpg" alt=""></a>
              <div class="list-body">
                <a href="">userid</a>
                <a href="">テキストテキストテキ...</a>
              </div>
            </div>
            <div class="pop-post-list">
              <a href=""><img src="./img/prof-icon01.jpg" alt=""></a>
              <div class="list-body">
                <a href="">userid</a>
                <a href="">テキストテキストテキ...</a>
              </div>
            </div>
          </div>
          <div class="sidebar-info">
            <ul>
              <li><a href="">Shibastagramについて</a></li>
              <li>・</li>
              <li><a href="">サポート</a></li>
              <li>・</li>
              <li><a href="">利用規約</a></li>
            </ul>
            © 2019 Shibastagram
          </div>
        </section>
      </div>
    </article>

    <footer>
    </footer>

  </body>
</html>