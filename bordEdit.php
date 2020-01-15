<?php
// 共通関数と変数の読み込み
require('function.php');

debug('********************************************************************************');
debug(' コミュニティ（管理者編集ページ） ');
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
$dbBordData = getBord($b_id);
debug('$dbFormData掲示板データ：'.print_r($dbBordData, true));

// DBよりメッセージを取得
$bordMsgData = getMsgBord($b_id);
debug('メッセージ情報：'.print_r($bordMsgData, true));

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
  debug('POST送信されました。');
  debug('POST送信'.print_r($_POST, true));

  // 変数に代入
  $title = $_POST['title'];
  $info = $_POST['info'];
  $delete = $_POST['delete'];

  if(!empty($delete)){
      debug('削除ボタンが押されました');
      try{
        // DB接続
        $dbh = dbConnect();
          // SQL文作成
        $sql = 'DELETE FROM bord WHERE user_id = :u_id AND id = :id';
        $data = array(':u_id' => $_SESSION['user_id'], ':id' => $b_id);
        debug('SQL:'.$sql);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if($stmt){
          // 掲示板に参加している人たちも削除
          debug('掲示板に参加している人たちも削除します');
          $sql = 'DELETE FROM `join` WHERE bord_id = :b_id';
          $data = array(':b_id'=>$b_id);
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);
          if($stmt){
          debug('掲示板詳細画面に遷移します');
          header("Location:community.php");
          exit();
          }
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG06;
      }
  }else{
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
      debug('編集画面です');
        // SQL文作成
      $sql = 'UPDATE bord SET title = :title, info = :info WHERE user_id = :u_id AND id = :id';
      $data = array(':title'=>$title, ':info'=>$info, ':u_id' => $_SESSION['user_id'], ':id' => $b_id);
      debug('SQL:'.$sql);

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        debug('掲示板詳細画面に遷移します');
        header("Location:bord.php?b_id=$b_id");
        exit();
        }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG06;
    }
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
                <h3 class="bord-ttl"><?php echo sanitize($dbFormData['title']); ?></h3>
                <div class="bord-info">
                  <?php echo sanitize($dbFormData['info']); ?>
                </div>
                <ul class="bord-create">
                  <li class="bord-create-user">
                    <img src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt="<?php echo sanitize($dbFormData['name']); ?>">
                    管理人：<?php echo sanitize($dbFormData['name']); ?>
                  </li>

                  <li class="bord-create-data">作成日：<?php echo sanitize($dbFormData['created_date']); ?></li>
                </ul>
                <?php
                if($dbFormData['id'] == $_SESSION['user_id']){
                ?>
                <p class="bord-editBtn"><a href="communityEdit.php?b_id=<?php echo $b_id ?>">管理者掲示板編集画面へ</a></p>
                <?php
                }
                ?>

              </div>


              <!-- 発信 -->
              <div class="bord-chatWrapper">
                <form class="chat-form" action="" method="post">
                  <div class="chat-formInner">
                    <img class="chat-form-pic" src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt="">
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
                  <p class="bord-Date"><?php echo sanitize($val['create_date']); ?></p>
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
              </ul>


              <form class="joinBtn" action="" method="post">
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
                ?>
              </form>

            </div>
          </div>
          <a class="squareBtn" href="community.php">一覧に戻る</a>
        </div>
      </div>

<!-- 編集画面フォーム -->
      <div class="communityForm-wrapper">
        <form class="communityForm as_edit" action="" method="post">
          <div class="profileFrom-top">
            <a href="bord.php?b_id=<?php echo $b_id; ?>"><i class="fas fa-times clossIcon"></i></a>
            <h3 class="profileFrom-top-ttl">コミュニティを編集する</h3>
            <input class="as_profileSave" type="submit" name="submit" value="保存">
          </div>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="form-group <?php if(!empty($err_msg['title'])) echo 'is-err'; ?>">
            コミュニティタイトル
            <input class="fn-profileName as_profileForm" type="text" name="title" value="<?php echo sanitize($dbBordData['title']); ?>">
            <div class="profileForm-countWrapper">
              <p><span class="counter-number1"><?php echo mb_strlen($dbBordData['title']); ?></span>/50</p>
            </div>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['title'])) echo $err_msg['title'];
              ?>
            </div>
          </label>
          <label class="form-group <?php if(!empty($err_msg['info'])) echo 'is-err'; ?>">
            コミュニティの説明
            <textarea class="fn-profileProfile profileForm-selfintro" name="info" rows="8" cols="80"><?php echo $dbBordData['info']; ?></textarea>
            <div class="profileForm-countWrapper">
              <p><span class="counter-number2"><?php echo mb_strlen($dbBordData['info']); ?></span>/160</p>
            </div>
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['info'])) echo $err_msg['info'];
              ?>
            </div>
          </label>
          <div class="delete-wrapper">
            <p class="delete-ttl">コミュニティを削除する</p>
            <div class="delete-note-wrapper">
              <p class="delete-note">このコミュニティを削除します。トラブルなどを避けるために、削除される際はあらかじめ参加者への告知を行っておいてください。</p>
            </div>
            <input class="as_delete" type="submit" name="delete" value="削除する">
          </div>
        </form>
      </div>

  <?php
  require('footer.php');
  ?>
