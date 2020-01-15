<?php
// ==============================
// ログ
// ==============================
ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

// ==============================
// デバック
// ==============================
// デバックフラグ
$debug_flg = false;
// デバック関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバック：'.$str);
  }
}

// ==============================
// セッション準備・セッションの有効期限を延ばす
// ==============================
// セッションの置き場を変更する
session_save_path("/var/tmp");
// ガーページコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
// セッションを使う
session_start();
// 現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

// ==============================
// 画面表示処理開始ログ吐き出し関数
// ==============================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID:'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期日日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}
// ==============================
// 定数
// ==============================
// エラーメッセージを定数に設定
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', '文字以内で入力してください');
define('MSG04', '6文字以上で入力してください');
define('MSG05', 'そのemailアドレスはすでに登録済みです');
define('MSG06', 'エラーが発生しました。しばらく経ってからやり直してください');
define('MSG07', '半角で入力してください');
define('MSG08', 'パスワード（再入力）が合っていません');
define('MSG09', 'メールアドレスもしくはパスワードが合っていません');
define('MSG10', '半角英語で入力してください');
define('MSG11', '半角数字で入力してください');
define('MSG12', '選択してください');
define('MSG13', '全て入力してください');
define('MSG14', '正しく入力してください');
define('MSG15', 'その背番号はすでに他の選手が使用しています');
define('MSG16', '写真を登録してください');

// ==============================
// グローバル変数
// ==============================
// エラーメッセージ格納用の配列
$err_msg = array();

// ==============================
// バリデーション関数
// ==============================
// 未入力チェック
function validRequired($str, $key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
// 未入力チェック（写真）
function validRequiredPic($str, $key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG16;
  }
}
// email形式チェック
function validEmail($str, $key){
	//大抵のメールアドレスはこれで問題なし
	if(filter_var($str, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	//RFC違反のメールアドレスがあるdocomoとauだけ、救済チェックを行う
	if(strpos($str, '@docomo.ne.jp') !== false || strpos($str, '@ezweb.ne.jp') !== false) {
		$pattern = '/^([a-zA-Z])+([a-zA-Z0-9\._-])*@(docomo\.ne\.jp|ezweb\.ne\.jp)$/';
		if(preg_match($pattern, $str, $matches) === 1) {
			return true;
		}
	}
	return false;
}
// email重複チェック
function validEmailDup($email){
  global $err_msg;
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG05;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG06;
  }
}
// 背番号重複チェック
function validPlayernumDup($playernum){
  global $err_msg;
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM players WHERE playernum = :playernum AND delete_flg = 0';
    $data = array(':playernum' => $playernum);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['playernum'] = MSG15;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG06;
  }
}

