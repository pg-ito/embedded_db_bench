<?php

$dbtype = $argv[1] ?? 'cdb';
$dbtype_w = ($dbtype == 'cdb')? 'cdb_make':$dbtype;

$optype = $argv[2] ?? 'rw';
require_once('libs/dba_bench_lib.php');
require_once('libs/path_builder.php');



