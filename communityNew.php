<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' コミュニティ（新規作成ページ） ');
debug('********************************************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面用表示データ取得
//==============================
// DBから掲示板データを取得
$dbBordData = getBordListData();
debug('掲示板データを取得：'.print_r($dbBordData, true));
// GETデータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';

// DBより掲示板データを取得する
$dbFormData = (!empty($b_id)) ? getBordData($b_id,$_SESSION['user_id']) : '';
// 新規作成画面か編集画面か判別フラグ
$edit_flg = (empty($dbFormData)) ? false : true;

debug('掲示板ID:'.print_r($b_id, true));
debug('掲示板データを取得：'.print_r($dbFormData, true));

// post送信されたら
if(!empty($_POST)){
  debug('POST送信されました。');
  debug('POST送信'.print_r($_POST, true));

  // 変数に代入
  $title = $_POST['title'];
  $info = $_POST['info'];

  if(empty($dbFormData)){
    debug('DBにありません。');
    // 未入力チェック（タイトル）
    validRequired($title, 'title');
    // 最大文字数チェック（タイトル）
    validMaxLen($title, 'title', 50);
    // 未入力チェック（説明）
    validRequired($info, 'info');
    // 最大文字数チェック（説明）
    validMaxLen($info, 'info', 160);
  }else{
    debug('DBデータあり');
    if($dbFormData['title'] !== $_POST['title']){
      // 未入力チェック（タイトル）
      validRequired($title, 'title');
      // 最大文字数チェック（タイトル）
      validMaxLen($title, 'title', 50);
    }
    if($dbFormData['info'] !== $_POST['info']){
      // 未入力チェック（説明）
      validRequired($info, 'info');
      // 最大文字数チェック（説明）
      validMaxLen($info, 'info', 160);
    }
  }
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    try{
      // DB接続
      $dbh = dbConnect();
      if($edit_flg){
        debug('編集画面です');
        // SQL文作成
        $sql = 'UPDATE bord SET title = :title, info = :info WHERE user_id = :u_id';
        $data = array(':title'=>$title, ':info'=>$info, ':u_id' => $_SESSION['user_id']);
      }else{
        debug('DB新規登録です');
        // SQL文作成
        $sql = 'INSERT INTO bord (title, info, user_id, created_date) VALUES (:title, :info, :u_id, :date)';
        $data = array(':title'=>$title, ':info'=>$info, ':u_id' => $_SESSION['user_id'], ':date'=>date('Y-m-d H:i:s'));
      }
      debug('SQL:'.$sql);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        // INSERTされたデータのIDを取得
        $id = $dbh->lastInsertId('id');
        $sql = 'INSERT INTO `join` (bord_id, user_id, create_date) VALUES (:b_id, :u_id, :date)';
        $data = array(':b_id'=>$id, ':u_id'=>$_SESSION['user_id'], ':date'=>date('Y-m-d H:i:s'));
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
        debug('掲示板詳細画面に遷移します');
        header("Location:community.php");
        }
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

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

          <div class="community-btnWrapper">
            <a class="" href=""><i class="fas fa-plus-circle plusIcon"></i></a>
            <span>新しいコミュニティを作る</span>
          </div>
        </div>
      </div>
    <div class="communityForm-wrapper">
      <form class="communityForm" action="" method="post">
        <div class="profileFrom-top">
          <a href="community.php"><i class="fas fa-times clossIcon"></i></a>
          <h3 class="profileFrom-top-ttl">新しいコミュニティを作る</h3>
          <input class="as_profileSave" type="submit" name="" value="作成">
        </div>
        <div class="area-msg">
          <?php
          if(!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <label class="form-group <?php if(!empty($err_msg['title'])) echo 'is-err'; ?>">
          コミュニティタイトル
          <input class="fn-profileName as_profileForm" type="text" name="title" value="<?php echo getFormData('title'); ?>">
          <div class="profileForm-countWrapper">
            <p><span class="counter-number1"><?php echo mb_strlen(getFormData('title')); ?></span>/50</p>
          </div>
          <div class="is-areaMsg">
            <?php
            if(!empty($err_msg['title'])) echo $err_msg['title'];
            ?>
          </div>
        </label>
        <label class="form-group <?php if(!empty($err_msg['info'])) echo 'is-err'; ?>">
          コミュニティの説明
          <textarea class="fn-profileProfile profileForm-selfintro" name="info" rows="8" cols="80"><?php echo getFormData('info'); ?></textarea>
          <div class="profileForm-countWrapper">
            <p><span class="counter-number2"><?php echo mb_strlen(getFormData('info')); ?></span>/160</p>
          </div>
          <div class="is-areaMsg">
            <?php
            if(!empty($err_msg['info'])) echo $err_msg['info'];
            ?>
          </div>
        </label>
      </form>
    </div>

  <?php
  require('footer.php');
  ?>
