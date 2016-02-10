<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Output\CanWriteStream;
use League\Flysystem\GridFS\GridFSAdapter;
use MongoClient;

class GridFSOutput extends FlySystemOutput implements CanWriteStream, CanReadStream
{
    /**
     * @var
     */
    protected $database;

    /**
     * Constructor.
     *
     * @param $database
     */
    public function __construct($database)
    {
        $this->database = $database;

        $mongoClient = new MongoClient();
        $gridFs = $mongoClient->selectDB($database)->getGridFS();

        $this->setFilesystem(new GridFSAdapter($gridFs));
    }
}
