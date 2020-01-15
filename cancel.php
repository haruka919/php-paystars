<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('退会ページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// ==============================
// 画面処理
// ==============================
// post送信された場合
if(!empty($_POST)){
  debug('POST送信があります');
  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
    // $sql2 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :us_id';
    // $sql3 = 'UPDATE `join` SET delete_flg = 1 WHERE user_id = :us_id';
    // データの流し込み
    $data = array(':us_id' => $_SESSION['user_id']);
    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    // $stmt2 = queryPost($dbh, $sql2, $data);
    // $stmt3 = queryPost($dbh, $sql3, $data);

    if($stmt1){
      // セッション削除
      session_destroy();
      debug('退会後のセッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します');
      header("Location:index.php");
    }else{
      debug('クエリ失敗しました');
      $err_msg['common'] = MSG06;
    }

  } catch(Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG06;
  }
}
?>

<?php
$siteTitle = '退会';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
    require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">退会</h2>
        <div class="form-wrapper">
          <form class="form as_cancel" action="" method="post">
            <input type="submit" name="cancel" value="退会">
          </form>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
