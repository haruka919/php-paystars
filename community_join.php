<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug(' 参加している掲示板一覧ページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面表示用データ取得
// =======================
// DBより参加している掲示板データを取得
$dbJoinBord = getMyJoinBord($_SESSION['user_id']);
debug('参加している掲示板リスト：'.print_r($dbJoinBord, true));

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
        <h2 class="main-heading">参加しているコミュニティ</h2>
        <div class="cheering-players-outer as_mypage">
          <div class="community-wrapper as_mypage">

            <?php
            foreach ($dbJoinBord as $key => $val) {
            ?>

            <div class="community-bord as_mypage">
              <a href="bord.php?b_id=<?php echo sanitize($val['id']); ?>">
                <h3 class="community-bord-ttl"><?php echo sanitize($val['title']); ?></h3>
                <span class="community-bord-date"><?php echo sanitize($val['created_date']); ?></span>
              </a>
            </div>

            <?php
            }
            ?>
          </div>
        </div>
      </div>

      <?php
      require('footer.php');
      ?>
