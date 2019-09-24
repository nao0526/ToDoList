<footer id="footer">
    Copyright ©️ <a href="https://twitter.com/naooooo7010" style="color: #F5FFFA;">@naooooo7010</a> All Rights Reserved.
</footer>
<script src="js/jquery-3.4.1.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function(){
        // フッターを画面下部に固定
        var $ftr = document.getElementById('footer');
        if(window.innerHeight > $ftr.offsetTop + $ftr.offsetHeight){
            $ftr.setAttribute('style', 'position: fixed; top: ' + (window.innerHeight - $ftr.offsetHeight) + 'px');
        }
        // 文字数カウンター
        var $comment = document.getElementById('comment');
        if($comment !== null){
            $comment.addEventListener('keyup', function(){
                var $counter = document.getElementById('counter')
                var count = this.value.length;

                $counter.firstChild.textContent= count;
                if(count > 100){
                    $counter.style.color = '#FF0033';
                } else{
                    $counter.style.color = '#330000';
                }
            }, false);
        }
        // サクセスメッセージ表示
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, '').length){
            $jsShowMsg.fadeToggle(2000);
            setTimeout(function(){$jsShowMsg.fadeToggle(2000);}, 2000);
        }
        // ToDoList削除
        var $jsDeleteToDo = $('.js-delete-todo') || null;
        if($jsDeleteToDo !== undefined && $jsDeleteToDo !== null){
            $jsDeleteToDo.on('click', function(e){
                e.stopPropagation();
                e.preventDefault();
                var $this = $(this),
                    result = confirm('本当に削除してよろしいですか');
                if(result){
                    var todoId = $this.data('todoid') || null;
                    if(todoId !== undefined && todoId !== null){
                        $.ajax({
                            type: "POST",
                            url: "ajaxDelete.php",
                            data:{todoId : todoId}
                        }).done(function ( data ){
                            console.log('success')
                                location.reload();
                        }).fail(function ( msg ){
                            console.log('error')
                        });
                    }
                }
            });
        }
    }, false);
</script>
</body>
</html>