<?php

namespace Classes;

use Phinx\Migration\AbstractMigration;

class PostgresMigration extends AbstractMigration {

	/**
   * {@inheritdoc}
   */
  public function table($tableName, $options = array())
  {
    return new \Classes\Table($tableName, $options, $this->getAdapter());
  }
}
