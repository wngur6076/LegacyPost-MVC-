<?php

function createPost($userId, $title, $content, $createdAt)
{
    return execute('INSERT INTO posts(user_id, title, content, created_at) VALUES(?, ?, ?, ?)', $userId, $title, $content, $createdAt);
}

function updatePost($title, $content, $id)
{
    return execute('UPDATE posts SET title = ?, content = ? WHERE id = ?', $title, $content, $id);
}

function deletePost($id)
{
    return execute('DELETE FROM posts WHERE id = ?', $id);
}