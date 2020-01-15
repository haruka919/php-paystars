<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('マイページページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面表示用データ取得
// =======================
// DBより、お気に入りにしている選手のデータを取得
$dbFavoriteData = getFavoriteData($_SESSION['user_id']);
debug('お気に入りの選手リスト：'.print_r($dbFavoriteData, true));

// DBより参加している掲示板データを取得
$dbJoinBord = getMyJoinBord($_SESSION['user_id']);
debug('参加している掲示板リスト：'.print_r($dbJoinBord, true));

// DBより管理している掲示板データを取得
$dbCreateBord = getMyCreateBord($_SESSION['user_id']);
debug('管理している掲示板リスト：'.print_r($dbCreateBord, true));

?>
<?php
$siteTitle = 'マイページ';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="mypage main-outer">
        <h2 class="main-heading">マイページ</h2>

        <!-- お気に入りの登録している選手 -->
        <div class="cheering-players-outer as_mypage">
          <h3 class="sub-heading">お気に入り</h3>
          <ul class="cheering-players">

            <?php
            foreach ($dbFavoriteData as $key => $val) {
            ?>
              <li class="cheering-player"><a href="cheeringDetail.php?p_id=<?php echo sanitize($val['id']); ?>"><img class="cheering-player-pic" src="<?php echo $val['pic'];?>" /><?php echo sanitize($val['name']); ?></a></li>
            <?php
            }
            ?>

          </ul>
          <p class="mypage-btn"><a href="favorite.php" class="squareBtn as_mypage">一覧を見る</a></p>
        </div>

        <!-- 参加しているコミュニティ -->
        <div class="cheering-players-outer as_mypage">
          <h3 class="sub-heading">参加しているコミュニティ</h3>
          <div class="community-wrapper as_mypage">

            <?php
            foreach ($dbJoinBord as $key => $val) {
            ?>

            <div class="community-bord as_mypage">
              <a href="bord.php?b_id=<?php echo $val['id'];?>">
                <h3 class="community-bord-ttl"><?php echo $val['title'];?></h3>
                <span class="community-bord-date"><?php echo $val['created_date'];?></span>
              </a>
            </div>

            <?php
            }
            ?>

          </div>
          <p class="mypage-btn"><a href="community_join.php" class="squareBtn as_mypage">一覧を見る</a></p>
        </div>

        <!-- 管理人になっているコミュニティ -->
        <div class="cheering-players-outer as_mypage">
          <h3 class="sub-heading">管理人になっているコミュニティ</h3>
          <div class="community-wrapper as_mypage">

            <?php
            foreach ($dbCreateBord as $key => $val) {
            ?>

            <div class="community-bord as_mypage">
              <a href="bord.php?b_id=<?php echo $val['id'];?>">
                <h3 class="community-bord-ttl"><?php echo $val['title'];?></h3>
                <span class="community-bord-date"><?php echo $val['created_date'];?></span>
              </a>
            </div>

            <?php
            }
            ?>

          </div>
          <p class="mypage-btn"><a href="community_admin.php" class="squareBtn as_mypage">一覧を見る</a></p>
        </div>


      </div>


  <?php
  require('footer.php');
  ?>
