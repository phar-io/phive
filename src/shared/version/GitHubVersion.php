<?php
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PharIo\Phive;

use PharIo\Version\Version;

class GitHubVersion extends Version {

    /**
     * @param string $versionString
     */
    public function __construct($versionString) {
        $versionString = preg_replace('/^v/i', '', $versionString);
        parent::__construct($versionString);
    }

}
