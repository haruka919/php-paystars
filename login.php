<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('ログインページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// ==============================
// ログイン画面処理
// ==============================
// post送信されていた場合
if(!empty($_POST)){
  debug('login.phpでpost送信があります。');

  // 変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  // emailの形式チェック
  validEmail($email, 'email');
  // emailの最大文字数チェック
  validMaxLen($email, 'email');

  // パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  // パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  // パスワードの最小文字数チェック
  validMinLen($pass, 'pass');

  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションチェックOKです');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      // クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：'.print_r($result, true));

      // パスワード照合
      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました。');

        // ログイン有効期限（デフォルトを１時間とする）
        $sesLimit = 60*60;
        // 最終ログイン日時を現在の日時に
        $_SESSION['login_date'] = time();

        // ログイン保持にチェックがある場合
        if($pass_save){
          debug('ログイン保持にチェックがあります。');
          // ログイン日時を30日にセット
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持にチェックがありません');
          $_SESSION['login_limit'] = $sesLimit;
        }
        // ユーザーIDを格納
        $_SESSION['user_id'] = $result['id'];
        debug('セッション変数の中身:'.print_r($_SESSION, true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ
        // exit();
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
$siteTitle = 'ログイン';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">ログイン</h2>
        <div class="form-wrapper">
          <form class="form" action="" method="post">
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['email'])) echo 'is-err'; ?>">
              Email
              <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass'])) echo 'is-err'; ?>">
              Password
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>

            <label>
              <input type="checkbox" name="pass_save" class="form-checkBox"><span class="checkBox-note">次回ログインを省略する</span>
            </label>
            <input type="submit" name="" value="ログイン">
            <p class="form-note">パスワード忘れた方は<a href="passRemindSend.php">こちら</a></p>
          </form>
        </div>
      </div>


  <?php
  require('footer.php');
  ?>
