<?php
namespace PharIo\Phive;

interface ReleasesRepository {

    /**
     * @param PharAlias $alias
     *
     * @return ReleaseCollection
     */
    public function getReleasesByAlias(PharAlias $alias);
}
