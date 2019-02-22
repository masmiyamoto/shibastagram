<footer id="footer">
  <ul>
    <li><a href="">Shibastagramについて</a></li>
    <li>・</li>
    <li><a href="">サポート</a></li>
    <li>・</li>
    <li><a href="">利用規約</a></li>
  </ul>
  <span class="copyright">© 2019 Shibastagram</span>
</footer>

<script src="./js/vendor/jquery-2.2.2.min.js"></script>
<script>
  $(function(){

    // メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
          $jsShowMsg.slideToggle('slow');
          setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 3000);
        }

    // 画像ライブプレビュー
    $('.input-file').on('change', function(e) {
      // 1枚だけ表示する
      var file = e.target.files[0];

      // ファイルリーダー作成
      var fileReader = new FileReader();
      fileReader.onload = function() {
        // Data URLを取得
        var dataUrl = this.result;

        // img要素に表示
        $('.prev-img').attr('src', dataUrl);
      }

      // ファイルをData URLとして読み込む
      fileReader.readAsDataURL(file);

    });

    // テキストエリアカウント
    var $countUp = $('#js-count');
    var $countView = $('#js-count-view');
    $countUp.on('keyup', function(e){
      $countView.html($(this).val().length);
    });
  });
</script>