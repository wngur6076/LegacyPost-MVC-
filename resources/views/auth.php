<?php $_SESSION['CSRF_TOKEN'] = bin2hex(random_bytes(32)) ?> 

<div id="main__form-auth" class="uk-padding uk-position-fixed uk-position-center">
    <form action="<?=$requestUrl?>" method="POST">
        <input type="hidden" name="token" value="<?=$_SESSION['CSRF_TOKEN']?>">
        <input type="text" value="<?=$email ?? ''?>" name="email" placeholder="Email" class="uk-input">
        <input type="password" name="password" placeholder="Password" class="uk-input">
        <input type="submit" value="Submit" class="uk-button uk-button-default uk-width-1-1">
    </form>
</div>