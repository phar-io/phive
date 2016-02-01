<?php
namespace PharIo\Phive;

abstract class WritableXmlRepository extends XmlFileWrapper {

    /**
     *
     */
    protected function save() {
        $this->getDom()->save($this->getFilename());
    }

}
