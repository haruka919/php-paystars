<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('TOPページ');
debug('***********************************************************');
debugLogStart();

// 画面表示用データ取得
// =======================
// ランキングの数を取得する
$dbRankigData = getRankingTop();
debug('人気TOP3を取得します：'.print_r($dbRankigData, true));


?>
<?php
$siteTitle = 'ランキング';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="cheering main-outer">
        <h2 class="main-heading">ランキング</h2>
        <div class="cheering-players-outer">

          <!-- <ul class="ranking-playerWrapper as-index as-ranking">
            <li class="ranking-player as-ranking as-first">
              <a href="cheeringDetail.php"><img class="ranking-player-pic" src="img/11_azuma.png" alt="" /><span class="ranking-player-name">名前が入ります</span></a>
              <img class="rank-pic as_first" src="img/first.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count">100票</span>
              </p>
            </li>
            <li class="ranking-player as-ranking as_second">
              <a href="cheeringDetail.php"><img class="ranking-player-pic" src="img/11_azuma.png" alt="" /><span class="ranking-player-name">名前が入ります</span></a>
              <img class="rank-pic" src="img/second.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count">100票</span>
              </p>
            </li>
            <li class="ranking-player as-ranking as_third">
              <a href="cheeringDetail.php"><img class="ranking-player-pic" src="img/11_azuma.png" alt="" /><span class="ranking-player-name">名前が入ります</span></a>
              <img class="rank-pic" src="img/third.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count">100票</span>
              </p>
            </li>
          </ul> -->

          <ul class="ranking-playerWrapper as_index">

            <li class="ranking-player as_second">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[1]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[1]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[1]['name']); ?></span></a>
              <img class="rank-pic" src="img/second.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[1]['ranking']); ?>票</span>
              </p>
            </li>
            <li class="ranking-player rankingBg">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[0]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[0]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[0]['name']); ?></span></a>
              <img class="rank-pic as_first" src="img/first.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[0]['ranking']); ?>票</span>
              </p>
            </li>
            <li class="ranking-player as_third rankingBg">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[2]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[2]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[2]['name']); ?></span></a>
              <img class="rank-pic" src="img/third.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[2]['ranking']); ?>票</span>
              </p>
            </li>
          </ul>


          <ul class="ranking-playerWrapper as-ranking02">

            <?php
              for($i = 3; $i < 13; $i++){
            ?>

              <li class="ranking-player as-ranking02 rankingBg">
                <a class="" href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[$i]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[$i]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[$i]['name']); ?></span></a>
                <span class="rank-number"><?php echo $i+1; ?><span>位</span></span>
                <p class="ranking-like">
                  <i class="fas fa-heart ranking-like-icon"></i>
                  <span class="ranking-like-count"><?php echo sanitize($dbRankigData[$i]['ranking']); ?>票</span>
                </p>
              </li>

            <?php
              }
            ?>

          </ul>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
