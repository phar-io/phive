<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class InstallContext extends GeneralContext {

    /**
     * @return array
     */
    protected function getKnownOptions() {
        return [
            'target'         => 't',
            'copy'           => 'c',
            'global'         => 'g',
            'temporary'      => false,
            'trust-gpg-keys' => false
        ];
    }

    /**
     * @return array
     */
    protected function getConflictingOptions() {
        return [
            ['global' => 'temporary'],
            ['global' => 'target']
        ];
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function requiresValue($option) {
        return in_array($option, ['target', 'trust-gpg-keys'], true);
    }

}
