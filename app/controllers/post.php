<?php

function create()
{
    return view('post', [ 'requestUrl' => '/post/write' ]);
}

function store()
{
    return base(function ($_, $args) {
        $args = [
            'id' => user()['id'],
            'title' => $args['title'],
            'content' => $args['content'],
            'create_at' => date('Y-m-d H:i:s', time())
        ];
        return createPost(...array_values($args)) && redirect('/');
    });
}

function edit($id)
{
    return base(function ($post) {
        return owner($post['id']) && view('post', array_merge($post, [
            'requestUrl' => '/post/update?id=' . $post['id']
        ]));
    }, $id);
}

function update($id)
{
    return base(function ($post, $args) {
        $args = array_merge($args, [ 'id' => $post['id'] ]);
        return owner($post['id']) &&
            updatePost(...array_values($args)) &&
            redirect('/post/read?id=' . $post['id']);
    }, $id);
}

function destory($id)
{
    return base(function ($post) {
        return owner($post['id']) && deletePost($post['id']) && redirect('/');
    }, $id);
}

function show($id)
{
    return base(function ($post) {
        [ 'username' => $username ] = selectOne('users', $post['user_id']);
        return view('read', array_merge($post, compact('username')));
    }, $id);  
}

function base($callback, $id = null)
{
    $args = filter_input_array(INPUT_POST,[
        'title'     => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content'   => FILTER_DEFAULT
    ]);
    if ($id) {
        $post = selectOne('posts', filter_var($id), FILTER_VALIDATE_INT);
        if (empty($post)) {
            return reject(404);
        }
    }
    return call_user_func($callback, $post ?? [], $args) ?: reject();
}