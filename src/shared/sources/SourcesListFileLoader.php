<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface SourcesListFileLoader {
    public function load(): SourcesList;
}
