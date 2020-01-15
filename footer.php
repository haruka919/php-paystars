  <footer class="footer fn-footer">
    <p class="footer-copyright">copyright　/　<a href="registList.php">応援歌登録画面</a></p>
  </footer>
</main>
</div>
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/main.js"></script>
<script>
  $(function(){

    // フッターを最下部に固定
    // var $ftr = $('.fn-footer');
    // if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
    //   $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
    // }
    // メッセージの削除
    var $trash,
        MsgId;
    $trash = $('.fn-click-trash') || null;
    MsgId = $trash.data('msgid') || null;
    // 数値の0はfalseと判断されてしまう。playernumが0の場合もあり得るので、0もtrueとする場合はunderfinedとnullを判定する
    if(MsgId !== undefined && MsgId !== null){
      $trash.on('click', function(){
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxTrash.php",
          data: { msgId : MsgId }
        }).done(function( data ){
          console.log('Ajax Success');

          // クラス属性をtoggleで付け外しする
        }).fail(function( msg ){
          console.log('Ajax Error');
        });
      });
    };

    // お気に入りの登録・削除
    var $like,
        LikePlayerId;
    $like = $('.fn-click-like') || null;
    LikePlayerId = $like.data('playerid') || null;
    // 数値の0はfalseと判断されてしまう。playernumが0の場合もあり得るので、0もtrueとする場合はunderfinedとnullを判定する
    if(LikePlayerId !== undefined && LikePlayerId !== null){
      $like.on('click', function(){
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxLike.php",
          data: { playerId : LikePlayerId }
        }).done(function( data ){
          console.log('Ajax Success');
          // クラス属性をtoggleで付け外しする
          $this.toggleClass('active');
        }).fail(function( msg ){
          console.log('Ajax Error');
        });
      });
    };



    // 画像ライブプレビュー
    var $dropArea = $('.form-pic');
    var $fileInput = $('.form-pic-file');
    $dropArea.on('dragover', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e){
      $dropArea.css('border', 'none');
      var file = this.files[0],            // 2. files配列にファイルが入っています
          $img = $(this).siblings('.form-previmg'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
          fileReader = new FileReader();   // 4. ファイルを読み込むFileReaderオブジェクト

      // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
      fileReader.onload = function(event) {
        // 読み込んだデータをimgに設定
        $img.attr('src', event.target.result).show();
      };

      // 6. 画像読み込み
      fileReader.readAsDataURL(file);

    });

    // メニューアクティブにする
    $('.header-nav-mainMenu li a').each(function(){
    var $href = $(this).attr('href');
    if(location.href.match($href)) {
      $(this).parent().addClass('is_selected');
    } else {
      $(this).parent().removeClass('is_selected');
    }
  });

  });
</script>
</body>
</html>
