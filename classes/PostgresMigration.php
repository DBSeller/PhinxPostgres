<?php

namespace Classes;

use Phinx\Migration\AbstractMigration;

abstract class PostgresMigration extends AbstractMigration {

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = array())
    {
        return new \Classes\Table($tableName, $options, $this->getAdapter());
    }

    /**
     * Import a file to database
     * @param string path/to/file
     * @return integer OID
     */
    public function importLargeObject($path)
    {
        $pdo = $this->getAdapter()->getConnection();

        $oid = $pdo->pgsqlLOBCreate();
        $stream = $pdo->pgsqlLOBOpen($oid, 'w');
        $local = fopen($path, 'rb');

        stream_copy_to_stream($local, $stream);

        $local = null;
        $stream = null;

        return $oid;
    }

    public function up()
    {
        if(preg_match('/PostgresCallbackMigration/m', implode("\n", class_implements($this)))) {
            
            $this->callbackBeforeUp();
            
            $this->doUp();
            $this->execute("
                GRANT ALL ON SCHEMA plugins TO plugin;
                SELECT fc_grant_revoke('grant', 'plugin', 'select', '%', '%');
            ");

            $this->callbackAfterUp();
        }
    }
    
    public function down()
    {
        if(preg_match('/PostgresCallbackMigration/m', implode("\n", class_implements($this)))) {
            
            $this->callbackBeforeDown();

            $this->doDown();

            $this->callbackAfterDown();
        }
    }

    protected function callbackBeforeUp()
    {
        return;
    }

    protected function callbackAfterUp()
    {
        return;
    }

    protected function callbackBeforeDown()
    {
        return;
    }

    protected function callbackAfterDown()
    {
        return;
    }
}
