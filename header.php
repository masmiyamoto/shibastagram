<header class="sticky">
  <div class="header-wrapper">
    <a href="./index.php"><i class="fas fa-camera"></i></a>
    <h1 class="site-logo"><a href="./index.php">Shibastagram</a></h1>
    <input type="text" placeholder="検索">
    <nav id="top-nav">
      <ul>
        <?php if(empty($_SESSION['user_id'])) : ?>
          <li><a href="./signup.php">ユーザー登録</a></li>
          <li><a href="./login.php">ログイン</a></li>
        <?php else : ?>
          <li><a href="./post.php">投稿</a></li>
          <li><a href="./logout.php">ログアウト</a></li>
          <li><a href="./userpage.php">マイページへ</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>