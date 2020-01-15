<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug(' Ajax ');
debug('***********************************************************');
debugLogStart();

// =======================
// Ajax処理
// -----------------------

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['msgId']) && isset($_SESSION['user_id']) &&isLogin()){
  debug('POST送信があります。');
  $m_id = $_POST['msgId'];
  debug('メッセージID：'.$m_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    // レコードを削除する
    $sql = 'DELETE FROM message WHERE id = :id AND user_id = :u_id';
    $data = array(':id' => $m_id, ':u_id' => $_SESSION['user_id']);
      // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    }
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
