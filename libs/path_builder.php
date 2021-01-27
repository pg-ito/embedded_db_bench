<?php
class path_builder{

    public static $base_path='data/';
    const PREFIX = 'postal_code_db';

    public static function build_db_fpath( string $dbtype, string $suffix='dba'){
        return static::$base_path.static::PREFIX.'.'.$dbtype.'.'.$suffix;
    }
}