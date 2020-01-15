<?php
// ==============================
// パスワード認証・自動ログアウト
// ==============================
// パスワードしている場合
if(!empty($_SESSION['pass_date'])){
  debug('パスワード認証済みユーザーです');

  // 現在日時が最終ログイン日時＋有効期限を超えていた場合
  if( ($_SESSION['pass_date'] + $_SESSION['pass_limit']) < time()){
    debug('パスワード有効期限オーバーです。');

    // セッションを削除
    session_destroy();
    // ログインページへ
    header("Location:passform.php");

  }else{
    debug('パスワード有効期限以内です');
    // 最終ログイン日時を更新
    $_SESSION['pass_date'] = time();

    if(basename($_SERVER['PHP_SELF']) === 'passform.php'){
      debug('登録一覧へ遷移します');
      header("Location:registList.php"); //マイページへ
    }
  }
}else{
  debug('パスワード未入力です');
  if(basename($_SERVER['PHP_SELF']) !== 'passform.php'){
    header("Location:passform.php");//パスワード入力ページへ
  }
}

?>