// 最大文字数チェック
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = $max.MSG03;
  }
}
// 最小文字数チェック
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
// 半角チェック
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG07;
  }
}
// 半角英語チェック
function validEnglish($str, $key){
  if(!preg_match("/^[a-zA-Z]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
// 半角数字チェック
function validNumber($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}
// 同値チェック
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG08;
  }
}
// セレクトボックスチェック
function validSelect($str, $key){
  if(!preg_match("/^[1-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
// 未入力チェック（誕生日）
function validRequiredBirth($str1, $str2, $str3, $key1, $key2, $key3){
  if($str1 === '' || $str2 === ''  || $str3 === ''){
    global $err_msg;
    $err_msg[$key1] = MSG13;
    $err_msg[$key2] = MSG13;
    $err_msg[$key3] = MSG13;
  }
}
// 誕生日形式チェック
function validBirth($str1, $str2, $str3, $key1, $key2, $key3){
  global $birthday;
  $birthday = $str1 . "-" . $str2 . "-" . $str3;
    if (checkdate($str2, $str3, $str1) === false) { //ありえない日付だったら
      global $err_msg;
      $err_msg[$key1] = MSG14;
      $err_msg[$key2] = MSG14;
      $err_msg[$key3] = MSG14;
      debug('失敗した誕生日：'.print_r($birthday, true));
    }
    debug('成功した誕生日：'.print_r($birthday, true));

}
// ==============================
// DB接続関数
// ==============================
// DBへ接続設定
function dbConnect(){
  //DBへ接続準備
  $dsn = '';
  $user = '';
  $password = '';  //macはroot
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
function queryPost($dbh, $sql, $data){
  // クエリ作成
  $stmt = $dbh->prepare($sql);
  // プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました');
    debug('失敗したSQL：'.print_r($stmt->errorInfo(),true));
    $err_msg['common'] = MSG06;
    return 0;
  }
  debug('クエリ成功');
  return $stmt;
}
// ==============================
// データベース
// ==============================
// 選手データを取得（全員分）
function getPlayer(){
  debug('選手データ（全員）を取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM players';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 選手データを取得（1人分）
function getPlayerData($p_id){
  debug('選手データ（1人分）を取得します');
  debug('選手ID:'.$p_id);
  // 例外処理
  try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT p.id, p.name, p.ename, p.playernum, p.position_id, p.song, p.birthday, p.height, p.weight, p.pic, b.name AS bloodtype, f.name AS prefecture FROM players AS p
      LEFT JOIN bloodtype AS b ON p.bloodtype_id = b.id LEFT JOIN prefecture AS f ON p.prefecture_id = f.id WHERE p.id = :p_id AND p.delete_flg = 0';
      $data = array(':p_id' => $p_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        // クエリ結果の1レコードを返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 選手データを取得（登録用）
function getPlayerResistData($p_id){
  debug('選手データ（登録用）を取得します');
  debug('選手ID:'.$p_id);
  // 例外処理
  try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM players WHERE id = :p_id AND delete_flg = 0';
      $data = array(':p_id' => $p_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        // クエリ結果の1レコードを返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 選手データを取得（全員とポジション別）[id,name,pic]
function getPlayerList($c_id){
  debug('選手データ（全員とポジション別）を取得します');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id, name, playernum, pic FROM players WHERE delete_flg = 0';
    if(!empty($c_id)) $sql .= ' AND position_id = '.$c_id;
    $data = array();
    debug('SQL：'.$sql);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
      debug('選手データ取得：'.print_r($stmt,true));
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 血液型データを取得
function getBloodType(){
  debug('血液型データを取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id,name FROM bloodtype';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// ポジションデータを取得
function getPosition(){
  debug('ポジションデータを取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id,name FROM position';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 都道府県データを取得
function getPrefecture(){
  debug('都道府県データを取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id,name FROM prefecture';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 誕生日を取得
function getAge($p_id){
  debug('誕生日を取得します');
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT (YEAR(CURDATE()) - YEAR(birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(birthday, 5)) AS age FROM players WHERE id = :p_id AND delete_flg = 0';
    $data = array(':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// お気に入りの選手リストを取得
function getFavoriteData($u_id){
  debug('お気に入りの選手リストを取得します');
  debug('ユーザーID:'.$u_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT p.id, p.name ,p.pic FROM players AS p RIGHT JOIN favorite AS f ON p.id = f.player_id AND p.delete_flg = 0 AND f.delete_flg = 0 WHERE f.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// プロフィールデータを取得
function getProfileData($u_id){
  debug('プロフィール情報を取得します。');
  debug('ユーザーID:'.$u_id);
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// 掲示板データを取得
function getBordData($b_id, $u_id){
  debug('該当の掲示板データ(1つ)を取得します。');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM bord WHERE id = $b_id AND user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' . $e->getMessage());
  }
}
// 掲示板（リスト）データを取得
function getBordListData(){
  debug('掲示板データ(リスト)を取得します。');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM bord ORDER BY created_date DESC';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより掲示板データを取得
function getBord($b_id){
  debug('掲示板データを取得');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT b.title, b.info, b.user_id, b.created_date, u.id, u.name ,u.pic FROM bord AS b LEFT JOIN users AS u ON b.user_id = u.id WHERE b.id = :b_id';
    $data = array(':b_id' => $b_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の1レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより掲示板のメッセージデータを取得
function getMsgBord($b_id){
  debug('掲示板のメッセージを取得します。');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT m.id, m.user_id, m.msg, m.create_date, u.name, u.pic FROM bord AS b LEFT JOIN message AS m ON b.id = m.bord_id LEFT JOIN users AS u ON m.user_id = u.id WHERE b.id = :b_id ORDER BY m.create_date DESC';
    $data = array(':b_id' => $b_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより掲示板の参加者情報をを取得
function getJoinBord($b_id){
  debug('掲示板の参加者情報を取得');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT u.id, u.name, u.pic FROM bord AS b LEFT JOIN `join` AS j ON b.id = j.bord_id LEFT JOIN users AS u ON j.user_id = u.id WHERE b.id = :b_id';
    $data = array(':b_id' => $b_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより自分が参加している掲示板情報を取得
function getMyJoinBord($u_id){
  debug('自分が参加している掲示板情報を取得します');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT b.id, b.title, b.created_date FROM `join` AS j LEFT JOIN bord AS b ON j.bord_id = b.id WHERE j.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより自分が管理している掲示板情報を取得
function getMyCreateBord($u_id){
  debug('自分が管理している掲示板情報を取得します');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id, title, created_date FROM bord WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// DBより自分の写真を取得
function getMyPic($u_id){
  debug('自分の写真を取得します');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT pic FROM users WHERE id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// ランキングの数を取得する(TOPページ)
function getRankingTop(){
  debug('人気TOP3を取得します');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT P.id, P.name, P.pic, count(F.player_id) AS ranking FROM favorite AS F
    LEFT JOIN players AS P
    ON F.player_id = P.id
    GROUP BY P.id, P.name, P.pic
    ORDER BY ranking desc';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
function uploadImg($file, $key){
  debug('画像アップロード処理');
  debug('FILE情報：'.print_r($file, true));

  if(isset($file['error']) && is_int($file['error'])){
    try{
      switch($file['error']){
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズが超過した場合
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズ超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default: //その他の場合
          throw new RuntimeException('その他のエラーが発生しました');
      }
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
        throw new RuntimeException('画像形式が未対応です');
      }

      $path = 'uploads/players/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if(!move_uploaded_file($file['tmp_name'], $path)){
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      // 保存したファイルのパーミッション（権限）を変更する
      chmod($path, 0644);
      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;
    }catch(RuntimeException $e){
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}
// ==============================
// その他
// ==============================
function isJoin($b_id, $u_id){
  debug('参加情報があるか取得します。');
  debug('ユーザーID:'.$u_id);
  debug('掲示板ID:'.$b_id);
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM `join` WHERE bord_id = :b_id AND user_id = :u_id';
    $data = array(':b_id' => $b_id, ':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt->rowCount()){
      debug('参加しています');
      return true;
    }else{
      debug('参加していません');
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function isLike($u_id, $p_id){
  debug('お気に入り情報があるか取得します。');
  debug('ユーザーID:'.$u_id);
  debug('選手ID:'.$p_id);
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM favorite WHERE player_id = :p_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt->rowCount()){
      debug('お気に入りです。');
      return true;
    }else{
      debug('特に気に入っていません');
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function isLogin(){
  // ログインしている場合
  if( !empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');
    if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです。');

      // セッションを削除（ログアウトする）
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限以内です。');
      return true;
    }
  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}
function sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}
// フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET; // デフォルトは$_GET
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  // ユーザーもしくは選手データがある場合
  if(!empty($dbFormData)){
    // フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
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
    if(isset ($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// フォーム入力保持(選手)
function getPlayerFormData($str, $flg = false){
  if($flg){
    $method = $_GET; // デフォルトは$_GET
  }else{
    $method = $_POST;
  }
  global $dbPlayerData;
  // ユーザーもしくは選手データがある場合
  if(!empty($dbPlayerData)){
    // フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbPlayerData[$str]);
      }
    }else{
      // POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbPlayerData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbPlayerData[$str]);
      }
    }
  }else{
    if(isset ($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// 画像保持
function getImgData($str){
  $method = $_FILES;
  global $dbFormData;
  // ユーザーもしくは選手データがある場合
  if(!empty($dbFormData)){
    // フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
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
    if(isset ($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// 画像表示関数
function showImg($path){
  if(empty($path)){
    return 'img/sample-img.png';
  }else{
    return $path;
  }
}
// 全角・半角削除
function mbTrim($name){
  return preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $name);
}
?>
