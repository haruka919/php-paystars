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
$siteTitle = 'ベイスターズ応援歌検索サイト（練習）';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <section class="ranking">

          <div class="ranking-btnWrapper">
            <h2 class="ranking-ttl">応援歌人気ランキング</h2>
            <a class="squareBtn as_ranking" href="ranking.php">ランキング一覧を見る</a>
          </div>

          <ul class="ranking-playerWrapper as_index">

            <li class="ranking-player as_second">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[1]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[1]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[1]['name']); ?></span></a>
              <img class="rank-pic" src="img/second.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[1]['ranking']); ?>票</span>
              </p>
            </li>
            <li class="ranking-player">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[0]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[0]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[0]['name']); ?></span></a>
              <img class="rank-pic as_first" src="img/first.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[0]['ranking']); ?>票</span>
              </p>
            </li>
            <li class="ranking-player as_third">
              <a href="cheeringDetail.php?p_id=<?php echo sanitize($dbRankigData[2]['id']); ?>"><img class="ranking-player-pic" src="<?php echo sanitize(showImg($dbRankigData[2]['pic'])); ?>" alt="" /><span class="ranking-player-name"><?php echo sanitize($dbRankigData[2]['name']); ?></span></a>
              <img class="rank-pic" src="img/third.png" alt="">
              <p class="ranking-like">
                <i class="fas fa-heart ranking-like-icon"></i>
                <span class="ranking-like-count"><?php echo sanitize($dbRankigData[2]['ranking']); ?>票</span>
              </p>
            </li>
          </ul>
        </section>
        <section class="content-l">
          <a href="cheering.php">
            <p>応援歌を探す<i class="fas fa-music"></i></p>
          </a>
        </section>
        <div class="wrap">
          <section class="favorite content-s">
            <a href="favorite.php">
              <p>お気に入り<i class="far fa-kiss-wink-heart"></i></p>
            </a>
          </section>
          <section class="community content-s">
            <a href="community.php">
              <p>コミュニティ<i class="far fa-comments"></i></p>
            </a>
          </section>
        </div>
      </div>

      <?php
      require('footer.php');
      ?>
