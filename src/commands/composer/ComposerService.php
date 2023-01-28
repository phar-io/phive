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
use Exception;
use InvalidArgumentException;
use PharIo\FileSystem\Filename;
use PharIo\Version\VersionConstraintParser;

class ComposerService {
    /** @var SourcesList */
    private $sourcesList;

    public function __construct(SourcesList $sourcesList) {
        $this->sourcesList = $sourcesList;
    }

    /**
     * @return RequestedPhar[]
     */
    public function findCandidates(Filename $composerFilename): array {
        $list   = [];
        $parser = new VersionConstraintParser();

        foreach ($this->getRequires($composerFilename) as $required => $constraint) {
            try {
                $aliasName         = $this->sourcesList->getAliasForComposerAlias(new ComposerAlias($required));
                $versionConstraint = $parser->parse($constraint);
                $list[]            = new RequestedPhar(new PharAlias($aliasName), $versionConstraint, $versionConstraint);
            } catch (Exception $e) {
                continue;
            }
        }

        return $list;
    }

    private function getRequires(Filename $composerFilename): array {
        if (!$composerFilename->exists()) {
            throw new InvalidArgumentException(
                sprintf('Specified file %s does not exist', $composerFilename->asString())
            );
        }

        $jsonData = new JsonData($composerFilename->read()->getContent());
        $requires = [];

        if ($jsonData->tryGetFragment('require', $fragment)) {
            /** @var array<string, string> $fragment */
            foreach ($fragment as $required => $constraint) {
                $requires[$required] = $constraint;
            }
        }

        if ($jsonData->tryGetFragment('require-dev', $fragment)) {
            /** @var array<string, string> $fragment */
            foreach ($fragment as $required => $constraint) {
                $requires[$required] = $constraint;
            }
        }

        return $requires;
    }
}
