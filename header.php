
   <header>
       <h1 id="header-title"><a href="index.php">To Do List！<i class="fas fa-book-open"></i></a></h1>
    <nav id="top-nav">
      <ul>
       <?php if(empty($_SESSION['user_id'])){ ?>
            <li><a href="signup.php">会員登録</a></li>
            <li><a href="login.php">ログイン</a></li>
        </ul>
        <?php }else{ ?>
            <li><a href="to-do-view.php">My To Do List</a></li>
            <li><a href="logout.php">ログアウト</a></li>
        </ul>
        <?php } ?>
    </nav>
</header>