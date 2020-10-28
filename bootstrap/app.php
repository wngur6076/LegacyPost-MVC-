<?php

assert_options(ASSERT_BAIL, true);
assert_options(ASSERT_WARNING, false);

foreach ([ 'lib', 'services' ] as $dir) {
    $includePath = dirname(__DIR__) . "/app/{$dir}/";
    foreach (scandir($includePath) as $file) {
        if (fnmatch('*.php', $file)) {
            require_once $includePath . $file;
        }
    }
}

$providers = [
    'error',
    'database',
    'session',
    'middleware',
    'route'
];
foreach ($providers as $file) {
    assert(require_once dirname(__DIR__) . "/app/providers/{$file}.php");
    
}