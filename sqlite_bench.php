<?php

$dbtype = $argv[1] ?? 'sqlite3';

$optype = $argv[2] ?? 'rw';

require_once('libs/sqlite_bench_lib.php');
// path_builder::$base_path = realpath('./data').'/';
$path = path_builder::build_db_fpath('sqlite3', 'db');
if($optype == 'w' || $optype == 'rw'){
    sqlite_benchmarker::write($path);
}
if($optype == 'w' || $optype == 'rw' || $optype == 'memory'){
    sqlite_benchmarker::write(':memory:', true);
}


if($optype == 'r' || $optype == 'rw' ){
    sqlite_benchmarker::read($path);
}