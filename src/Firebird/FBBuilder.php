<?php
/**
 * Created by PhpStorm.
 * User: y.rusanov
 * Date: 7/26/2018
 * Time: 5:12 PM
 */

namespace Firebird;


use Illuminate\Database\Schema\Builder;

class FBBuilder extends Builder
{
    public function hasTable($table)
    {
        $sql = $this->grammar->compileTableExists($table);

        $database = $this->connection->getDatabaseName();

        $table = $this->connection->getTablePrefix().$table;

        return count($this->connection->select($sql, [$table])) > 0;
    }

    /**
     * Get the column listing for a given table.
     *
     * @param  string  $table
     * @return array
     */
    public function getColumnListing($table)
    {
        $sql = $this->grammar->compileColumnExists($table);

        $database = $this->connection->getDatabaseName();

        $table = $this->connection->getTablePrefix().$table;

        $results = $this->connection->select($sql, [$table]);

        return $this->connection->getPostProcessor()->processColumnListing($results);
    }

    public function getColumnType($table, $column)
    {
        $sql = 'SELECT RDB$FIELDS.RDB$FIELD_TYPE AS "column_type" FROM RDB$RELATION_FIELDS  JOIN RDB$FIELDS ON RDB$FIELDS.RDB$FIELD_NAME = RDB$RELATION_FIELDS.RDB$FIELD_SOURCE WHERE RDB$RELATION_FIELDS.RDB$FIELD_NAME = '."'$column'".' ';

        $database = $this->connection->getDatabaseName();

        $table = $this->connection->getTablePrefix().$table;

        $results = $this->connection->select($sql, [$table]);

        $idType = $results[0]->column_type;

        
        switch ($idType){
            case 7:
                return 'smallint';
            case 8:
                return 'integer';
            case 10:
                return 'float';
            case 12:
                return 'date';
            case 13:
                return 'time';
            case 14:
                return 'string';
            case 16:
                return 'bigint';
            case 27:
                return 'float';
            case 35:
                return 'datetime';
            case 37:
                return 'text';
            case 261:
                return 'blob';
            default:
                return 'object';
                
        }        
    }
}