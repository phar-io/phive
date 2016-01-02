<?php
namespace PharIo\Phive {

    class ExactVersionConstraint extends AbstractVersionConstraint
    {
        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version)
        {
            return $this->asString() == $version->getVersionString();
        }
    }

}

