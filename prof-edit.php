<?php

// 共通変数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「プロフィール編集ページ');
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

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  // 変数にユーザー情報を代入
  $user_name = $_POST['user_name'];
  $website = $_POST['website'];
  $self_introduction = $_POST['self_introduction'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  // 画像をアップロードし、パスを格納
  $prof_icon = (!empty($_FILES['prof_icon']['name'])) ? uploadImg($_FILES['prof_icon'], 'prof_icon') : '';
  // 画像をPOSTしていないが既にDBに登録がある場合、DBのパスを入れる
  $prof_icon = (empty($prof_icon) && !empty($dbFormData['prof_icon'])) ? $dbFormData['prof_icon'] : $prof_icon ;

  // DBの情報と入力情報が異なる場合にバリデーションを行う
  // 名前
  if($dbFormData['user_name'] !== $user_name){
    validMaxLen($user_name, 'user_name');
  }
  // ウェブサイト
  if($dbFormData['website'] !== $website){
    validWebsite($website, 'website');
    ValidMaxLen($website, 'website');
  }
  // 自己紹介
  if($dbFormData['self_introduction'] !== $self_introduction){
    validMaxLen($self_introduction, 'self_introduction');
  }
  // メールアドレス
  if($dbFormData['email'] !== $email){
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validRequired($email, 'email');
  }
  // 年齢
  if($dbFormData['age'] !== $age){
    validHalf($age, 'age');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです');

    // 例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET prof_icon = :prof_icon, user_name = :user_name, website = :website, self_introduction = :self_introduction, email = :email, gender = :gender, age = :age WHERE id = :u_id';
      $data = array(':prof_icon' => $prof_icon, ':user_name' => $user_name, ':website' => $website, ':self_introduction' => $self_introduction, ':email' => $email, ':gender' => $gender, ':age' => $age, ':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('userpage.phpに遷移します');
        header("Location:./userpage.php");
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
    <title>プロフィールを編集 | Shibastagram</title>
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
            <li><span class="active">プロフィールを編集</span></li>
            <li><a href="./pass-edit.php">パスワードを変更</a></li>
            <li><a href="./unsubscribe.php">退会</a></li>
          </ul>
        </section>

        <section class="right-list">
          <form action="" method="post" enctype="multipart/form-data">
            <div class="prof-edit-item">
              <aside>
                <img src="
                <?php
                  if(!empty(getFormData('prof_icon'))) {
                    echo getFormData('prof_icon');
                  }else{
                    echo './img/noimage.jpg';
                  }
                ?>" alt="" class="prev-img">
              </aside>
              <div class="edit-input">
                <h2><?php echo getFormData('user_id'); ?></h2>
                <label class="<?php if(!empty($err_msg['prof_icon'])) echo 'err'; ?>">
                  <span>プロフィール写真を変更</span>
                  <input type="file" name="prof_icon" class="input-file">
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['prof_icon'])) echo $err_msg['prof_icon']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                名前
              </aside>
              <div class="edit-input">
                <label class="<?php if(!empty($err_msg['user_name'])) echo 'err'; ?>">
                  <input type="text" name="user_name" value="<?php echo getFormData('user_name'); ?>">
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['user_name'])) echo $err_msg['user_name']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                ウェブサイト
              </aside>
              <div class="edit-input">
                <label class="<?php if(!empty($err_msg['website'])) echo 'err'; ?>">
                  <input type="text" name="website" value="<?php echo getFormData('website'); ?>">
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['website'])) echo $err_msg['website']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                自己紹介
              </aside>
              <div class="edit-input">
                <label class="<?php if(!empty($err_msg['self_introduction'])) echo 'err'; ?>">
                  <textarea name="self_introduction" id="" cols="30" rows="10"><?php echo getFormData('self_introduction'); ?></textarea>
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['self_introduction'])) echo $err_msg['self_introduction']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                メールアドレス
              </aside>
              <div class="edit-input">
                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                  <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                性別
              </aside>
              <div class="edit-input">
                <label  class="<?php if(!empty($err_msg['gender'])) echo 'err'; ?>">
                  <select name="gender">
                    <option value="0" <?php if(getFormData('gender') == 0 ) echo 'selected'; ?>>指定なし</option>
                    <option value="1" <?php if(getFormData('gender') == 1 ) echo 'selected'; ?>>男性</option>
                    <option value="2" <?php if(getFormData('gender') == 2 ) echo 'selected'; ?>>女性</option>
                  </select>
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['gender'])) echo $err_msg['gender']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>
                年齢
              </aside>
              <div class="edit-input">
                <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
                  <input type="text" class="age" name="age" value="<?php echo getFormData('age'); ?>">
                </label>
                <div class="message-area2">
                  <?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?>
                </div>
              </div>
            </div>
            <div class="prof-edit-item">
              <aside>

              </aside>
              <div class="edit-input">
                <label>
                  <input type="submit" value="変更する">
                </label>
              </div>
            </div>
          </form>

        </section>

      </div>
    </article>

    <?php
      require('./footer.php');
    ?>

  </body>
</html>