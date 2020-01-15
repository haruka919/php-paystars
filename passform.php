<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('パスワード入力ページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// パスワード認証
require('pass.php');
// ==============================
// ログイン画面処理
// ==============================
// post送信されていた場合
if(!empty($_POST)){
  debug('passform.phpでpost送信があります。');

  // 変数にユーザー情報を代入
  $pass = $_POST['pass'];

  // パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  // パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  // パスワードの最小文字数チェック
  validMinLen($pass, 'pass');

  // 未入力チェック
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションチェックOKです');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT password FROM password';
      $data = array();
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      // クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：'.print_r($result, true));

      // パスワード照合
      if(!empty($result) && strpos($result['password'],$pass) !== false && mb_strlen($pass) == mb_strlen($result['password'])){
        debug('パスワードがマッチしました。');

        // ログイン有効期限（デフォルトを１時間とする）
        $sesLimit = 60*60;
        // 最終ログイン日時を現在の日時に
        $_SESSION['pass_date'] = time();
        $_SESSION['pass_limit'] = $sesLimit;

        // ユーザーIDを格納
        debug('セッション変数の中身:'.print_r($_SESSION, true));
        debug('選手登録リストへ遷移します。');
        header("Location:registList.php"); //マイページへ
        exit();
      }else{
        debug('パスワードがアンマッチです');
        $err_msg['common'] = MSG09;
      }
    } catch (Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}
debug('画面表示終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'パスワード';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">パスワード</h2>
        <div class="form-wrapper">
          <form class="form" action="" method="post">
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass'])) echo 'pass'; ?>">
              パスワード（選手登録用）
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>

            <input type="submit" name="" value="ログイン">
          </form>
        </div>
      </div>


  <?php
  require('footer.php');
  ?>
