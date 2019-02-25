<?php
// ========================================
// ログ
// ========================================
// ログを取るか
ini_set('log_errors', 'on');
// ログの出力ファイルを指定
ini_set('error_log', 'php.log');

// ========================================
// デバッグ
// ========================================
// デバッグフラグ
$debug_flg = false;
// デバッグログ関数
function debug($str) {
  global $debug_flg;
  if($debug_flg){
    error_log('デバッグ：'.$str);
  }
}

// ========================================
// セッション準備・セッション有効期限を延ばす
// ========================================
// セッションファイルの置き場を変更(30日は削除されない)
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を延長
ini_set('session.cookie_lifetime', 60*60*24*30);

session_start();
session_regenerate_id();

// ========================================
// 画面表示処理開始ログ吐き出し用
// ========================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

// ========================================
// 定数
// ========================================
// システムメッセージを定数に設定
define('MSG01', '入力必須です');
define('MSG02', 'Eメールの形式で入力してください');
define('MSG03', 'パスワード(再入力)が合っていません');
define('MSG04', '半角英数字のみご利用いただけます');
define('MSG05', '6文字以上で入力してください');
define('MSG06', '255文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08', 'そのメールアドレスは既に登録されています');
define('MSG09', 'そのIDは既に登録されています');
define('MSG10', 'メールアドレスまたはパスワードが違います');
define('MSG11', 'http://もしくはhttps://から始まる正しい形式で入力してください');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');

// ========================================
// グローバル変数
// ========================================
// エラーメッセージ格納用
$err_msg = array();

// ========================================
// バリデーション関数
// ========================================

// 未入力チェック
function validRequired($str, $key){
  if($str === ''){ // 空文字のみ
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
// Email形式チェック
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
// Email重複チェック
function validEmailDup($email){
  global $err_msg;
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
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// user_id重複チェック
function validUserIdDup($user_id){
  global $err_msg;
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE user_id = :user_id AND deleted = 0';
    $data = array(':user_id' => $user_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['user_id'] = MSG09;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// 同値チェック
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
// 最小文字数チェック
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
// 最大文字数チェック
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
// 半角チェック
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
function validLength($str, $key, $len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len . MSG14;
  }
}
// パスワードチェック
function validPass($str, $key){
  // 最小文字数チェック
  validMinLen($str, $key);
  // 最大文字数チェック
  validMaxLen($str, $key);
  // 半角英数字チェック
  validHalf($str, $key);
}

// ウェブサイトチェック
function validWebsite($str, $key){
  if(!preg_match("/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}

// ========================================
// データベース
// ========================================
function dbConnect(){
  // DBへの接続準備
  $dsn = 'mysql:dbname=shibastagram;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時にの動作
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成(DBへ接続)
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
function queryPost($dbh, $sql, $data){
  // クエリー作成
  $stmt = $dbh->prepare($sql);
  // プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功');
  return $stmt;
}
function getUser($u_id){
  debug('ユーザー情報を取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id AND deleted = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ結果のデータを1つ返す
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}
function getPostList($id){
  debug('投稿データを取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    $sql = 'SELECT post_id, posted_image, posted_text, posted_at, user_id, user_name, prof_icon FROM posts AS p LEFT JOIN users AS u ON p.posted_user = u.id WHERE posted_user = :posted_user AND p.deleted = 0 ORDER BY post_id DESC';
    $data = array(':posted_user' => $id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果を全取得
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}

// ========================================
// メール送信
// ========================================
function sendMail($from, $to, $subject, $comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    // 文字化けしないように設定
    mb_language("ja");
    mb_internal_encoding("UTF-8");

    // メールを送信
    $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
    // 送信結果を判定
    if($result){
      debug('メールを送信しました。');
    }else{
      debug('エラー発生：メールの送信に失敗しました');
    }
  }
}

// ========================================
// その他
// ========================================
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
// フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  // ユーザーデータがある場合
  if(!empty($dbFormData)){
    // フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        // ない場合はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }else{
      // POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// SSESSIONを1回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
// 認証キー生成
function makeRandKey($length = 8) {
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i = 0; $i < $length; ++$i) {
    $str .= $chars[mt_rand(0, 61)];
  }
  return $str;
}
// 画像処理
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if(isset($file['error']) && is_int($file['error'])) {
    try {
      // $file['error']の確認
      switch ($file['error']) {
        case UPLOAD_ERR_OK: // OK
          break;
        case UPLOAD_ERR_NO_FILE: // ファイル未選択
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE: // php.ini定義の最大サイズが超過した場合
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
          throw new RuntimeException('その他のエラーが発生しました');
      }

      // MIMEタイプチェック
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type,[IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        throw new RuntimeException('画像形式が未対応です');
      }

      // ファイルデータからファイル名を取得してハッシュ化して保存
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      // 保存したファイルパスのパーミッション(権限)を変更
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}