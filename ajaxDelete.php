<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「');
debug(' Ajax ');
debug('「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();
// POST送信されていてかつログインしている場合
if(isset($_POST) && isset($_SESSION['user_id']) && isLogin()){
    $todoId = $_POST['todoId'];
    
    try{
        $dbh = dbConnect();
        $sql = 'DELETE FROM to_do WHERE id = :id';
        $data = array(':id' => $todoId);
        
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            $_SESSION['success-msg'] = SUC05;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>