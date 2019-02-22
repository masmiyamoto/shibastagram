<?php

// 共通変数・関数ファイル読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「投稿詳細ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証(出来れば外したい)
require('./auth.php');

// ========================================
// 画面処理
// ========================================



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
        <section id="posted-image">
          <div class="posted-image">
            <img src="./img/sample03.jpg" alt="投稿画像">
          </div>
        </section>

        <section id="comments">
          <div class="comments-area">
            <div class="poster-info">
              <a href=""><img src="./img/prof-icon01.jpg" alt="" class="poster-image"></a>
              <div class="poster-name">
                <a href=""><p class="user-id">userid</p></a>
                <a href=""><p class="user-name">username</p></a>
              </div>
              <a href="" class="follow">フォローする</a>
            </div>
            <p class="text">テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</p>
            <p class="comments-heading">コメント一覧</p>
            <div class="comments">
              <div class="comment">
                <a href="" class="follower-id">followerid</a>
                <span>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</span>
              </div>
              <div class="comment">
                <a href="" class="follower-id">followerid</a>
                <span>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</span>
              </div>
              <div class="comment">
                <a href="" class="follower-id">followerid</a>
                <span>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</span>
              </div>
              <div class="comment">
                <a href="" class="follower-id">followerid</a>
                <span>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</span>
              </div>
            </div>
            <a href=""><i class="action-icn far fa-heart"></i></a>
            <a href=""><i class="action-icn far fa-bookmark"></i></a>
            <a href=""><i class="action-icn fas fa-share-alt"></i></a>
            <p class="fav-number"><a href="">いいね！123456件</a></p>
            <p class="posted-date2">投稿：2019年1月1日</p>
            <input type="text" placeholder="コメントを追加..." class="comment-post1">
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