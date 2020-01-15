<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('応援歌を探すページ');
debug('***********************************************************');
debugLogStart();

// 画面表示用データ取得
// =======================
// GETパラメータを取得
// -----------------------
// ポジション
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';

// DBから全選手データを取得
$dbPlayersList = getPlayerList($c_id);
// DBからポジションデータを取得
$dbPositionData = getPosition();

debug('ポジションID：'.print_r($c_id, true));
debug('全選手リスト：'.print_r($dbPlayersList, true));
debug('ポジションデータ：'.print_r($dbPositionData, true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<?php
$siteTitle = '応援歌を探す';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="cheering main-outer">
        <h2 class="main-heading">応援歌を探す</h2>
        <div class="cheering-menuList-outer">
          <ul class="cheering-menuList">

            <li class="cheering-menuList-item <?php if(empty($c_id)){ echo 'is_selected'; } ?>"><a href="cheering.php">ALL</a></li>

            <?php
              foreach ($dbPositionData as $key => $val) {
            ?>

            <li class="cheering-menuList-item <?php if($c_id == $val['id']){ echo 'is_selected'; } ?>"><a href="cheering.php?c_id=<?php echo $val['id']; ?>"><?php echo $val['name'];?></a></li>

            <?php
              }
            ?>
            <!-- <li class="cheering-menuList-item is_selected"><a href="#">投手</a></li>
            <li class="cheering-menuList-item"><a href="#">捕手</a></li>
            <li class="cheering-menuList-item"><a href="#">内野手</a></li>
            <li class="cheering-menuList-item"><a href="#">外野手</a></li>
            <li class="cheering-menuList-item"><a href="#">チャンステーマ</a></li> -->
          </ul>
        </div>
        <div class="cheering-players-outer">
          <ul class="cheering-players">
            <?php
              foreach ($dbPlayersList as $key => $val):
            ?>
              <li class="cheering-player">
                <a href="cheeringDetail.php?p_id=<?php echo $val['id']; ?>">
                  <img class="cheering-player-pic" src="<?php echo $val['pic']; ?>"/>
                  <p class="cheering-player-name"><?php echo $val['name']; ?></p>
                </a>
              </li>
            <?php
              endforeach;
            ?>
            <!-- <li class="cheering-player"><a href="cheeringDetail.php"><img src="img/11_azuma.png" />選手名</a></li>
            <li class="cheering-player"><a href="cheeringDetail.php"><img src="img/11_azuma.png" />選手名</a></li>
            <li class="cheering-player"><a href="cheeringDetail.php"><img src="img/11_azuma.png" />選手名</a></li>
            <li class="cheering-player"><a href="cheeringDetail.php"><img src="img/11_azuma.png" />選手名</a></li> -->
          </ul>

        </div>
      </div>

  <?php
  require('footer.php');
  ?>
