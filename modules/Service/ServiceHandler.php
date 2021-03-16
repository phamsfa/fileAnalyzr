<?php


namespace hmsf\Service;


use hmsf\fs\AttribHandler;
use vznrw\db\dbSocket;

class ServiceHandler
{
    public $dbSocket;
    public $attribHandler;
    public $counter;

    public function __construct(dbSocket $dbSocket,AttribHandler $attribHandler) {
        $this->dbSocket = $dbSocket;
        $this->attribHandler = $attribHandler;
        $this->counter = 0;
    }
}