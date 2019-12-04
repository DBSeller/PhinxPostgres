<?php

namespace Classes;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table as BaseTable;

/**
 * Class Table
 * @package Classes
 */
class Table extends BaseTable
{
    /**
     * @var string
     */
    private $tableSchema;

    /**
     * Table constructor.
     * @param $name
     * @param array $options
     * @param AdapterInterface|null $adapter
     */
    public function __construct($name, $options = [], AdapterInterface $adapter = null)
    {
        $name = trim($name);

        if (!$this->hasSchema($name)) {
            $schema = empty($options['schema']) ? $this->getSchemaName($name, $adapter) : $options['schema'];

            if (empty($schema)) {
                $schema = 'public';
            }

            $name = "{$schema}.{$name}";
        }

        parent::__construct($name, $options, $adapter);
    }

    /**
     * @param string $schema
     * @return $this
     */
    public function setTableSchema($schema = 'public')
    {
        $this->tableSchema = $schema;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableSchema()
    {
        return $this->tableSchema;
    }

    /**
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setTableSchemaAdapter(AdapterInterface $adapter)
    {
        $adapterOptions = $adapter->getOptions();
        $adapterOptions['schema'] = $this->getTableSchema();

        $adapter->setOptions($adapterOptions);

        return $this;
    }

    /**
     *
     */
    public function save()
    {
        $this->setTableSchemaAdapter($this->getAdapter());
        parent::save();
    }

    /**
     * @param array $columns
     * @param array $values
     * @return BaseTable
     */
    public function insert(array $columns, array $values)
    {
        $count = count($columns);
        $rows = [];

        foreach ($values as $value) {
            $data = [];

            for ($key = 0; $key < $count; $key++) {
                $data[$columns[$key]] = $value[$key];
            }

            $rows[] = $data;
        }

        return parent::insert($rows);
    }

    /**
     * @param array|string $columns
     * @param BaseTable|string $referencedTable
     * @param array $referencedColumns
     * @param array $options
     * @return BaseTable
     */
    public function addForeignKey($columns, $referencedTable, $referencedColumns = ['id'], $options = [])
    {
        $referencedTable = trim($referencedTable);

        if (!$this->hasSchema($referencedTable)) {
            $referencedTable = "{$this->getSchemaName($referencedTable)}.{$referencedTable}";
        }

        return parent::addForeignKey($columns, $referencedTable, $referencedColumns, $options);
    }

    /**
     * @param string $table
     * @param AdapterInterface|null $adapter
     * @return string
     */
    private function getSchemaName($table, AdapterInterface $adapter = null)
    {
        $adapter = $adapter ?: $this->getAdapter();

        $row = $adapter->fetchRow(
            "SELECT table_schema FROM information_schema.tables where table_name = '{$table}'"
        );

        return $row['table_schema'];
    }

    /**
     * @param string $table
     * @return bool
     */
    private function hasSchema($table)
    {
        return strpos($table, '.') !== false;
    }
}
