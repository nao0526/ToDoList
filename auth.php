<?php
// $_SESSION['login_date']の中身がある場合、過去にログインしているとした
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');
    if($_SESSION['login_date'] + $_SESSION['limit_date'] < time()){ //ログイン期限オーバーの場合
        debug('有効期限切れユーザーです');
        debug('セッション変数の中身:'.print_r($_SESSION,true));
        session_destroy();
        debug('セッション変数の中身:'.print_r($_SESSION,true));
        debug('ログインページへ遷移');
        header('Location:login.php');
        exit;
    } else{
        debug('有効期限内ユーザーです');
        $_SESSION['login_date'] = time();
        debug('セッション変数の中身:'.print_r($_SESSION,true));
        if(basename($_SERVER['PHP_SELF']) === 'login.php' ){
            debug('ToDoListへ遷移');
            header('Location:to-do-view.php');
            exit;
        }
    }
}else{
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        debug('ログインページに遷移します');
        header('Location:login.php');
        exit;
    }
}
?>