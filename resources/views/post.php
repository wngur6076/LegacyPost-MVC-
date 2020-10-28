<?php $_SESSION['CSRF_TOKEN'] = bin2hex(random_bytes(32)) ?>

<div id="main__form-post">
    <form action="<?=$requestUrl?>" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['CSRF_TOKEN']?>">
        <input type="text" value="<?=$title ?? ''?>" name="title" placeholder="Type a post title"
            class="uk-input uk-article-title">
        <hr>
        <div class="editor uk-align-center">
            <textarea name="content"></textarea>
            <div id="editor"><?=$content ?? ''?></div>
        </div>
        <input type="submit" value="Submit" class="uk-button uk-button-default uk-width-1-1">
    </form>
</div>