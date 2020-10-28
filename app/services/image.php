<?php

function uploadImage($file, $accepts, $filename)
{
    $pathParts = pathinfo($file['name']);

    if (in_array(strtolower($pathParts['extension']), $accepts) && is_uploaded_file($file['tmp_name'])){
        $path = config('image.path') . $filename;
        if (move_uploaded_file($file['tmp_name'], $path)) {
            return json_encode([
                'uploaded' => 1,
                'url'      => '/image?path=' . $filename
            ]);
        }
    }
}

function getImage($path)
{
    if (file_exists($path)) {
        header("Content-type: " . mime_content_type($path));
        return file_get_contents($path);
    }
}