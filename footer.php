<footer id="footer">
    Copyright ©️ To Do List! All Rights Reserved.
</footer>
<script src="js/jquery-3.4.1.min.js"></script>
<script>
    $(function(){
        // フッターを画面下部に固定
        var $ftr = $('#footer');
        if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
            $ftr.attr('style', 'position: fixed; top: ' + (window.innerHeight - $ftr.outerHeight()) + 'px');
        }
        // 文字数カウンター
        var $comment = $('#comment');
        $comment.on('keyup', function(){
            var $counter = $('#counter')
            var count = this.value.length;

            $counter.firstChild.textContent= count;
            if(count > 100){
                $counter.style.color = '#FF0033';
            } else{
                $counter.style.color = '#330000';
            }
        });
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
    });
</script>
</body>
</html>