<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード再発行ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST, true));
    // 未入力チェック
    validRequire('pass_new');
    validRequire('pass_new_re');
    
    if(empty($err_msg)){
        debug('未入力チェックOK');
        
        $pass_new = $_POST['pass_new'];
        $pass_new_re = $_POST['pass_new_re'];
        
        // パスワードチェック
        validPass($pass_new, 'pass_new');
        
        if(empty($err_msg)){
            debug('半角、文字数チェックOK');
            // 新しいパスワードと再入力があっているかチェック
            validMatch($pass_new, $pass_new_re, 'pass_new');
            
            if(empty($err_msg)){
                debug('バリデーションチェックOKです');
                
                try{
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
                    
                    $stmt = queryPost($dbh, $sql, $data);
                    
                    if($stmt){
                        //メール送信
                        $from = 'naooooo@gmail.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行のご案内】';
                        $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行のパスワードをご入力いただき、ログインください。

ログインページ：http://localhost:8888/portfolio01/login.php
再発行パスワード：{$pass_new}
※ログイン後、パスワードのご変更をお願いします

////////////////////////////////////
なおちん
////////////////////////////////////
EOT;
                        sendMail($from, $to, $subject, $comment);
                        // セッションの中身をからにする
                        session_unset();
                        // サクセスメッセージをセッションに格納
                        $_SESSION['success-msg'] = SUC03;
                        debug('セッション変数の中身：'.print_r($_SESSION, true));
                        header('Location:login.php');
                        return;
                    }else{
                        $err_msg['common'] = MSG08;
                    }
                }catch(Exception $e){
                    error_log('エラー発生：'.$e->getMessage());
                    $err_msg['common'] = MSG08;
                }
            }
        }
    }
}
?>


<?php
$title = 'パスワード再発行ページ';
require('head.php');
?>

<body>
    <?php
    require('header.php');
    ?>
    <div id="main" class="site-width">
        <section class="form-container">
            <h1 class="title">パスワード再発行</h1>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>
            <form class="form" action="" method="post">
                <label>
                    新しいパスワード
                    <input type="password" name="pass_new" value="<?php if(!empty($_POST['pass_new'])) echo $_POST['pass_new'];?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new'];
                    ?>
                </div>
                <label>
                    新しいパスワード（再入力）
                    <input type="password" name="pass_new_re" value="<?php if(!empty($_POST['pass_new_re'])) echo $_POST['pass_new_re'];?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re'];
                    ?>
                </div>
                <div class="btn-container">
                    <input type="submit" name="submit" value="再発行">
                </div>
    </div>
    <?php
    require('footer.php');
    ?>