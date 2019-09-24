<?php
 
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「');
debug('会員登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

    if(!empty($_POST)){
        // 未入力チェック
        validRequire('email');
        validRequire('pass');
        validRequire('pass_re');
        
        if(empty($err_msg)){
            debug('未入力チェックOKです');
            
            $email = $_POST['email'];
            $pass = $_POST['pass'];
            $pass_re = $_POST['pass_re'];
            // email形式チェック
            validEmail($email);
            // email重複チェック
            validEmailDup($email);
            // パスワードチェック
            validPass($pass, 'pass');            
            
            if(empty($err_msg)){
                debug('email形式と重複チェックOK');
                debug('パスワード最大最小半角チェックOK');
                // パスワードとパスワード（再入力）が同じかチェック
                validMatch($pass, $pass_re, 'pass');
                
                if(empty($err_msg)){
                    debug('バリデーションチェックOKです');
                    
                    try{
                        $dbh = dbConnect();
                        $sql = 'INSERT INTO users (email, password, login_time, create_date) VALUES (:email, :pass, :login_time, :create_date)';
                        $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                                      ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
                        $stmt = queryPost($dbh, $sql, $data);
                        
                        if($stmt){
                            debug('クエリ成功');
                            
                            $sesLimit = 60*60;
                            debug('セッションID:'.session_id());
                            // 現在日時タイムスタンプをセッションに格納
                            $_SESSION['login_date'] = time();
                            // ログイン有効時間をセッションに格納
                            $_SESSION['limit_date'] = $sesLimit;
                            // ユーザーIDをセッションに格納
                            $_SESSION['user_id'] = $dbh->lastInsertId();
                            
                            debug('セッション変数の中身'.print_r($_SESSION,true));
                            debug('todolistへ遷移');
                            header("Location:to-do-view.php");
                        }
                    } catch (Exception $e){
                        debug('クエリ失敗');
                        error_log('エラー発生：'.$e->getMessage());
                        $err_msg['common'] = MSG08;
                    }
                    
                    
                }
            }
        }
    }
?>

<?php
    $title = '会員登録';
    require('head.php');
?>
<body>
    <?php require('header.php'); ?>
    <div id="main" class="site-width">
        <section class="form-container">
            <h1 class="title">会員登録</h1>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>
            <form class="form" action="" method="POST">
              <label>
                 メールアドレス
                  <input type="text" name="email" value = "<?php if(!empty($_POST['email'])) echo $_POST['email'] ?>">
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
                  パスワード(再入力)
                  <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re'];?>">
              </label>
              <div class="area-msg">
                  <?php
                  if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
                  ?>
              </div>
              <div class="btn-container">
                  <input type="submit" name="submit" value="登録する">
              </div>
            </form>
        </section>
    </div>
    <?php require('footer.php'); ?>
</body>
</html>