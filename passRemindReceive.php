<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード再発行認証キー入力ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

if(empty($_SESSION[('auth_key')])){
    header('Location:passRemindSend');
}

if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST, true));
    
    $auth_key = $_POST['auth_key'];
    // 認証キー未入力チェック
    validRequire('auth_key');
    
    if(empty($err_msg)){
        debug('未入力チェックOK');
        // 認証キーの文字数チェック
        validLength($auth_key, 'auth_key');
        // 半角チェック
        validHalf($auth_key, 'auth_key');
        
        if(empty($err_msg)){
            // 入力された認証キーが正しいかチェック
            if($auth_key !== $_SESSION['auth_key']){
            $err_msg['auth_key'] = MSG11;
            }
            // 認証キーの有効期限をオーバーしていないかチェック
            if(time() > $_SESSION['auth_key_limit']){
            $err_msg['auth_key'] = MSG12;
            }
        
            if(empty($err_msg)){
                debug('バリデーションチェックOK');
                
                debug('パスワード再発行画面に遷移します');
                header('Location:passRemind.php');
            }
        }
    }
}
?>


<?php 
$title = "パスワード再発行ページ";
require('head.php');
?>

<body>
    <?php
    require('header.php');
    ?>
    <p id="js-show-msg" class="slide-msg" style="display: none;">
        <?php
        echo getSessionFlash('success-msg');
        ?>
    </p>
    <div id=main class="site-width" >
        <section class="form-container"  style="width: 45%;">
            <form action="" method="post" class="form">
                <p style="margin-top:20px; margin-bottom: 10px;">
                    ご指定のメールアドレスお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力ください。
                </p>
                <div class="area-msg">
                <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
                </div>
                認証キー
                <label for="">
                    <input type="text" name="auth_key" value="<?php if(!empty($_POST['auth_key'])) echo $_POST['auth_key']?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['auth_key'])) echo $err_msg['auth_key'];
                    ?>
                </div>
                <input type="submit" name="submit" value="送信">
            </form>
        </section>
    </div>
    <?php
    require('footer.php');
    ?>