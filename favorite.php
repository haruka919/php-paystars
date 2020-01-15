<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug(' お気に入り ');
debug('***********************************************************');
debugLogStart();

// 画面表示用データ取得
// =======================
// DBより、お気に入りにしている選手のデータを取得
$dbFavoriteData = getFavoriteData($_SESSION['user_id']);
debug('お気に入りの選手リスト：'.print_r($dbFavoriteData, true));

?>
<?php
$siteTitle = 'お気に入り';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="cheering main-outer">
        <h2 class="main-heading">お気に入り</h2>

    <?php
    if(isLogin()){
    ?>

    <div class="cheering-players-outer">
      <ul class="cheering-players">
        <?php
        foreach ($dbFavoriteData as $key => $val) {
        ?>
          <li class="cheering-player"><a href="cheeringDetail.php?p_id=<?php echo sanitize($val['id']); ?>"><img class="cheering-player-pic" src="<?php echo $val['pic'];?>" /><p class="cheering-player-name"><?php echo sanitize($val['name']); ?></p></a></li>
        <?php
        }
        ?>
        <!-- <li class="cheering-player"><a href="cheeringDetail.php"><img class="cheering-player-pic" src="img/11_azuma.png" />選手名</a></li>
        <li class="cheering-player"><a href="cheeringDetail.php"><img class="cheering-player-pic" src="img/11_azuma.png" />選手名</a></li>
        <li class="cheering-player"><a href="cheeringDetail.php"><img class="cheering-player-pic" src="img/11_azuma.png" />選手名</a></li>
        <li class="cheering-player"><a href="cheeringDetail.php"><img class="cheering-player-pic" src="img/11_azuma.png" />選手名</a></li> -->
      </ul>
    </div>

    <?php
    }else{
    ?>

    <p class="favorite-note">※お気に入りのご利用には<a href="login.php">ログイン</a>が必要です。</p>

    <?php
    }
    ?>

    </div>


    <?php
    require('footer.php');
    ?>
