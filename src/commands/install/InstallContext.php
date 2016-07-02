<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class InstallContext extends GeneralContext {

    protected function getKnownOptions() {
        return [
            'target'    => 't',
            'copy'      => 'c',
            'global'    => 'g',
            'temporary' => false
        ];
    }

    public function requiresValue($option) {
        return $option === 'target';
    }

}
