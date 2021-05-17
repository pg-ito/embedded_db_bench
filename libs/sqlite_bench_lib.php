<?php
require_once('stopwatch.php');
require_once('libs/path_builder.php');

class sqlite_benchmarker{
    public static $use_mmap = false;
    public static $use_transaction = false;
    const PRAGMA_MMAP='PRAGMA mmap_size=1073741824;';
    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function read( string $fpath='data/postal_code_db'){
        echo 'sqlite3 read start ======================== '.$fpath.PHP_EOL;
        
        $fpathkeys = $fpath.'.keys';
        $dbtype = 'sqlite3';
        $db = new SQLite3($fpath, SQLITE3_OPEN_READONLY);
        if($db === false){
            print("db open error:".PHP_EOL);
            var_dump($db);
            return;
        }
        if(static::$use_mmap){
            print('exec. '.static::PRAGMA_MMAP.PHP_EOL);
            var_dump($db->exec(static::PRAGMA_MMAP));
        }

        $keys = file($fpathkeys);
        foreach($keys as $k=>$v){
            $keys[$k] = trim($v);// trim eol
        }
        $values = [];
        stopwatch::start();
        if(static::$use_transaction){
            $db->exec('BEGIN');
        }
        $stmt = $db->prepare("SELECT * FROM postal_code WHERE id=:id");
        foreach($keys as $key){
            $stmt->bindValue(':id', $key, SQLITE3_INTEGER);
            // var_dump($stmt->getSQL(true));
            $result = $stmt->execute();
            // $result = $db->query("SELECT * FROM postal_code WHERE id={$key}");
            if($result === false){
                print("read error: {$key}".PHP_EOL);
                if(static::$use_transaction){
                    $db->exec('ROLLBACK');
                }
                $db->close();
                exit;
            }
            $values[] = $result->fetchArray(SQLITE3_ASSOC);
        }
        if(static::$use_transaction){
            $db->exec('ROLLBACK');
        }
        $elapsed = stopwatch::stop();
        // var_dump($values);
        $count = count($keys);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
        $db->close();
    }

    /**
     * string $dbtype e.g. dbm, ndbm, gdbm, db2, db3, db4, cdb(cdb_make), flatfile, inifile, qdbm, tcadb, lmdb
     */
    public static function write( string $fpath='data/postal_code_db', bool $is_rw = false){
 

        echo 'sqlite3 write start ======================== '.$fpath.PHP_EOL;

        if(file_exists($fpath)){
            echo 'delete '.$fpath.PHP_EOL;
            unlink($fpath);
        }
        $fpathkeys = $fpath.'.keys';
        $dbtype = 'sqlite3';
        $db = new SQLite3($fpath, SQLITE3_OPEN_CREATE|SQLITE3_OPEN_READWRITE);
        if($db === false){
            var_dump($db);
            return;
        }
        if(static::$use_mmap){
            print('exec. '.static::PRAGMA_MMAP.PHP_EOL);
            var_dump($db->exec(static::PRAGMA_MMAP));
        }
        $db->exec('CREATE TABLE postal_code (id INTEGER PRIMARY KEY ,body TEXT)');

        $keys = [];
        $data = json_decode( file_get_contents('data/postal_code.json') , true);

        stopwatch::start();
        if(static::$use_transaction){
            $db->exec('BEGIN');
        }
        $stmt = $db->prepare("INSERT INTO postal_code (id , body) VALUES (:id, :body)");
        foreach($data as $k=>$val){
            $valstr = json_encode($val,JSON_FORCE_OBJECT);
            // $key =  'key_'.(string)$val['id']; // postal code is duplicated!!
            // print($key."\t");
            // $key = 'key_'.$k;
            $key = $k;
            $stmt->bindValue(':id', $key, SQLITE3_INTEGER);
            $stmt->bindValue(':body', $valstr, SQLITE3_TEXT);
            // var_dump($stmt->getSQL(true));
            $result = $stmt->execute();
            if($result === false){
                print("error: {$key}, {$valstr}".PHP_EOL);
                if(static::$use_transaction){
                    $db->exec('ROLLBACK');
                }
                $db->close();
                exit;
            }
            // dba_sync($dba);
            $keys[] = $key;
        }
        $stmt->close();
        if(static::$use_transaction){
            $db->exec('COMMIT');
        }
        $elapsed = stopwatch::stop();
        // dba_optimize($dba);
        // dba_sync($dba);
        
        echo PHP_EOL;
        $result_writekeys = file_put_contents($fpathkeys, implode(PHP_EOL, $keys));
        if(!$result_writekeys){
            print("error: result_writekeys failed. {$fpathkeys}");
            $db->close();
            exit;
        }
        $count = count($keys);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
        if(!$is_rw){
            $db->close();
            return;
        }


        echo 'sqlite3 read after write start ======================== '.$fpath.PHP_EOL;
        stopwatch::start();
        $stmt = $db->prepare("SELECT * FROM postal_code WHERE id=:id");
        foreach($keys as $key){
            $stmt->bindValue(':id', $key, SQLITE3_INTEGER);
            // var_dump($stmt->getSQL(true));
            $result = $stmt->execute();
            if($result === false){
                print("read error: {$key}".PHP_EOL);
                exit;
            }
            // var_dump($result->fetchArray(SQLITE3_ASSOC) );
        }
        $elapsed = stopwatch::stop();
        $count = count($keys);
        echo "dbtype: {$dbtype}, elapsed time: {$elapsed}, count: {$count}".PHP_EOL;
    }


    public static function build_db_fpath(string $base_path, string $dbtype, string $suffix='dba'){
        return $base_path.'.'.$dbtype.'.'.$suffix;
    }
}