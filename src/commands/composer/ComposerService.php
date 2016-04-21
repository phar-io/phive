<?php
namespace PharIo\Phive;

class ComposerService {

    /**
     * @var SourcesList
     */
    private $sourcesList;

    /**
     * ComposerService constructor.
     *
     * @param SourcesList $sourcesList
     */
    public function __construct(SourcesList $sourcesList) {
        $this->sourcesList = $sourcesList;
    }

    /**
     * @param Filename $composerFilename
     *
     * @return RequestedPhar[]
     */
    public function findCandidates(Filename $composerFilename) {
        $list = [];
        $parser = new VersionConstraintParser();

        foreach ($this->getRequires($composerFilename) as $required => $constraint) {
            try {
                $aliasName = $this->sourcesList->getAliasForComposerAlias(new ComposerAlias($required));
                $versionConstraint = $parser->parse($constraint);
                $list[] = RequestedPhar::fromAlias(
                    new PharAlias($aliasName, $versionConstraint)
                );
            } catch (\Exception $e) {
                continue;
            }
        }
        return $list;
    }

    private function getRequires(Filename $composerFilename) {
        if (!$composerFilename->exists()) {
            throw new \InvalidArgumentException(
                sprintf('Specified file %s does not exist', $composerFilename->asString())
            );
        }

        $jsonData = new JsonData($composerFilename->read()->getContent());
        $requires = [];

        if ($jsonData->hasFragment('require')) {
            foreach ($jsonData->getFragment('require') as $required => $constraint) {
                $requires[$required] = $constraint;
            }
        }

        if ($jsonData->hasFragment('require-dev')) {
            foreach ($jsonData->getFragment('require-dev') as $required => $constraint) {
                $requires[$required] = $constraint;
            }
        }

        return $requires;
    }

}
