<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class LocalSourcesListFileLoader implements SourcesListFileLoader {
    /** @var Filename */
    private $filename;

    public function __construct(Filename $filename) {
        $this->filename = $filename;
    }

    public function load(): SourcesList {
        return new SourcesList(
            new XmlFile(
                $this->filename,
                'https://phar.io/repository-list',
                'repositories'
            )
        );
    }
}
