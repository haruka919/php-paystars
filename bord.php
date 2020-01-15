<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' コミュニティ（詳細ページ） ');
debug('********************************************************************************');
debugLogStart();

// ログイン認証
require('auth.php');

// 画面用表示データ取得
//==============================
// GETパラメータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
debug('掲示板ID:'.print_r($b_id, true));

// DBより掲示板データを取得
$viewData = getBord($b_id);
debug('掲示板データ：'.print_r($viewData, true));

// DBより自分の写真を取得
$myPic = getMyPic($_SESSION['user_id']);
debug('自分のアイコンを取得：'.print_r($myPic, true));

// DBよりメッセージを取得
$bordMsgData = getMsgBord($b_id);
debug('メッセージ情報 $bordMsgData：'.print_r($bordMsgData, true));

// DBより掲示板の参加者情報を取得
$joinBordData = getJoinBord($b_id);
debug('掲示板の参加情報：'.print_r($joinBordData, true));
debug('掲示板の参加情報：'.print_r($joinBordData[0]['id'], true));
$joinNum = $joinBordData[0]['id'];
debug('掲示板の参加情報：'.print_r($joinNum, true));
if(!empty($joinNum)){
  // 空以外の処理
    $count = count($joinBordData);
    debug('数：'.print_r($count, true));
}else{
  // 空の場合の処理
  $count = 0;
  debug('数：'.print_r($count, true));
}

