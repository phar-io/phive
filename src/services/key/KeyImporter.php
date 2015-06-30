<?php
namespace TheSeer\Phive {

    interface KeyImporter {

        /**
         * @param string $key
         *
         * @return KeyImportResult
         */
        public function importKey($key);

    }

}

