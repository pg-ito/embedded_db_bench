<?php
class path_builder{

    const BASE_PATH='data/postal_code_db';

    public static function build_db_fpath( string $dbtype, string $suffix='dba'){
        return static::BASE_PATH.'.'.$dbtype.'.'.$suffix;
    }
}