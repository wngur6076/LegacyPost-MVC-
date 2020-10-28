<?php

function user() 
{
    if (array_key_exists('user', $_SESSION)) {
        return $_SESSION['user'];
    }
    return false;
}

function view($view, $vars)
{
    foreach ($vars as $name => $value) {
        $$name = $value;
    }
    return require dirname(__DIR__, 2) . '/resources/views/layouts/app.php';
}

function redirect($url) 
{
    header("Location: {$url}");
    return http_response_code() === 302;
}

function reject($code = null)
{
    switch ($code) {
        case 400:
            return header('HTTP/1.1 400 Bad Request');
        case 404:
            return header('HTTP/1.1 404 Not Found');
        default:
            return header("Location: {$_SERVER['HTTP_REFERER']}");
    }
}

function selectOne($table, $id)
{
    return first("SELECT * FROM {$table} WHERE id = ?", $id);
}

function owner($id)
{
    [ 'user_id' => $userId ] = selectOne('posts', $id);
    if ($user = user()) {
        return $userId == $user['id'];
    }
    return false;
}

/* function hit($path, $method = null)
{
    $is = $_SERVER['PATH_INFO'] ?? '/' == $path;
    if ($method) {
        $is = $is && strtoupper($method) == $_SERVER['REQUEST_METHOD'];
    }
    return $is;
} */

function hit($path, $method = null)
{
    [ $requestPath ] = explode('?', $_SERVER['REQUEST_URI']);
    $is = $requestPath == $path;
    if ($method) {
        $is = $is && strtoupper($method) == $_SERVER['REQUEST_METHOD'];
    }
    return $is;
}

function verify($guards)
{
    foreach ($guards as [ $path, $method ]) {
        if (hit($path, $method)) {
            $token = array_key_exists('token', $_REQUEST) ? filter_var($_REQUEST['token'], FILTER_SANITIZE_STRING) : null;
            
            if (hash_equals($token, $_SESSION['CSRF_TOKEN'])) {
                return true;
            }
            return false;
        }
    }
    return true;
}

function guard($guards)
{
    foreach ($guards as $path) {
        if (hit($path)) {
            return user() ?: false;
        }
    }
    return true;
}

function requires($requires) {
    if (count($requires) == count(array_filter($requires))) {
        return true;
    }
    return false;
}

function routes($routes)
{
    foreach ($routes as [ $path, $method, $callbackString ]) {
        if (hit($path, $method)) {
            [ $file, $callback ] = explode('.', $callbackString);
            require_once dirname(__DIR__, 2) . "/app/controllers/{$file}.php";
            call_user_func($callback, ...array_values($_GET));
            return true;
        }
    }
    return false;
}

function session($path, $lifetime)
{
    ini_set('session.gc_maxlifetime', $lifetime);
    session_set_cookie_params($lifetime);
    session_save_path($path);

    return session_start();
}

function config($conf)
{
    $configParts = explode('.', $conf);

    $config = include dirname(__DIR__, 2) . '/config/' . $configParts[0] . '.php';
    return count($configParts) > 1 ? $config[next($configParts)] : $config;
}

function transform($posts)
{
    return array_map(function ($post) {
        [ 'username' => $username ] = selectOne('users', $post['user_id']);
    
        $content = filter_var(
            mb_substr(strip_tags($post['content']), 0, 200), 
            FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );
    
        $mappings = array_merge(
            compact('username', 'content'),
            [
                'created_at' => date('h:i A, M j', strtotime($post['created_at'])),
                'url'        => '/post/read?id=' . $post['id']
            ]
        );
        return array_merge($post, $mappings);
    }, $posts);
}