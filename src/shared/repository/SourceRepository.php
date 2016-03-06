<?php
namespace PharIo\Phive;

interface SourceRepository {

    /**
     * @param PharAlias $alias
     *
     * @return ReleaseCollection
     */
    public function getReleasesByAlias(PharAlias $alias);
}
