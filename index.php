<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「');
debug('ホーム画面');
debug('「「「「「「「「「「「「「「「「「「「');
debugLogstart();

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
   

<?php
    $title = 'ToDoList!';
    require('head.php');
?>
<body>
    <?php
    require('header.php');
    ?>
    <div id="main" class="site-width">
        <div class="index">
            <h2 class="h2-title">やることが多すぎて<br>なにからやればいいかわからない...</h2>
            <p class="explain">
                仕事や学校が忙しくても何からやれば良いかもう悩みません。<br>
                ToDoList!ではタスク（やること）を期限付きで管理できるため、あなたがいま最優先にやるべきことが一目瞭然になります。<br>
                ぜひ、To Do List!を活用して、優雅な毎日をお送りください。
            </p>
            <a class="btn btn-signup" href="signup.php">今すぐ会員登録をする</a>
        </div>
        <div class="index-img">
            <img src="img/watermark.jpg" alt="">
        </div>
         
    </div>
    <?php
    require('footer.php');
    ?>