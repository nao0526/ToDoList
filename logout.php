<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウトページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

debug('ログアウトします');
// session削除
session_destroy();

debug('ログインページへ遷移します');
header('Location:login.php');
?>