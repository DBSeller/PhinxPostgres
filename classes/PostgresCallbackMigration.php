<?php

namespace Classes;

interface PostgresCallbackMigration {

    public function doUp();
    public function doDown();
}
