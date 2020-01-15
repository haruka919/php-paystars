<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' コミュニティ（一覧画面） ');
debug('********************************************************************************');
debugLogStart();

// DBから掲示板データを取得
$dbBordData = getBordListData();
debug('掲示板データを取得：'.print_r($dbBordData, true));

?>
<?php
$siteTitle = 'コミュニティ';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">コミュニティ</h2>

        <?php
        if(isLogin()){
        ?>
        <div class="community-wrapper">

          <?php
          foreach ($dbBordData as $key => $val) {
          ?>

          <div class="community-bord">
            <a href="bord.php?b_id=<?php echo $val['id']; ?>">
              <h3 class="community-bord-ttl"><?php echo $val['title']; ?></h3>
              <span class="community-bord-date"><?php echo $val['update_date']; ?></span>
            </a>
          </div>

          <?php
          }
          ?>

          <!-- <div class="community-bord">
            <a href="#">
              <h3 class="community-bord-ttl">掲示板のタイトルが入ります。</h3>
              <span class="community-bord-date">2019.07.07.23.59.20</span>
            </a>
          </div>
          <div class="community-bord">
            <a href="#">
              <h3 class="community-bord-ttl">掲示板のタイトルが入ります。</h3>
              <span class="community-bord-date">2019.07.07.23.59.20</span>
            </a>
          </div>
          <div class="community-bord">
            <a href="#">
              <h3 class="community-bord-ttl">掲示板のタイトルが入ります。</h3>
              <span class="community-bord-date">2019.07.07.23.59.20</span>
            </a>
          </div> -->

          <div class="community-btnWrapper">
            <a class="" href="communityNew.php"><i class="fas fa-plus-circle plusIcon"></i></a>
            <span>新しいコミュニティを作る</span>
          </div>
        </div>

        <?php
        }else{
        ?>

        <p class="favorite-note">※コミュニティのご利用には<a href="login.php">ログイン</a>が必要です。</p>

        <?php
        }
        ?>

      </div>

  <?php
  require('footer.php');
  ?>
