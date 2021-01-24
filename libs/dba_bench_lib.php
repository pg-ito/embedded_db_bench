<?php
require_once('stopwatch.php');

class dba_benchmarker{
    const OPEN_OPTIONS = [
        'cdb' => 'r-',
        'db4' => 'r-',
        'flatfile' => 'r-',
        'inifile' => 'r-',
        'qdbm' => 'r',
        'lmdb' => 'r',
        'gdbm' => 'r'
    ];
    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function dba_bench_r(string $dbtype='cdb', string $base_path='data/postal_code_db'){
        // $handler = ($dbtype == 'cdb')? 'cdb_make': $dbtype;
        $handler = $dbtype;
        // $data = json_decode( file_get_contents('data/postal_code.json') , true);
        echo $handler.' read start ========================'.PHP_EOL;
        $fpath = static::build_db_fpath($base_path, $dbtype);// $base_path.'.'.$dbtype.'.dba';
        $fpathkeys = static::build_db_fpath($base_path, $dbtype, 'keys');//$base_path.'.'.$dbtype.'.keys';
        $open_mode = isset(static::OPEN_OPTIONS[$dbtype])? static::OPEN_OPTIONS[$dbtype]: 'r-';
        $dba = dba_open($fpath, $open_mode, $handler);
        if($dba === false){
            var_dump($dba);
            return;
        }
        $keys = file($fpathkeys);
        foreach($keys as $k=>$v){
            $keys[$k] = trim($v);// trim eol
        }
        $values = [];
        // echo 'dba_firstkey'.PHP_EOL;
        // var_dump(dba_firstkey($dba));
        // echo 'dba_exists'.PHP_EOL;
        // var_dump(dba_exists('0',$dba));
        stopwatch::start();
        foreach($keys as $key){
            // var_dump($key);
            // print($key."\t");
            $result = dba_fetch((string)$key, $dba);
            // var_dump($result);
            if($result === false){
                print("error!! key: {$key}".PHP_EOL);
                continue;
                // exit;
            }
            $values[] = $result;
        }
        $elapsed = stopwatch::stop();
        dba_close($dba);
        echo PHP_EOL;
        $result_writekeys = file_put_contents($fpathkeys, implode(PHP_EOL, $keys));
        if(!$result_writekeys){
            print("error: result_writekeys failed. {$fpathkeys}");
            exit;
        }
        $count = count($values);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
    }

    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function dba_bench_w(string $dbtype='cdb', string $base_path='data/postal_code_db'){
        $handler = ($dbtype == 'cdb')? 'cdb_make': $dbtype;
        // $handler = $dbtype;
        $data = json_decode( file_get_contents('data/postal_code.json') , true);
        echo $handler.' write start ========================'.PHP_EOL;
        $fpath = static::build_db_fpath($base_path, $dbtype);// $base_path.'.'.$dbtype.'.dba';
        $fpathkeys = static::build_db_fpath($base_path, $dbtype, 'keys');//$base_path.'.'.$dbtype.'.keys';
        if(file_exists($fpath)){
            echo 'delete '.$fpath.PHP_EOL;
            unlink($fpath);
        }
        $dba = dba_open($fpath, 'n', $handler);
        if($dba === false){
            var_dump($dba);
            return;
        }
        $keys = [];
        stopwatch::start();
        foreach($data as $k=>$val){
            $valstr = json_encode($val,JSON_FORCE_OBJECT);
            // $key =  'key_'.(string)$val['id']; // postal code is duplicated!!
            // print($key."\t");
            // $key = 'key_'.$k;
            $key = $k;
            $result = dba_insert($key, $valstr, $dba);
            if($result === false){
                print("error: {$key}, {$valstr}".PHP_EOL);
                exit;
            }
            // dba_sync($dba);
            $keys[] = $key;
        }
        $elapsed = stopwatch::stop();
        // dba_optimize($dba);
        // dba_sync($dba);
        dba_close($dba);
        echo PHP_EOL;
        $result_writekeys = file_put_contents($fpathkeys, implode(PHP_EOL, $keys));
        if(!$result_writekeys){
            print("error: result_writekeys failed. {$fpathkeys}");
            exit;
        }
        $count = count($keys);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
    }


    public static function build_db_fpath(string $base_path, string $dbtype, string $suffix='dba'){
        return $base_path.'.'.$dbtype.'.'.$suffix;
    }
}