<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('新規登録ページ');
debug('***********************************************************');
debugLogStart();

// post送信がされていた場合
if(!empty($_POST)){

  // 変数にユーザー情報を代入
  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  // 未入力チェック
  validRequired($name, 'name');
  validRequired($email, 'email');
  validRequired($email, 'pass');
  validRequired($email, 'pass');

  if(empty($err_msg)){
    // 名前最大文字数チェック
    validMaxLen($name, 'name', 50);

    // emailの形式チェック
    validEmail($email, 'email');
    // Email最大文字数チェック
    validMaxLen($email, 'email');
    // email重複チェック
    validEmailDup($email);

    // パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    // 最大文字数チェック
    validMaxLen($pass, 'pass');
    // 最小文字数チェック
    validMinLen($pass, 'pass');

    if(empty($err_msg)){
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)){

        // 例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'INSERT INTO users (name, email, password, login_time, create_date) VALUES (:name, :email, :pass, :login_time, :create_date)';
          $data = array(':name' => $name, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);
          // クエリ成功の場合
          if($stmt){
            // ログイン有効期限（デフォルトを１時間とする）
            $sesLimit = 60*60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ログインユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();
            debug('新規登録後のセッション変数の中身：'.print_r($_SESSION,true));
            
            header("Location:mypage.php");
          }
        } catch(Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}

?>
<?php
$siteTitle = '新規登録';
require('head.php');
?>
<body>
  <div class="wrap">

        <?php
        require('header.php');
        ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">新規登録</h2>
        <div class="form-wrapper">
          <form class="form" action="" method="post">
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['name'])) echo 'is-err'; ?>">
              名前
              <input type="text" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
            </label>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['name'])) echo $err_msg['name'];
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
            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'is-err'; ?>">
              Password(再入力)
              <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
            </label>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
              ?>
            </div>
            <input type="submit" name="" value="登録">
          </form>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
