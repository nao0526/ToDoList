<?php
//================================
// ログ
//================================
//ログを取るか
ini_set('log-errors', 'On');
//ログの出力ファイルを指定
ini_set('error_log', 'php.log');

$debug_flg = true;
//デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'すでに登録されたメールアドレスです');
define('MSG04', 'パスワード（再入力）の内容が合っていません');
define('MSG05', '半角英数字で入力してください');
define('MSG06', '文字以内で入力してください');
define('MSG07', '6文字以上で入力してください');
define('MSG08','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','文字で入力してください');
define('MSG11','認証キーが正しくありません');
define('MSG12','認証キーの有効期限が切れています');
define('MSG13','日付が正しくありません');
define('MSG14','古いパスワードが違います');
define('MSG15','古いパスワードと新しいパスワードが同じです');
// サクセスメッセージ定数
define('SUC01','ToDoListを登録しました');
define('SUC02','メールを送信しました');
define('SUC03','パスワードを再発行しました');
define('SUC04', 'ToDoListを編集しました');
define('SUC05', 'ToDoListを削除しました');
define('SUC06', 'パスワードを変更しました');

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する
session_save_path('/var/tmp/');
//ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxliftime', 60*60+24+30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogstart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['limit_date'])){
        debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['limit_date']));
    }
}

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg=array();

//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequire($key){
    global $err_msg;
    if(empty($_POST[$key])){
        $err_msg[$key] = MSG01;
    }
}
//バリデーション関数（Email形式チェック）
function validEmail($str){
    global $err_msg;
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        $err_msg['email'] = MSG02;
    }
}
//バリデーション関数（Email重複チェック）
function validEmailDup($str){
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $str);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG03;
        }
    } catch(Exception $e){
        error_log('エラー発生'.$e->getMessage());
    }
}
//バリデーション関数（同地チェック）
function validMatch($str1, $str2, $key){
    global $err_msg;
    if($str1 !== $str2){
        $err_msg[$key] = MSG04;
    }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
    global $err_msg;
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        $err_msg[$key] = MSG05;
    }
}
//バリデーション関数（最大文字数チェック）
function validMaxlen($str, $key, $max=255){
    global $err_msg;
    if(mb_strlen($str) > $max){
        $err_msg[$key] = $max.MSG06;
    }
}
//バリデーション関数（最小文字数チェック）
function validMinlen($str, $key, $min=6){
    global $err_msg;
    if(mb_strlen($str) < $min){
        $err_msg[$key] = MSG07;
    }
}
//バリデーション関数（文字数チェック）
function validLength($str, $key, $length= 8){
    global $err_msg;
    if(mb_strlen($str) !== $length){
        $err_msg[$key] = $length.MSG10;
    }
}
//バリデーション関数（日時チェック）
function validTime($str, $key){
    global $err_msg;
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $str)) {
        $err_msg[$key] = MSG13;
    }
}
function validPass($str, $key){
    validMaxlen($str, $key);
    validHalf($str, $key);
    validMinlen($str, $key);
}
//================================
// データベース
//================================
//DB接続関数
function dbConnect(){
    $dsn = 'mysql:dbname=nikeda7010_todolist;host=mysql8018.xserver.jp;charset=utf8';
    $user = 'nikeda7010_user';
    $password = 'Nikeda7010';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    
    return $dbh;
}

function queryPost($dbh, $sql, $data){
    // クエリ作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        global $err_msg;
        debug('クエリ失敗');
        debug('失敗したSQL:'.print_r($stmt, true));
        $err_msg['common'] = MSG08;
        return 0;
    }
    else{
        debug('クエリ成功');
        return $stmt;
    }
}
// ユーザー情報取得関数
function getUser($u_id){
    
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE id = :u_id';
        $data = array(':u_id' => $u_id);
        
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        global $err_msg;
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
// ToDoList情報を1つ取得
function getToDoOne($todo_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM to_do WHERE id = :todo_id AND delete_flg = 0';
        $data = array(':todo_id' => $todo_id);

        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        global $err_msg;
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
// ToDoList情報取得関数
function getToDoList($currentMinNum = 1, $span = 5){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM to_do WHERE user_id = :u_id';
        $data = array(':u_id' => $_SESSION['user_id']);
        
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            $rst['total'] = $stmt->rowCount();
            $rst['total_page'] = ceil($rst['total']/$span);
        }else{
            return false;
        }
        
        $sql = 'SELECT id, todo, limit_date, comment FROM to_do WHERE user_id = :user_id AND delete_flg = 0 ORDER BY limit_date IS NULL ASC, limit_date ASC LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array(':user_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            $rst['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rst;
        }else{
            return false;
        }
    }catch(Exception $e){
        global $err_msg;
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}


//================================
// メール送信
//================================
function sendMail($from, $to, $subject, $comment){
    
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    
    $result = mb_send_mail($to, $subject, $comment, 'From:'.$from);
    
    if($result){
        debug('メールを送信しました');
    }else{
        debug('メールの送信に失敗しました');
    }
}
//================================
// その他
//================================
//sessionを１回だけ取得できる
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}
// 認証キー生成関数
function makeRandKey($length = 8){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0;$i < $length; $i++){
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}
// フォーム入力保持
function getFormData($key){
    global $dbFormData;
    
    if(!empty($dbFormData)){
        global $err_msg;
        // ユーザーデータがある場合
        if(!empty($err_msg[$key])){
            //POSTにデータがある場合
            if(isset($_POST[$key])){
                return $_POST[$key];
            }else{
                 //ない場合（基本ありえない）はDBの情報を表示
                return $dbFormData[$key];
            }
        }else{
            //POSTにデータがあり、DBの情報と違う場合
            if(isset($_POST[$key]) && $_POST[$key] !== $dbFormData[$key]){
                return $_POST[$key];
            }else{
                return $dbFormData[$key];
            }
        }
    }else{
        if(isset($_POST[$key])){
            return $_POST[$key];
        }
    }
}
// ページネーション
function pagenation($currentPageNum, $totalPageNum){
    $pageColNum = 5;
    if($currentPageNum === $pageColNum && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
    }elseif($currentPageNum === ($pageColNum - 1) && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
    }elseif($currentPageNum === 2 && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
    }elseif($currentPageNum === 1 && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = $currentPageNum + 4;
    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }
    
    echo '<div class="pagenation">';
        echo '<ul class="pagenation-list">';
    if($currentPageNum != 1){
        echo '<li><a href="to-do-view.php?p=1" style="margin-left: 0;">&lt</a></li>';
    }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="';
        if($i == $currentPageNum) echo 'active'; 
        echo '"><a href="to-do-view.php?p='.$i.'">'.$i.'</a></li>';
    }
    if($currentPageNum != $maxPageNum){
        echo '<li><a href="to-do-view.php?p='.$maxPageNum.'">&gt</a></li>';
    }
        echo '</ul>';
    echo '</div>';
}
// GETパラメータ付与
function getPageParam($arr_delete_str){
    $str = '';
    if(!empty($_GET)){
        $str .= '?';
        foreach($_GET as $key => $val){
            if(!in_array($key, $arr_delete_str, true)){
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, 'UTF-8');
    }
    echo $str;
}
// ログイン認証
function isLogin(){
    if($_SESSION['login_date']){
        debug('ログイン済みユーザーです');
        if($_SESSION['login_date'] + $_SESSION['limit_date'] < time()){
            debug('ログイン期限オーバーです');
            session_destroy();
            return false;
        }else{
            debug('ログイン期限内です');
            return true;
        }
    }else{
        debug('未ログインユーザーです');
        return false;
    }
}
?>