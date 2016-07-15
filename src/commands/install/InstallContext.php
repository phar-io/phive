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

    protected function getConflictingOptions() {
        return [
            ['global' => 'temporary'],
            ['global' => 'target']
        ];
    }

    public function requiresValue($option) {
        return $option === 'target';
    }


}
