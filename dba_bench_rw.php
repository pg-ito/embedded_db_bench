<?php

$dbtype = $argv[1] ?? 'cdb';
$dbtype_w = ($dbtype == 'cdb')? 'cdb_make':$dbtype;

$optype = $argv[2] ?? 'rw';
require_once('libs/dba_bench_lib.php');

var_dump(dba_handlers(true));
var_dump(dba_handlers(false));

if($dbtype != 'all'){
    if($optype == 'w' || $optype == 'rw'){
        dba_benchmarker::dba_bench_w($dbtype);
    }
    if($optype == 'r' || $optype == 'rw'){
        dba_benchmarker::dba_bench_r($dbtype);        
    }
    exit;
}

$dbtypes = dba_handlers();
$exclude_type = array_search('cdb_make', $dbtypes);
var_dump($exclude_type);
if($exclude_type !== false){
    unset($dbtypes[$exclude_type]);
}

$exclude_type = array_search('inifile', $dbtypes);
var_dump($exclude_type);
if($exclude_type !== false){
    unset($dbtypes[$exclude_type]);
}

$exclude_type = array_search('flatfile', $dbtypes);
var_dump($exclude_type);
if($exclude_type !== false){
    unset($dbtypes[$exclude_type]);
}


var_dump($dbtypes);
foreach($dbtypes as $type){
    if($type == 'cdb_make'){
        continue;
    }
    if($optype == 'w' || $optype == 'rw'){
        dba_benchmarker::dba_bench_w($type);
    }
    if($optype == 'r' || $optype == 'rw'){
        dba_benchmarker::dba_bench_r($type);
    }
}

