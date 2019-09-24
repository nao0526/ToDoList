<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「');
debug('退会ページ');
debug('「「「「「「「「「「「「「「「「「「');
debugLogstart();

require('auth.php');

if(!empty($_POST)){
    debug('POST送信があります');
    
    try{
        $dbh = dbConnect();
        
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
        $sql2 = 'UPDATE to_do SET delete_flg = 1 WHERE user_id = :u_id';
        
        $data = array(':u_id' => $_SESSION['user_id']);
        
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);
        
        if($stmt1){
            debug('退会します');
            // session削除
            $_SESSION = array();
            if(isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 42000, '/');
            }
            session_destroy();
            debug('会員登録ページへ遷移します');
            header('Location:signup.php');
        }else{
            $err_msg['common'] = MSG07;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

?>


<?php
$title = '退会';
require('head.php');
?>

<body>

<?php
require('header.php');
?>   
    <div id="main" class="site-width">
       <section class="form-container">
         <div class="area-msg">
             <?php
             if(!empty($err_msg['common'])) echo $err_msg['common'];
             ?>
         </div>
          <h1 class="title">退会</h1>
           <form class="form" action="" method="post">
               <div class="btn-container" id="btn-withdraw">
                  <input type="submit" name="submit" value="退会する">
              </div>
           </form>
           <div style="font-size: 22px; margin-top: 90px;">
               <a  href="to-do-view.php">todolist <i class="fas fa-angle-double-right"></i></a>
           </div>
       </section>
        
        
    </div>

<?php
require('footer.php');
?>