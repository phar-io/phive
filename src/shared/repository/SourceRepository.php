<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface SourceRepository {
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar): ReleaseCollection;
}
