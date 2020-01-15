<?php
// 共通関数と変数の読み込み
require('function.php');

debug('***********************************************************');
debug('選手登録ページ');
debug('***********************************************************');
debugLogStart();

// ログイン認証
require('auth.php');
// パスワード認証
require('pass.php');

// 画面表示用データ取得
// =======================
// GETデータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから選手データを取得
$dbPlayerData = (!empty($p_id)) ? getPlayerResistData($p_id) : '';
// 新規登録画面か編集画面か判別フラグ
$edit_flg = (empty($dbPlayerData)) ? false : true;
// DBからポジションデータを取得
$dbPositionData = getPosition();
// DBから血液型データを取得
$dbBloodTypeData = getBloodType();
// DBから都道府県データを取得
$dbPrefectureData = getPrefecture();
// $birth_year = (!empty($dbPlayerData['birthday'])) ? date('Y') : '';
// debug('誕生日：'.$birth_year);

//
debug('選手ID：'.$p_id);
debug('選手登録データ：'.print_r($dbPlayerData, true));
debug('ポジションデータ：'.print_r($dbPositionData, true));
debug('血液型データ：'.print_r($dbBloodTypeData, true));
debug('都道府県データ：'.print_r($dbPrefectureData, true));

// post送信時処理
// =======================
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST送信：'.print_r($_POST, true));
  debug('FILE送信：'.print_r($_FILES, true));

  // 変数にユーザー情報を代入
  //（１）選手の名前
  $name = $_POST['name'];
  //（2）ローマ字
  $ename = $_POST['ename'];
  //（3）背番号
  $playernum = $_POST['playernum'];
  //（4）ポジション
  $position = $_POST['position_id'];
  //（5）応援歌
  $song = $_POST['song'];
  //（6）生年月日（西暦）
  $birth_year = (!empty($_POST['birth_year'])) ? $_POST['birth_year'] : 0;
  //（7）生年月日（月）
  $birth_month = (!empty($_POST['birth_month'])) ? $_POST['birth_month'] : 0;
  //（8）生年月日（日）
  $birth_day = (!empty($_POST['birth_day'])) ? $_POST['birth_day'] : 0;
  $birthday = $birth_year.'-'.$birth_month.'-'.$birth_day;
  //（9）身長
  $height = (!empty($_POST['height'])) ? $_POST['height'] : 0;
  //（１0）体重
  $weight = (!empty($_POST['weight'])) ? $_POST['weight'] : 0;
  //（１1）血液型
  $bloodtype = (!empty($_POST['bloodtype_id'])) ? $_POST['bloodtype_id'] : 0;
  //（１2）出身地
  $prefecture = (!empty($_POST['prefecture_id'])) ? $_POST['prefecture_id'] : 0;
  //（１3）写真
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic'):'';
  $pic = (empty($pic) && !empty($dbPlayerData['pic'])) ? $dbPlayerData['pic'] : $pic;

  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションチェックを行う
  //DBに情報がない場合
  if(empty($dbPlayerData)){

    // 未入力チェック(名前)
    validRequired($name, 'name');
    // 最大文字数チェック（名前）
    validMaxLen($name, 'name');

    // 未入力チェック(ローマ字)
    // validRequired($ename, 'ename');
    // 最大文字数チェック（名前）
    validMaxLen($ename, 'ename');
    // 半角英語チェック（ローマ字）
    // validEnglish($ename, 'ename');

    validPlayernumDup($playernum);
    // 未入力チェック(背番号)
    validRequired($playernum, 'playernum');
    // 半角数字チェック（背番号）
    validNumber($playernum, 'playernum');

    // 未入力チェック（ポジション）
    validRequired($position, 'position_id');
    // 未入力チェック(ポジション)
    validSelect($position, 'position_id');

    // 未入力チェック(応援歌)
    validRequired($song, 'song');
    // 最大文字数チェック（応援歌）
    validMaxLen($song, 'song', 500);

    // 未入力チェック(生年月日-年)
    validRequiredBirth($birth_year, $birth_month, $birth_day, 'birth_year', 'birth_month',  'birth_day');
    validBirth($birth_year, $birth_month, $birth_day, 'birth_year', 'birth_month',  'birth_day');

    // 未入力チェック(身長)
    // validRequired($height, 'height');
    // 半角数字チェック（身長）
    // validNumber($height, 'height');

    // 未入力チェック(体重)
    // validRequired($weight, 'weight');
    // 半角数字チェック（身長）
    // validNumber($weight, 'weight');

    // 未入力チェック（血液型）
    // validRequired($bloodtype, 'bloodtype_id');
    // 未入力チェック(血液型)
    // validSelect($bloodtype, 'bloodtype_id');

    // 未入力チェック（出身地）
    // validRequired($prefecture, 'prefecture_id');
    // 未入力チェック(出身地)
    // validSelect($prefecture, 'prefecture_id');

    // 未入力チェック（写真）
    validRequiredPic($pic, 'pic');

  }else{
    if($dbPlayerData['name'] !== $name){
      // 未入力チェック（名前）
      validRequired($name, 'name');
      // 最大文字数チェック（名前）
      validMaxLen($name, 'name');
    }
    if($dbPlayerData['ename'] !== $ename){
      // 未入力チェック（ローマ字）
      validRequired($ename, 'ename');
      // 最大文字数チェック（ローマ字）
      validMaxLen($ename, 'ename');
      // 半角英語チェック（ローマ字）
      // validEnglish($ename, 'ename');
    }
    if($dbPlayerData['playernum'] !== $playernum){
      // 未入力チェック（背番号）
      validRequired($playernum, 'playernum');
      // 半角数字チェック（背番号）
      validNumber($playernum, 'playernum');
    }
    if($dbPlayerData['position_id'] !== $position){
      // 未入力チェック（ポジション）
      validRequired($position, 'position_id');
      // セレクトボックスチェック（ポジション）
      validSelect($position, 'position_id');
    }
    if($dbPlayerData['song'] !== $song){
      // 未入力チェック（応援歌）
      validRequired($song, 'song');
      // 最大文字数チェック（応援歌）
      validMaxLen($song, 'song', 500);
    }
    if($dbPlayerData['birthday'] !== date($birth_year.'-'.$birth_month.'-'.$birth_day)){
      // 未入力チェック（生年月日ー年）
      validRequiredBirth($birth_year, $birth_month, $birth_day, 'birth_year', 'birth_month',  'birth_day');
      validBirth($birth_year, $birth_month, $birth_day, 'birth_year', 'birth_month',  'birth_day');
    }
    if($dbPlayerData['height'] !== $height){
      // 未入力チェック（身長）
      // validRequired($height, 'height');
      // 半角数字チェック（身長）
      validNumber($height, 'height');
    }
    if($dbPlayerData['weight'] !== $weight){
      // 未入力チェック（体重）
      // validRequired($weight, 'weight');
      // 半角数字チェック（体重）
      validNumber($weight, 'weight');
    }
    if($dbPlayerData['bloodtype_id'] !== $bloodtype){
      // 未入力チェック（血液型）
      // validRequired($bloodtype, 'bloodtype_id');
      // セレクトボックスチェック（血液型）
      // validSelect($bloodtype, 'bloodtype_id');
    }
    if($dbPlayerData['prefecture_id'] !== $prefecture){
      // 未入力チェック（出身地）
      // validRequired($prefecture, 'prefecture_id');
      // セレクトボックスチェック（出身地）
      // validSelect($prefecture, 'prefecture_id');
    }
    if($dbPlayerData['pic'] !== $pic){
      // 未入力チェック（写真）
      validRequiredPic($pic, 'pic');
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      //SQL文作成
      // 編集画面の場合はUPDATE文、新規画面の場合はINSERT文を生成
      if($edit_flg){
        $sql = 'UPDATE players SET name = :name, ename = :ename, playernum = :playernum, position_id = :position_id, song = :song, birthday = :birthday, height = :height, weight = :weight, bloodtype_id = :bloodtype_id, prefecture_id = :prefecture_id, pic = :pic WHERE id = :p_id';
        $data = array(':name' => $name, ':ename' => $ename, ':playernum' => $playernum, ':position_id' => $position, ':song' => $song, ':birthday' => $birthday, ':height' => $height, ':weight' => $weight, ':bloodtype_id' => $bloodtype,
          ':prefecture_id' => $prefecture, ':pic' => $pic, ':p_id' => $p_id);
      }else{
        debug('DB新規登録です');
        $sql = 'INSERT INTO players (name, ename, playernum, position_id, song, birthday, height, weight, bloodtype_id, prefecture_id, pic, create_date) VALUES  (:name, :ename, :playernum, :position_id, :song, :birthday, :height, :weight, :bloodtype_id, :prefecture_id, :pic, :date)';
        $data = array(':name' => $name, ':ename' => $ename, ':playernum' => $playernum, ':position_id' => $position, ':song' => $song, ':birthday' => $birthday, ':height' => $height, ':weight' => $weight, ':bloodtype_id' => $bloodtype,
          ':prefecture_id' => $prefecture, ':pic' => $pic, ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ：'.print_r($data, true));

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        // $_SESSION['msg_success'] = SUC04;
        debug('TOPページに遷移します');
        header("Location:registList.php");
      }
    } catch (Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '応援歌登録画面';
require('head.php');
?>
<body>
  <div class="wrap">

        <?php
        require('header.php');
        ?>

    <main class="main-wrapper">
      <div class="main-outer">
        <h2 class="main-heading">
          <?php  echo (!$edit_flg) ? '応援歌登録画面' : '応援歌編集画面';?>
        </h2>
        <div class="form-wrapper as-regist">
          <form class="form registForm" action="" method="post" enctype="multipart/form-data">
            <div class="is-areaMsg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <!-- registFormWrapper始まり -->
            <div class="registFormWrapper">

              <!-- registFormInner始まり -->
              <div class="registFormInner as-left">
              　<!-- 選手名入力 -->
                <label for="name1">選手名<span class ="required"><必須><span></label>
                <label class="<?php if(!empty($err_msg['name'])) echo 'is-err'; ?>" id="name1">
                  <input type="text" name="name" value="<?php echo getPlayerFormData('name'); ?>">
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['name'])) echo $err_msg['name'];
                  ?>
                </div>

              　<!-- ローマ字入力 -->
                <label for="ename1">選手名（ローマ字）<span class="formSmallNote">※ローマ字で入力してください</span></label>
                <label class="<?php if(!empty($err_msg['ename'])) echo 'is-err'; ?>" id="ename">
                  <input type="text" name="ename" value="<?php echo getPlayerFormData('ename'); ?>">
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['ename'])) echo $err_msg['ename'];
                  ?>
                </div>

              　<!-- 背番号入力 -->
                <label for="number1">背番号<span class ="required"><必須><span></label>
                <label class="<?php if(!empty($err_msg['playernum'])) echo 'is-err'; ?>" id="number1">
                  <input type="number" name="playernum" min="0" value="<?php echo getPlayerFormData('playernum'); ?>" class="as_short">
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['playernum'])) echo $err_msg['playernum'];
                  ?>
                </div>

              　<!-- ポジション入力 -->
                <label for="position1">ポジション<span class ="required"><必須><span></label>
                <label class="<?php if(!empty($err_msg['position_id'])) echo 'is-err'; ?>" id="position1">
                  <select name="position_id" class="as_short">
                      <option value="0" <?php if(getPlayerFormData('position_id') === 0){ echo 'selected';}?>>--</option>

                    <?php
                    foreach ($dbPositionData as $key => $val) {
                    ?>

                      <option value="<?php echo $val['id']; ?>" <?php if(getPlayerFormData('position_id') == $val['id']){ echo 'selected';} ?>>
                        <?php echo $val['name']; ?>
                      </option>

                    <?php
                    }
                    ?>

                  </select>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['position_id'])) echo $err_msg['position_id'];
                  ?>
                </div>

                <!-- 応援歌入力 -->
                <label for="song1">応援歌<span class ="required"><必須></span></label>
                <label class="<?php if(!empty($err_msg['song'])) echo 'is-err'; ?>" id="song1">
                  <textarea name="song" rows="8" cols="80"><?php echo getPlayerFormData('song'); ?></textarea>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['song'])) echo $err_msg['song'];
                  ?>
                </div>

                <!-- 生年月日入力 -->
                生年月日<span class ="required"><必須><span>
                <div class="birthWrapper">
                  <div class="">
                    <label for="birth_day1"></label>
                    <label class="<?php if(!empty($err_msg['birth_year'])) echo 'is-err'; ?>" id="birth_day1">
                      <input type="number" name="birth_year" min="1900" value="<?php if(!empty($dbPlayerData['birthday'])){ echo date('Y', strtotime($dbPlayerData['birthday'])); }elseif(!empty($_POST['birth_year'])){ echo $_POST['birth_year'];} ?>" class="as_birth as_inline"><span class="formText">年</span>
                    </label>
                  </div>

                  <div class="">
                  <label for="birth_day2"></label>
                    <label class="<?php if(!empty($err_msg['birth_month'])) echo 'is-err'; ?>" id="birth_day2">
                      <input type="number" name="birth_month" value="<?php if(!empty($dbPlayerData['birthday'])){ echo date('m', strtotime($dbPlayerData['birthday'])); }elseif(!empty($_POST['birth_month'])){ echo $_POST['birth_month'];} ?>" class="as_birth as_inline"><span class="formText">月</span>
                    </label>
                  </div>

                  <div class="">
                    <label for="birth_day3"></label>
                    <label class="<?php if(!empty($err_msg['birth_day'])) echo 'is-err'; ?>" id="birth_day3">
                      <input type="number" name="birth_day" value="<?php if(!empty($dbPlayerData['birthday'])){ echo date('d', strtotime($dbPlayerData['birthday'])); }elseif(!empty($_POST['birth_day'])){ echo $_POST['birth_day'];} ?>" class="as_birth as_inline"><span class="formText">日</span>
                    </label>
                  </div>
                </div>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['birth_year'])) echo $err_msg['birth_year'];
                  ?>
                </div>
              </div>
              <!-- registFormInner終わり -->


              <!-- registFormInner始まり -->
              <div class="registFormInner">

                <!-- 身長入力 -->
                <label for="height1">身長<span class="formSmallNote">※整数で入力してください</span></label>
                <label class="<?php if(!empty($err_msg['height'])) echo 'is-err'; ?>" id="height1">
                  <input type="number" name="height" value="<?php if(!empty($dbPlayerData['height'])){ echo getPlayerFormData('height'); } ?>" class="as_short as_inline"><span class="formText">cm</span>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['height'])) echo $err_msg['height'];
                  ?>
                </div>

                <!-- 体重入力 -->
                <label for="weight1">体重<span class="formSmallNote">※小数第一位までで入力してください</span></label>
                <label class="<?php if(!empty($err_msg['email'])) echo 'is-err'; ?>" id="weight1">
                  <input type="number" name="weight" step="0.1" value="<?php if(!empty($dbPlayerData['weight'])){ echo getPlayerFormData('weight'); } ?>" class="as_short as_inline"><span class="formText">kg</span>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['weight'])) echo $err_msg['weight'];
                  ?>
                </div>

                <!-- 血液型入力 -->
                <label for="bloodtype1">血液型</label>
                <label class="<?php if(!empty($err_msg['bloodtype_id'])) echo 'is-err'; ?>" id="bloodtype1">
                  <select name="bloodtype_id" class="as_short">
                    <option value="0" <?php if(getPlayerFormData('bloodtype_id') === 0){ echo 'selected';}?>>--</option>
                    <?php
                        foreach ($dbBloodTypeData as $key => $val) {
                    ?>
                    <option value="<?php echo $val['id']; ?>" <?php if(getPlayerFormData('bloodtype_id') == $val['id']){ echo 'selected';} ?>>
                      <?php echo $val['name']; ?>
                    </option>

                    <?php
                        }
                    ?>
                  </select>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['bloodtype_id'])) echo $err_msg['bloodtype_id'];
                  ?>
                </div>

                <!-- 出身地入力 -->
                <label for="prefecture1">出身地</label>
                <label class="<?php if(!empty($err_msg['prefecture_id'])) echo 'is-err'; ?>" id="prefecture1">
                  <select name="prefecture_id" class="as_short">
                    <option value="0" <?php if(getPlayerFormData('prefecture_id') === 0){ echo 'selected';} ?>>--</option>
                    <?php
                    foreach ($dbPrefectureData as $key => $val) {
                    ?>
                    <option value="<?php echo $val['id'] ?>" <?php if(getPlayerFormData('prefecture_id') == $val['id']){ echo 'selected';} ?>><?php echo $val['name']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </label>
                <div class="is-areaMsg">
                  <?php
                  if(!empty($err_msg['prefecture_id'])) echo $err_msg['prefecture_id'];
                  ?>
                </div>

                <!-- 写真入力 -->

                <label for="pic1">写真</label>
                <div class="as_pic is-areaMsg">
                  <?php
                  if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                  ?>
                </div>
                <label class="form-pic <?php if(!empty($err_msg['pic'])) echo 'is-err'; ?>" id="pic1">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic" value="" class="form-pic-file">
                  <img src="<?php echo getPlayerFormData('pic'); ?>" alt="" class="form-previmg" style="<?php if(empty(getPlayerFormData('pic'))) echo 'display:none;' ?>">
                  ドラッグ&ドロップ
                </label>

              </div>
              <!-- registFormInner終わり -->

            </div>
            <!-- registFormWrapper終わり -->

            <input type="submit" name="" value="登録" class="as-resist">
          </form>
        </div>
      </div>

  <?php
  require('footer.php');
  ?>
