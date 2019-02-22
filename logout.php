<?php
// 共通関数・関数ファイルを読込み
require('./function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログアウト機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('ログアウトします');
// セッションを削除(ログアウトする)
session_destroy();
debug('ログインページへ遷移します');
header("Location:./login.php");
?>