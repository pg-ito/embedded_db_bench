<?php

$optype = $argv[1] ?? 'rw';
$base_path = $argv[2] ?? 'data/';
$use_transaction = $argv[3] ?? '';


require_once('libs/sqlite_bench_lib.php');

if($use_transaction == 'transaction'){
    sqlite_benchmarker::$use_transaction = true;
}

if($optype == 'w' || $optype == 'rw' || $optype == 'memory'){
    sqlite_benchmarker::write(':memory:', true);
}



// path_builder::$base_path = realpath('./data').'/';
path_builder::$base_path = $base_path;// tmpfs
$path = path_builder::build_db_fpath('sqlite3', 'db');
if($optype == 'w' || $optype == 'rw'){
    sqlite_benchmarker::write($path);
}


if($optype == 'r' || $optype == 'rw' ){
    sqlite_benchmarker::read($path);
}