<?php

function store()
{
    $file = $_FILES['upload'];
    echo uploadImage($file, config('image.accepts'), hash('md5', time() . $file['name']));
}

function show($path)
{
    echo getImage(realpath(config('image.path') . basename($path)));
}