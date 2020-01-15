<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('応援歌を探す(詳細)ページ');
debug('***********************************************************');
debugLogStart();

// 画面表示用データ取得
// =======================
// GETパラメータを取得
// -----------------------

// ポジション
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// プレイヤーid
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';

debug('パラメーター：'.print_r($p_id, true));

// DBから該当選手データを取得
$dbPlayerData = getPlayerData($p_id);
if(empty($dbPlayerData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
debug('該当選手データ：'.print_r($dbPlayerData, true));

// DBからポジションデータを取得
$dbPositionData = getPosition();
// 年齢を取得する
$dbPlayerAge = getAge($p_id);


debug('ポジションID：'.print_r($c_id, true));
debug('ポジションデータ：'.print_r($dbPositionData, true));
debug('年齢取得：'.print_r($dbPlayerAge, true));


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<?php
$siteTitle = '応援歌を探す(詳細ページ)';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
      require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="cheering main-outer cheering-wrapper">



        <h2 class="main-heading">応援歌を探す</h2>
        <div class="cheering-menuList-outer">
          <ul class="cheering-menuList">

            <li class="cheering-menuList-item <?php if(empty($c_id)){ echo 'is_selected';} ?>"><a href="cheering.php">ALL</a></li>

            <?php
              foreach ($dbPositionData as $key => $val) {
            ?>

              <li class="cheering-menuList-item <?php if($c_id == $val['id']){ echo 'is_selected';} ?>"><a href="cheering.php?c_id=<?php echo $val['id']; ?>"><?php echo $val['name']; ?></a></li>
            <?php
              }
            ?>
            <!-- <li class="cheering-menuList-item"><a href="#">内野手</a></li>
            <li class="cheering-menuList-item"><a href="#">外野手</a></li>
            <li class="cheering-menuList-item"><a href="#">チャンステーマ</a></li> -->
          </ul>
        </div>
        <div class="cheering-data-outer">



          <div class="cheering-figure-wrapper">
            <div class="cheering-figure-wrapperBg"></div>

            <figure class="cheering-data-pic">
              <img src="<?php echo $dbPlayerData['pic'];?>" alt="">
            </figure>
            <div class="cheering-data-nameWrapper">
              <span class="cheering-data-playernum"><?php echo $dbPlayerData['playernum']; ?></span>
              <h3 class="cheering-data-name"><?php echo $dbPlayerData['name']; ?></h3>
              <span class="cheering-data-ename"><?php echo $dbPlayerData['ename']; ?></span>
            </div>
          </div>

          <div class="cheering-data-wrapper">
            <div class="cheering-data">
              <table class="cheering-data-detail">
                <tr>
                  <td class="as_bgGray">生年月日</td><td><?php echo date('Y年m月d日',  strtotime($dbPlayerData['birthday'])); ?>(<?php echo $dbPlayerAge['age'];?>歳)</td>
                </tr>
                <tr>
                  <td class="as_bgGray">身長/体重</td><td><?php if(!empty($dbPlayerData['height'])){ echo $dbPlayerData['height'].'cm/';}else{
                    echo '-- cm/';} ?><?php if(!empty($dbPlayerData['weight'])){ echo $dbPlayerData['weight'].'kg';}else{
                      echo '-- kg';} ?></td>
                </tr>
                <tr>
                  <td class="as_bgGray">血液型</td><td><?php echo $dbPlayerData['bloodtype']; ?></td>
                </tr>
                <tr>
                  <td class="as_bgGray">出身地</td><td><?php echo $dbPlayerData['prefecture']; ?></td>
                </tr>
              </table>
            </div>

            <div class="cheering-data-song">
              <div class="cheering-data-song-inner">
                <p><?php echo $dbPlayerData['song']; ?></p>
              </div>
              <div class="likeIcon-wrapper">

                <i class="fas fa-heart likeIcon fn-click-like <?php if(!empty($_SESSION['user_id'])){ if(isLike($_SESSION['user_id'], $dbPlayerData['id'])){ echo 'active'; }}  ?>" aria-hidden="true" data-playerid= "<?php echo sanitize($dbPlayerData['id']); ?>"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
