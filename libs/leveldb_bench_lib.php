<?php
require_once('stopwatch.php');
require_once('libs/path_builder.php');

class leveldb_benchmarker{
    const DBTYPE='leveldb';
    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function read( string $fpath='data/postal_code_db'){

        
        $fpathkeys = $fpath.'.keys';
        $dbtype = static::DBTYPE;
        echo $dbtype.' read start ======================== '.$fpath.PHP_EOL;
        $db = new LevelDB($fpath);
        if($db === false){
            print("db open error:".PHP_EOL);
            var_dump($db);
            return;
        }
        // $db->exec('PRAGMA mmap_size=1073741824');
        $keys = file($fpathkeys);
        foreach($keys as $k=>$v){
            $keys[$k] = trim($v);// trim eol
        }
        $values = [];
        stopwatch::start();
        // $stmt = $db->prepare("SELECT * FROM postal_code WHERE id=:id");
        foreach($keys as $key){
            // $stmt->bindValue(':id', $key, SQLITE3_INTEGER);
            // var_dump($stmt->getSQL(true));
            // $result = $stmt->execute();
            $result = $db->get($key);
            if($result === false){
                print("read error: {$key}".PHP_EOL);
                $db->close();
                exit;
            }
            $values[] = $result;
            // var_dump($result);
        }
        $elapsed = stopwatch::stop();
        $count = count($keys);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
        $db->close();
    }

    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function write( string $fpath='data/postal_code_db', bool $is_rw = false){
 

        if(file_exists($fpath)){
            echo 'delete '.$fpath.PHP_EOL;
            array_map('unlink', glob($fpath.'/*'));
            rmdir($fpath);
        }
        $fpathkeys = $fpath.'.keys';
        $dbtype = static::DBTYPE;
        echo $dbtype.' write start ======================== '.$fpath.PHP_EOL;
        $db = new LevelDB($fpath);
        if($db === false){
            var_dump($db);
            return;
        }


        $keys = [];
        $data = json_decode( file_get_contents('data/postal_code.json') , true);

        stopwatch::start();


        foreach($data as $k=>$val){
            $valstr = json_encode($val,JSON_FORCE_OBJECT);
            // $key =  'key_'.(string)$val['id']; // postal code is duplicated!!
            // print($key."\t");
            // $key = 'key_'.$k;
            $key = $k;
            $result = $db->put($key, $valstr);
            if($result === false){
                print("error: {$key}, {$valstr}".PHP_EOL);
                $db->close();
                exit;
            }

            $keys[] = $key;
        }
        $elapsed = stopwatch::stop();
        $db->close();

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