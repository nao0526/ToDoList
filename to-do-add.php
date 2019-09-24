<?php
    require('function.php');
    
    require('auth.php');
$todo_id = !empty($_GET['todo_id'])? $_GET['todo_id']: '';
$dbFormData= getTodoOne($todo_id);
$edit_flg = !empty($todo_id)? true: false;

debug('$todo_id:'.print_r($todo_id, true));
debug('$dbFormData:'.print_r($dbFormData, true));
debug('$edit_flg:'.$edit_flg);

    if(!empty($_POST)){
        debug('POST情報'.print_r($_POST, true));
        validRequire('todo');
        //validRequire('limit_date');
        
        if(empty($err_msg)){
            $todo = $_POST['todo'];
            $limit_date = !empty($_POST['limit_date']) ? $_POST['limit_date'] : NULL;
            $comment = !empty($_POST['comment']) ? $_POST['comment']: 'なし';
            debug('$limit_date'.$limit_date);
            if($todo !== $dbFormData['todo']){
                validMaxlen($todo, 'todo');
            }
            
            if($limit_date !== $dbFormData['limit_date'] && $limit_date !== NULL){
                validTime($limit_date, 'limit_date');
            }
            
            if($comment !== $dbFormData['comment']){
                validMaxlen($comment, 'comment', 100);
            }
            
            if(empty($err_msg)){

                if(!$edit_flg){
                    try{
                        $dbh = dbConnect();
                        $sql = 'INSERT INTO to_do (user_id, todo, limit_date, comment, create_date) VALUES (:user_id, :todo, :limit_date, :comment, :create_date)';
                        $data = array(':user_id' => $_SESSION['user_id'], ':todo' => $todo, ':limit_date' => $limit_date, ':comment' => $comment, ':create_date' => date('Y-m-d H:i:s'));
                        $stmt = queryPost($dbh, $sql, $data);
                        if($stmt){
                            $_SESSION['success-msg'] = SUC01;
                            $userData = getUser($_SESSION['user_id']);
                            debug('取得したユーザデータ：'.print_r($userData, true));
                            $username = !empty($userData['username']) ? $userData['username']: '名無し';

                            $from = 'naooooo@gmail.com';
                            $to = $userData['email'];
                            $subject = '新しいToDoListが追加されました';
                            $comments = <<<EOT
新しいToDoListが登録されました。
////////////////////////////
ToDo:：{$todo}
期限：{$limit_date}
備考：{$comment}
////////////////////////////
EOT;

                            sendMail($from, $to, $subject, $comments);
                            debug('todolistに遷移');
                            header('Location:to-do-view.php');
                        }else{
                            $err_msg['common'] = MSG08;
                        }
                    }catch (Exception $e){
                        error_log('エラー発生:'.$e->getMessage());
                        $err_msg['common'] = MSG08;
                    }
                }else{
                    try{
                        $dbh = dbConnect();
                        $sql = 'UPDATE to_do SET todo = :todo, limit_date = :limit_date, comment = :comment WHERE id = :todo_id AND delete_flg = 0';
                        $data = array(':todo' => $todo, ':limit_date' => $limit_date, ':comment' => $comment, 'todo_id' => $todo_id);
                        
                        $stmt = queryPost($dbh, $sql, $data);
                        if($stmt){
                            $_SESSION['success-msg'] = SUC04;
                            $userData = getUser($_SESSION['user_id']);
                            debug('取得したユーザデータ：'.print_r($userData, true));
                            $username = !empty($userData['username']) ? $userData['username']: '名無し';

                            $from = 'naooooo@gmail.com';
                            $to = $userData['email'];
                            $subject = 'ToDoListを編集しました';
                            $comments = <<<EOT
ToDoListが編集されました。
////////////////////////////
ToDo:：{$todo}
期限：{$limit_date}
備考：{$comment}
////////////////////////////
EOT;

                            sendMail($from, $to, $subject, $comments);
                            debug('todolistに遷移');
                            header('Location:to-do-view.php');
                        }else{
                            $err_msg['common'] = MSG08;
                        }
                    }catch (Exception $e){
                        error_log('エラー発生:'.$e->getMessage());
                        $err_msg['common'] = MSG08;
                    }
                }
            }
        }
    }
?>
   

<?php
    $title = 'todolist登録';
    require('head.php');
?>
<body>
    <?php require('header.php'); ?>
    <div class="site-width">
        <section id="make-to-do-list">
            <div class="nav-wrapper">
                <h1 class="nav-left" style = "margin-left: 5px;"><?php echo (empty($edit_flg))? '<i class="fas fa-plus"></i>ToDoList' : '編集'; ?></h1>
                <div class="nav-right"><a href="to-do-view.php<?php getPageParam(array('todo_id')); ?>"> ToDoList<i class="fas fa-angle-double-right"></i></a></div>
            </div>
            <form class="form to-do-list-form" action="" method="POST">
              <div class="area-msg">
                  <?php 
                  if(!empty($err_msg['common'])) echo  $err_msg['common']; 
                  ?>
              </div>
               <label>
                   ToDo
                   <input type="text" name="todo" value="<?php echo getFormData('todo')?>">
               </label>
               <div class="area-msg">
                   <?php 
                   if(!empty($err_msg['todo'])) echo  $err_msg['todo']; 
                   ?>
               </div>
                <label>
                    期限
                    <input type="date" name="limit_date" value="<?php echo getFormData('limit_date')?>">
                </label>
                <div class="area-msg">
                    <?php 
                    if(!empty($err_msg['limit_date'])) echo  $err_msg['limit_date']; 
                    ?>
                </div>
                <label>
                    備考
                    <textarea id="comment" name="comment" id="" cols="30" rows="10"><?php echo getFormData('comment')?></textarea>
                </label>
                <div id="counter"><span>0</span>/100</div>
                <div class="area-msg">
                    <?php 
                    if(!empty($err_msg['comment'])) echo  $err_msg['comment']; 
                    ?>
                </div>
                
                <input type="submit" name = "submit" value = "<?php echo (empty($edit_flg))? "登録" : "編集"; ?>">
            </form>
        </section>
    </div>
    <?php require('footer.php'); ?>