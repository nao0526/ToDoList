<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード再発行認証キー送信ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST, true));
    
    $email = $_POST['email'];
    // email未入力チェック
    validRequire('email');
    
    if(empty($err_msg)){
        debug('未入力チェックOKです');
        // email形式チェック
        validEmail($email);
        // emailの最大文字数チェック
        validMaxlen($email, 'email');
        
        if(empty($err_msg)){
            debug('バリデーションチェックOKです');
            
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);
                
                $stmt = queryPost($dbh, $sql, $data);
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                debug('$result:'.print_r($result, true));
                if($stmt && array_shift($result)){
                    debug('DB登録されています');
                    // 認証キー発行
                    $auth_key = makeRandKey();
                    // サクセスメッセージをセッションに格納
                    $_SESSION['success-msg'] = SUC02;
                    // メール送信
                    $from = 'naooooo@gmail.com';
                    $to = $email;
                    $subject = '【パスワード再発行のご案内】';
                    $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8888/portfolio01/passRemindReceive.php
認証キー：{$auth_key}
※認証キーの有効期限は30分でとなります。

認証キーを再発行されたい場合は下記ページより再度発行をお長い致します。
http://localhost:8888/portfolio01/passRemindSend.php

////////////////////////////////////
なおちん
////////////////////////////////////
EOT;
                    
                    sendMail($from, $to, $subject, $comment);
                    
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_key_limit'] = time() + (60*30);
                    
                    debug('セッション変数の中身：'.print_r($_SESSION, true));
                    
                    debug('パスワード再発行認証キー入力ページに遷移します');
                    header('Location:passRemindReceive.php');
                }else{
                    debug('DB登録されていないかクエリ失敗がしました');
                    $err_msg['common'] = MSG08;
                }
            }catch(Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>


<?php 
$title = "パスワード再発行認証キー送信ページ";
require('head.php');
?>

<body>
    <?php
    require('header.php');
    ?>
    <div id=main class="site-width" >
        <section class="form-container">
            <form action="" method="post" class="form">
                <p>
                    ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーを送り致します。
                </p>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                Email
                <label for="">
                    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <input type="submit" name="submit" value="送信">
            </form>
            <div><a href="login.php"> &lt; ログイン画面へ戻る</a></div>
        </section>
    </div>
   <?php
    require('footer.php');
    ?>