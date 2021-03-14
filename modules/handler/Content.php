<?php
namespace hmsf\reader;

/**
 * Description of Content
 *
 * @author peter
 */
class Content {
    
    private $path;
    private $dbSocket;
    private $folder;
    private $attribHandler;
    
    public function __construct() {
        
        // DATABASE
        $conf = new \vznrw\config\Conf();
        $con = new \vznrw\db\Connection($conf);
        $this->dbSocket = new \vznrw\db\dbSocket($con,'files');

        // CONFIGURE ENGINE
        $this->attribHandler = new \hmsf\fs\AttribHandler();
        $content = $conf->get('content');
        $this->path = $content->path;
        $this->folder = $content->folder;
        
    }
    
    public function read() {
        // CONFIGURE TREEE WALKER
        $config = new \stdClass();
        $config->dbSocket = $this->dbSocket;
        $config->attribHandler = $this->attribHandler;
        
        // LETS RUN GIVEN ROOT
        $root = new \hmsf\fs\Folder($this->path, $this->folder, NULL, $config);
        
    }
    
    public function delete($name) {
        $query = "select name from file where id_parent in (select id_file from file where name = '$name' and isNull(hash))";
    
        echo $query;
    }
}
