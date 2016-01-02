<?php
namespace PharIo\Phive;

abstract class WritableXmlRepository extends XmlRepository {

    /**
     *
     */
    protected function save() {
        $this->getDom()->save($this->getFilename());
    }

}



