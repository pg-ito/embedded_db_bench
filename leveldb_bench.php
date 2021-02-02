<?php

$dbtype = $argv[1] ?? 'leveldb';

$optype = $argv[2] ?? 'rw';

require_once('libs/leveldb_bench_lib.php');



// path_builder::$base_path = realpath('./data').'/';
// path_builder::$base_path = '/mnt/wsl/';// tmpfs
$path = path_builder::build_db_fpath('leveldb', 'db');
echo $path.PHP_EOL;
if($optype == 'w' || $optype == 'rw'){
    leveldb_benchmarker::write($path);
}


if($optype == 'r' || $optype == 'rw' ){
    leveldb_benchmarker::read($path);
}