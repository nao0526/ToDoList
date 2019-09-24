<?php
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('ログインページ');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogstart();

    require('auth.php');

    if(!empty($_POST)){
        //未入力チェック
        validRequire('email');
        validRequire('pass');
        
        if(empty($err_msg)){
            
            $email = $_POST['email'];
            $pass = $_POST['pass'];
            $pass_save = !empty($_POST['pass_save']) ? true: false;
            // email形式チェック
            validEmail($email);
            // email最大文字数チェック
            validMaxlen($email, 'email');

            // パスワードチェック
            validPass($pass, 'pass');
           
            
            if(empty($err_msg)){
                debug('バリデーションチェックOKです');
                
                try{
                    $dbh = dbConnect();
                    $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $email);
                    
                    $stmt = queryPost($dbh, $sql, $data);
                    
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    debug('$result'.print_r($result, true));
                    if(!empty($result) && password_verify($pass, array_shift($result))){
                        
                        debug('パスワードがマッチしました');
                        
                        $sesLimit = 60*60;
                        $_SESSION['login_date'] = time();
                        if($pass_save){
                            debug('チェックボックスがチェックされていました');
                            $_SESSION['limit_date'] = $sesLimit * 24 * 30;
                        }else{
                            debug('チェックボックスがチェックされていません');
                            $_SESSION['limit_date'] = $sesLimit;
                        }
                        $_SESSION['user_id'] = $result['id'];
                        debug('セッション変数の中身:'.print_r($_SESSION,true));
                        debug('todolistへ遷移します');
                        header('Location:to-do-view.php');
                    }else{
                        debug('パスワードがマッチしません');
                        $err_msg['common'] = MSG09;
                    }
                }catch(Exception $e){
                    error_log('エラー発生:'.$e->getMessage());
                    $err_msg['common'] = MSG08;
                }
            }
        }
    }
debug('画面処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$title = 'ログイン';
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
<div id="main" class="site-width">
    <section class="form-container">
       <h1 class="title">ログイン</h1>
        <div class="area-msg">
            <?php
                if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
        </div>
        <form class="form" action="" method="post">
            <label>
               メールアドレス
                <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['email'])) echo $err_msg['email'];
                ?>
            </div>
            <label>
               パスワード
                <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                ?>
            </div>
            <label>
                <input type="checkbox" name="pass_save">次回ログインを省略する
            </label>
            <div class="btn-container">
                <input type="submit" name="submit" value="ログイン">
            </div>
            パスワードを忘れた方は<a id="pass-reminder" href="passRemindSend.php">こちら</a>
        </form>
    </section>
</div>
<?php
    require('footer.php');
?>