// post送信された場合
if(!empty($_POST)){
  debug('POST送信されました');


  // 変数に代入
  $chat = (!empty($_POST['chat'])) ? $_POST['chat'] : '';
  $cancel = (!empty($_POST['cancel'])) ? $_POST['cancel'] : '';
  $join = (!empty($_POST['join'])) ? $_POST['join'] : '';

  if(!empty($cancel || $join)){
    debug('キャンセルもしくは参加ボタンが押されました。');

    if($cancel){
      debug('掲示板をやめます。');
      // 例外処理
      try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'DELETE FROM `join` WHERE bord_id = :b_id AND user_id = :u_id';
        $data = array(':b_id' => $b_id, ':u_id' => $_SESSION['user_id']);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ成功の場合
        if($stmt){
          debug('今のページに遷移します。');
          header("Location:".$_SERVER['PHP_SELF'].'?b_id='.$b_id); //自分自身に遷移する
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      $_POST = array();
    }else{
      debug('掲示板に参加します。');
      // 例外処理
      try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'INSERT INTO `join` (bord_id, user_id, create_date) VALUES (:b_id, :u_id, :date)';
        $data = array(':b_id' => $b_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ成功の場合
        if($stmt){
          debug('今のページに遷移します。');
          header("Location:".$_SERVER['PHP_SELF'].'?b_id='.$b_id); //自分自身に遷移する
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      $_POST = array();
    }

  }else{
    // 未入力チェック
    validRequired($chat, 'chat');
    // 最大文字数チェック
    validMaxLen($chat, 'chat', 500);

    if(empty($err_msg)){
      debug('バリデーションOKです。');

      // 例外処理
      try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'INSERT INTO message (bord_id, user_id, msg, create_date) VALUES (:bord_id, :user_id, :msg, :date)';
        $data = array(':bord_id' => $b_id, 'user_id' => $_SESSION['user_id'], ':msg' => $chat, ':date' => date('Y-m-d H:i:s'));
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if($stmt){
          debug('今のページに遷移します。');
          header("Location:".$_SERVER['PHP_SELF'].'?b_id='.$b_id); //自分自身に遷移する
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      $_POST = array();
    }
  }
}

?>
<?php
$siteTitle = 'コミュニティ詳細';
require('head.php');
?>
<body>
  <div class="wrap">

    <?php
    require('header.php');
    ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">コミュニティ詳細</h2>
        <div class="bord-wrapper">
          <div class="bord-msgWrapper">
            <div class="bord-converseWrapper">

              <div class="bord-headingWrapper">
                <h3 class="bord-ttl"><?php echo sanitize($viewData['title']); ?></h3>
                <div class="bord-info">
                  <?php echo sanitize($viewData['info']); ?>
                </div>
                <ul class="bord-create">
                  <li class="bord-create-user">
                    <img src="<?php echo sanitize(showImg($viewData['pic'])); ?>" alt="<?php echo sanitize($viewData['name']); ?>">
                    管理人：<?php echo sanitize($viewData['name']); ?>
                  </li>

                  <li class="bord-create-data">作成日：<?php echo sanitize($viewData['created_date']); ?></li>
                </ul>
                <?php
                if($viewData['id'] == $_SESSION['user_id']){
                ?>
                <p class="bord-editBtn"><a href="bordEdit.php?b_id=<?php echo $b_id ?>">管理者掲示板編集画面へ</a></p>
                <?php
                }
                ?>

              </div>


              <!-- 発信 -->
              <div class="bord-chatWrapper">
                <form class="chat-form" action="" method="post">
                  <div class="chat-formInner">
                    <img class="chat-form-pic" src="<?php echo sanitize(showImg($myPic['pic'])); ?>" alt="">
                    <textarea class="chat-form-text" name="chat" rows="8" cols="80" placeholder="コメントをかく"></textarea>
                  </div>
                  <input class="chat-form-btn" type="submit" name="submit" value="投稿する">
                </form>
              </div>


              <?php
                  foreach ($bordMsgData as $key => $val) {
                    if(!empty($bordMsgData[0]['msg'])){
              ?>

              <div class="bord-talkWrapper">
                <figure class="bord-talkIcon">
                  <img src="<?php echo sanitize(showImg($val['pic'])); ?>" alt="">
                  <figcaption><?php echo sanitize($val['name']); ?></figcaption>
                </figure>
                <div class="bord-talkInner as_others">
                  <p class="bord-talk"><?php echo sanitize($val['msg']); ?></p>
                  <p class="bord-Date"><?php echo sanitize($val['create_date']); ?>
                    <i class="far fa-trash-alt msg-trash fn-click-trash" style="<?php if(!empty($_SESSION['user_id'])){
                      if($val['user_id'] !== $_SESSION['user_id']){ echo 'display:none;'; }} ?>" aria-hidden="true" data-msgid="<?php echo sanitize($val['id']); ?>"></i>
                  </p>
                </div>
              </div>

              <?php
                    }
                  }
              ?>

            </div>
            <div class="bord-memberWrapper">
              <div class="bord-memberCount">
                <p>このコミュニティのメンバー</p>
                <p>現在：<?php echo $count;?>人</p>
              </div>
              <ul class="bord-memberIconList">
                <?php
                    foreach ($joinBordData as $key => $val) {
                      if(!empty($count)){
                ?>

                <li class="bord-memberIcon"><a href="#"><img src="<?php echo sanitize(showImg($val['pic'])); ?>" alt="<?php echo sanitize($val['name']); ?>"/></a></li>

                <?php
                      }
                    }
                ?>
                <!-- <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li>
                <li class="bord-memberIcon"><a href="#"><img src="img/11_azuma.png" alt=""/></a></li> -->
              </ul>


              <form class="joinBtn" action="" method="post">
                <?php
                    if($viewData['user_id'] !== $_SESSION['user_id']){
                ?>

                <?php
                  if(isJoin($b_id, $_SESSION['user_id'])){
                ?>

                <input class="roundBtn as_cancel" type="submit" name="cancel" value="辞める">

                <?php
                  }else{
                ?>

                <input class="roundBtn as_join" type="submit" name="join" value="参加する">
                <?php
                    }
                  }
                ?>
              </form>

            </div>
          </div>
          <a class="squareBtn" href="community.php">一覧に戻る</a>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
