<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' プロフィール（確認画面） ');
debug('********************************************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面表示用データ取得
// =======================
// DBよりプロフィール情報を取得する
// すでに、header.phpで取得済み
// $dbFormData = getProfileData($_SESSION['user_id']);
// debug('プロフィールデータ取得：'.print_r($dbFormData, true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<**************');

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

          <img src="<?php echo showImg(getFormData('pic')); ?>" alt="<?php echo getFormData('name'); ?>" class="profile-pic">
          <div class="profile-info">
            <p class="profile-info-name"><?php echo getFormData('name'); ?></p>
            <p class="profile-info-intro"><?php echo getFormData('profile'); ?></p>
          </div>
        </div>
        <div class="profile-btnWrapper">
          <a class="profile-btn" href="profileEdit.php">プロフィール編集</a>
        </div>
      </div>

  </div>
  <?php
  require('footer.php');
  ?>
