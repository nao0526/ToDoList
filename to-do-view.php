<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ToDoList閲覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

require('auth.php');
// GET送信の値にページ番号以外が入力された場合の改ざんチェック
if(!empty($_GET['p']) && !(int)$_GET['p']){
    debug('不正な値が入りました');
    header("Location:to-do-view.php");
    exit;
}
// 現在のページ番号を変数に格納
$currentPageNum = !empty($_GET['p']) ? $_GET['p'] : 1;
// 1ページあたりに表示するToDoListの数を定義
$listSpan = 5;
// 現在の表示レコード先頭を算出
$carrentMinNum = ($currentPageNum - 1) * $listSpan;
debug('$carrentMinNum'.$carrentMinNum);
// 表示するToDoListデータをDBから取得
$viewData = getToDoList($carrentMinNum);
debug('取得した情報'.print_r($viewData, true));
// 存在しないページ番号を入力された際の改ざんチェック
if($currentPageNum > 1 && empty($viewData['data'][0])){
    debug('不正な値が入りました');
    header("Location:to-do-view.php");
    exit;
}
debug('現在のページ：'.$currentPageNum);

debug('画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
   

<?php 
    $title = 'todolist管理';
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
        <div id="view-list">
            <div class="nav-wrapper">
               <?php if(!empty($viewData['data'])):  ?>
                <h1 class="nav-left">To Do List!</h1>
                <div class="nav-right"><a href="to-do-add.php?p=<?php echo $currentPageNum; ?>">新しいTodoListを登録</a></div>
               <?php else: ?>
               <p>ToDoListがありません</p>
               <br>
                <p><a href="to-do-add.php">新しいTodoListを登録 <i class="far fa-hand-point-left"></i></a></p>
               <?php endif; ?>
            </div>
            <?php foreach($viewData['data'] as $key => $val){ ?>
            <section class="panel"> 
                <div class="panel-left">
                    <p>ToDo：<?php echo $val['todo']; ?></p>
                    <p>期限：<?php 
                            if(!empty($val['limit_date'])) echo $val['limit_date'];
                            else echo 'なし';
                            ?>
                    </p>
                    <div class="list-comment">
                        <p>備考：</p>
                        <p class="">
                           <?php 
                            echo $val['comment'];
                            ?>
                        </p>
                    </div>
                </div>
                <div class="panel-right">
                    <div class="edit-list">
                    <a href="to-do-add.php?todo_id=<?php echo $val['id'] ?>&p=<?php echo $currentPageNum; ?>"><i class="fas fa-edit"></i></a>
                        <i class="fas fa-trash-alt js-delete-todo" data-todoid=<?php echo $val['id'] ?>></i></div>
                </div>
            </section>
            <?php } ?>
                  <?php
                    if(!empty($viewData['data'])){
                        pagenation($currentPageNum, $viewData['total_page']);
                    }
                  ?>
        </div>
        <div class="sidebar">
            <ul>
                <li><a href="passEdit.php">パスワードを変更する</a></li>
                <li><a href="withdraw.php">退会する</a></li>
            </ul>
        </div>
    </div>
    <?php
        require('footer.php');
    ?>