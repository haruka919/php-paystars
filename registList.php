<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug(' 選手登録リスト ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// パスワード認証
require('pass.php');

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
$siteTitle = 'お気に入り';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper as_mgBg">
      <div class="cheering main-outer">
        <h2 class="main-heading">管理画面[登録済み選手一覧]</h2>
        <div class="cheering-menuList-outer">
          <ul class="cheering-menuList">

            <li class="cheering-menuList-item <?php if(empty($c_id)){ echo 'is_selected'; } ?>"><a href="registList.php">ALL</a></li>

            <?php
              foreach ($dbPositionData as $key => $val) {
            ?>

            <li class="cheering-menuList-item <?php if($c_id == $val['id']){ echo 'is_selected'; } ?>"><a href="registList.php?c_id=<?php echo $val['id']; ?>"><?php echo $val['name'];?></a></li>

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
                <a href="registPlayer.php?p_id=<?php echo $val['id']; ?>">
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
            <div class="community-btnWrapper as_resist">
              <p>
                <a class="" href="registPlayer.php"><i class="fas fa-plus-circle plusIcon"></i></a>
                <span>選手を登録する</span>
              </p>
            </div>
          </ul>


        </div>

    </div>


    <?php
    require('footer.php');
    ?>
