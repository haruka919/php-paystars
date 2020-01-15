<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' プロフィール（編集画面） ');
debug('********************************************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面表示用データ取得
// =======================
// DBよりプロフィール情報を取得する
$dbFormData = getProfileData($_SESSION['user_id']);
debug('プロフィールデータ取得：'.print_r($dbFormData, true));

// post送信されたら
if(!empty($_POST)){
  debug('POST送信されました。');
  debug('POST送信'.print_r($_POST, true));
  debug('FILES送信'.print_r($_FILES, true));
  // 変数を代入
  $name = $_POST['name'];
  $name = mbTrim($name);
  $profile = $_POST['profile'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic'):'';
  $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  debug('picの中身：'.$pic);

  if(empty($dbFormData)){
    debug('バリデーションチェック');
    // 未入力チェック（名前）
    validRequired($name, 'name');
    // 最大文字数チェック（名前）
    validMaxLen($name, 'name',50);
    // 最大文字数チェック（プロフィール）
    validMaxLen($profile, 'profile', 500);

  }else{
    debug('DBデータあり');
    if($dbFormData['name'] !== $_POST['name']){
      // 未入力チェック（名前）
      validRequired($name, 'name');
      // 最大文字数チェック（名前）
      validMaxLen($name, 'name',50);
    }
    if($dbFormData['profile'] !== $_POST['profile']){
      // 最大文字数チェック（名前）
      validMaxLen($profile, 'profile', 500);
    }
  }
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    try{
      // DB接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET name = :name, profile = :profile, pic = :pic WHERE id = :u_id';
      $data = array(':name' => $name, ':profile' => $profile, ':pic' => $pic, ':u_id' => $_SESSION['user_id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        debug('profile画面に戻ります');
        header("Location: profile.php");
        exit();
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<?php
$siteTitle = 'プロフィール ';
require('head.php');
?>
<body>
  <div class="wrap">

        <?php
        require('header.php');
        ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">プロフィール</h2>
        <div class="profile-wrapper">

          <img src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt="<?php echo sanitize($dbFormData['name']); ?>" class="profile-pic">
          <div class="profile-info">
            <p class="profile-info-name"><?php echo sanitize($dbFormData['name']); ?></p>
            <p class="profile-info-intro"><?php echo sanitize($dbFormData['profile']); ?></p>
          </div>
        </div>
        <div class="profile-btnWrapper">
          <a class="profile-btn">プロフィール編集</a>
        </div>
      </div>


<!-- 入力フォーム -->
    <div class="profileForm-wrapper">
      <form class="profileForm" action="" method="post" enctype="multipart/form-data">
        <div class="profileFrom-top">
          <a href="profile.php"><i class="fas fa-times clossIcon fn-closebtn"></i></a>
          <h3 class="profileFrom-top-ttl">プロフィールを編集</h3>
          <input class="as_profileSave" type="submit" name="submit" value="保存">
        </div>
        <div class="is-areaMsg">
          <?php
          if(!empty($err_msg['comon'])) echo $err_msg['common'];
          ?>
        </div>

        <label class="form-pic as-profile <?php if(!empty($err_msg['pic'])) echo 'is-err'; ?>">
          <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
          <input type="file" class="form-pic-file" name="pic" value="">
          <img class="form-previmg as-profileEdit" src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt="">
          <i class="fas fa-plus profile-icon"></i>
        </label>
        <div class="is-areaMsg">
          <?php
          if(!empty($err_msg['pic'])) echo $err_msg['pic'];
          ?>
        </div>

        <label class="form-group <?php if(!empty($err_msg['name'])) echo 'is-err'; ?>">
          名前
          <input class="fn-profileName as_profileForm" type="text" name="name" value="<?php echo sanitize($dbFormData['name']); ?>">
          <div class="profileForm-countWrapper">
            <p><span class="counter-number1"><?php echo mb_strlen(sanitize($dbFormData['name'])); ?></span>/50</p>
          </div>
          <div class="is-areaMsg">
            <?php
            if(!empty($err_msg['name'])) echo $err_msg['name'];
            ?>
          </div>
        </label>

        <label class="form-group <?php if(!empty($err_msg['pic'])) echo 'is-err'; ?>">
          自己紹介
          <textarea class="fn-profileProfile profileForm-selfintro" name="profile" rows="8" cols="80"><?php echo sanitize($dbFormData['profile']); ?></textarea>
          <div class="profileForm-countWrapper">
            <p><span class="counter-number2"><?php echo mb_strlen(sanitize($dbFormData['profile'])); ?></span>/160</p>
          </div>
          <div class="is-areaMsg">
            <?php
            if(!empty($err_msg['profile'])) echo $err_msg['profile'];
            ?>
          </div>
        </label>

      </form>
    </div>


  </div>
  <?php
  require('footer.php');
  ?>
