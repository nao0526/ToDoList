<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ToDoList閲覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogstart();

require('auth.php');

if(!empty($_GET['p']) && !(int)$_GET['p']){
    debug('不正な値が入りました');
    header("Location:to-do-view.php");
    exit;
}
$currentPageNum = !empty($_GET['p']) ? $_GET['p'] : 1;

$listSpan = 5;

$carrentMinNum = ($currentPageNum - 1) * $listSpan;

$viewData = getToDoList($carrentMinNum);
debug('$carrentMinNum'.$carrentMinNum);
debug('取得した情報'.print_r($viewData, true));
if($currentPageNum > 1 && empty($viewData['data'][0])){
    debug('不正な値が入りまし');
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