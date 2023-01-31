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

use function sprintf;
use DOMElement;

class SourcesList {
    /** @var XmlFile */
    private $sourcesFile;

    /**
     * SourcesList constructor.
     */
    public function __construct(XmlFile $sourcesFile) {
        $this->sourcesFile = $sourcesFile;
    }

    /**
     * @throws SourcesListException
     */
    public function getSourceForAlias(PharAlias $alias): Source {
        $query           = sprintf('//phive:phar[@alias="%s"]/phive:repository', $alias->asString());
        $repositoryNodes = $this->sourcesFile->query($query);

        if ($repositoryNodes->length === 0) {
            throw new SourcesListException(sprintf('No repository found for alias %s', $alias->asString()));
        }

        if ($repositoryNodes->length > 1) {
            throw new SourcesListException(sprintf('Multiple repositories found for alias %s', $alias->asString()));
        }

        /** @var DOMElement $repositoryNode */
        $repositoryNode = $repositoryNodes->item(0);

        return new Source(
            $repositoryNode->getAttribute('type') ?: 'phar.io',
            new Url($repositoryNode->getAttribute('url'))
        );
    }

    /**
     * @throws SourcesListException
     */
    public function getAliasForComposerAlias(ComposerAlias $alias): string {
        $query  = sprintf('//phive:phar[@composer="%s"]', $alias->asString());
        $result = $this->sourcesFile->query($query);

        if ($result->length === 0) {
            throw new SourcesListException(
                sprintf('No such composer alias "%s"', $alias->asString()),
                SourcesListException::ComposerAliasNotFound
            );
        }

        /** @var DOMElement $pharNode */
        $pharNode = $result->item(0);

        return $pharNode->getAttribute('alias');
    }

    /**
     * @return string[]
     */
    public function getAliases(): array {
        $result = [];

        foreach ($this->sourcesFile->query('//phive:phar') as $node) {
            /** @var DOMElement $node */
            $result[] = $node->getAttribute('alias');
        }

        return $result;
    }
}
