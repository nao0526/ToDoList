<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード変更ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();
// ユーザー情報取得
$userData = getUser($_SESSION['user_id']);
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報'.print_r($_POST, true));
    //未入力チェック
    validRequire('pass_old');
    validRequire('pass_new');
    validRequire('pass_new_re');
    
    if(empty($err_msg)){
        debug('未入力チェックOKです');
        $pass_old = $_POST['pass_old'];
        $pass_new = $_POST['pass_new'];
        $pass_new_re = $_POST['pass_new_re'];
        // 古いパスワードチェック
        validPass($pass_old, 'pass_old');
        // 古いパスワードチェック
        validPass($pass_new, 'pass_new');
        
        if(empty($err_msg)){
            debug('半角・最大最小文字数チェックOKです');
            // 古いパスワードがあっているかチェック
            if(!password_verify($pass_old, $userData['password'])){
                $err_msg['pass_old'] =  MSG14;
            }
            // 古いパスワードち新しいパスワードが同じでないかチェック
            if($pass_old === $pass_new){
                $err_msg['pass_new'] =  MSG15;
            }
            // 新しいパスワードと再入力がおなじかチェック
            validMatch($pass_new, $pass_new_re, 'pass_new');
            
            if(empty($err_msg)){
                debug('バリデーションチェックOKです');
                try{
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password = :pass WHERE id = :u_id AND delete_flg = 0';
                    $data = array(':u_id' => $userData['id'],':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
                    
                    $stmt = queryPost($dbh, $sql, $data);
                    if($stmt){
                        debug('新しいパスワードがDBに登録されました');
                        // メール送信
                        $from = 'naooooo@gmail.com';
                        $to = $userData['email'];
                        $subject = '【パスワード変更のご案内】';
                        $comment = <<<EOT
本メールアドレス宛にパスワードの変更を致しました。
下記のURLにて変更したパスワードをご入力いただき、ログインください。

ログインページ：http://localhost:8888/portfolio01/login.php
再発行パスワード：{$pass_new}
※ログイン後、パスワードのご変更をお願いします

////////////////////////////////////
なおちん
////////////////////////////////////
EOT;
                        sendMail($from, $to, $subject, $comment);
                        // sessionの中身をからにする
                        session_unset();
                        // サクセスメッセージをセッションに格納
                        $_SESSION['success-msg'] = SUC06;
                        debug('セッション変数の中身：'.print_r($_SESSION, true));
                        header('Location:login.php');
                        return;
                    }else{
                        $err_msg['common'] = MSG08;
                    }
                }catch(Exception $e){
                    error_log('エラー発生：'.$e-_getMessage());
                }
            }
        }
    }
}

?>


<?php
$title = "パスワード変更ページ";
require('head.php');
?>

<body>
    <?php
    require('header.php')
    ?>
    <div id="main" class="site-width">
        <section class="form-container">
            <h1 class="title">パスワード変更</h1>
            <div class="area-msg">
                <?php
        if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>
            <form class="form" action="" method="post">
                <label>
                    古いパスワード
                    <input type="password" name="pass_old" value="<?php if(!empty($_POST['pass_old'])) echo $_POST['pass_old'];?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old'];
                    ?>
                </div>
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
                    <input type="submit" name="submit" value="変更する">
                </div>
            </form>
        </section>
    </div>
   <?php
        require('footer.php');
    ?>