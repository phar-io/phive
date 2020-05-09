<?php declare(strict_types = 1);
namespace PharIo\Phive;

abstract class PhiveVersion {
    public function getVersionString(): string {
        return \sprintf(
            'Phive %s - Copyright (C) 2015-%d by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion(),
            \date('Y')
        );
    }

    abstract public function getVersion(): string;
}
