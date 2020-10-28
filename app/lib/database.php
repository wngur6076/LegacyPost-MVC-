<?php

function connect($hostname, $username, $password, $database) 
{
    return $GLOBALS['DB_CONNECTION'] = mysqli_connect($hostname, $username, $password, $database);
}

function close() 
{
    if (array_key_exists('DB_CONNECTION', $GLOBALS) && $GLOBALS['DB_CONNECTION']){
        return mysqli_close($GLOBALS['DB_CONNECTION']);
    }
    return false;
}

function first($query, ...$params)
{
    return raw($query, $params, function ($result) {
        if ($row = mysqli_fetch_assoc($result)) {
            if (is_array($row) && count($row) > 0) {
                return $row;
            }
        }
        return [];
    });
}

function rows($query, ...$params) {
    return raw($query, $params, function ($result) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($rows, $row);
        }
        return $rows;
    });
}

function execute($query, ...$params)
{
    return raw($query, $params);
}

function raw($query, $params = [], $callback = null) 
{
    $stmt = mysqli_prepare($GLOBALS['DB_CONNECTION'], $query);
    if (count($params) > 0) {
        $mappings = [
            'integer'   => 'i',
            'string'    => 's',
            'double'    => 'd'
        ];
        $bs = array_reduce($params, function ($bs, $args) use ($mappings) {
            return $bs .= $mappings[gettype($args)];
        });
        mysqli_stmt_bind_param($stmt, $bs, ...array_values($params));
    }
    if (mysqli_stmt_execute($stmt)) {
        if (is_callable($callback)) {
            $res = call_user_func($callback, mysqli_stmt_get_result($stmt));
        }
        $is = $res ?? true;
    }
    mysqli_stmt_close($stmt);

    return $is ?? [];
}