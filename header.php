<header class="header">
  <div class="header-inner">
    <h1 class="sitelogo"><a href="index.php"><img src="img/logo_70th.png" alt=""></a></h1>
    <nav class="header-nav">
      <div class="header-nav-inner">
        <ul class="header-nav-mainMenu">
          <li><a href="index.php">TOP</a></li>
          <li><a href="ranking.php">ランキング</a></li>
          <li><a href="cheering.php">応援歌を探す</a></li>
          <li><a href="favorite.php">お気に入り</a></li>
          <li><a href="community.php">コミュニティ</a></li>
        </ul>
        <ul class="header-nav-subMenu">

          <?php
            if(empty($_SESSION['user_id'])){

          ?>
          <li class="btn as_login"><a href="login.php">ログイン</a></li>
          <li class="btn as_signup"><a href="signup.php">新規登録</a></li>

          <?php
            }else{
              $dbFormData = getProfileData($_SESSION['user_id']);

          ?>

          <li>
            <a href="profile.php" class="header-profile">
              <img class="header-profile-icon" src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt=""><span>プロフィール</span>
            </a>
          </li>
          <li class="btn as_login"><a href="mypage.php">マイページ</a></li>
          <li class="btn as_logout"><a href="logout.php">ログアウト</a></li>

          <?php
            }
          ?>

        </ul>
      </div>
    </nav>
  </div>
</header>
