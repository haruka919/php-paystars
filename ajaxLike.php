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
if(isset($_POST['playerId']) && isset($_SESSION['user_id']) &&isLogin()){
  debug('POST送信があります。');
  $p_id = $_POST['playerId'];
  debug('選手の背番号：'.$p_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM favorite WHERE player_id = :p_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    // レコードが1件でもある場合
    if(!empty($resultCount)){
      // レコードを削除する
      $sql = 'DELETE FROM favorite WHERE player_id = :p_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      // レコードを挿入する
      $sql = 'INSERT INTO favorite (player_id, user_id, create_date) VALUES (:p_id, :u_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
