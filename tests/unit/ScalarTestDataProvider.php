<?php
namespace PharIo\Phive;

    trait ScalarTestDataProvider {

        /**
         * @return array
         */
        public function stringProvider() {
            return [
                ['foo'],
                ['bar'],
                ['äüß'],
                ['Ӵ', 'Ӹ', 'ה']
            ];
        }

        /**
         * @return array
         */
        public function intProvider() {
            return [
                [0],
                [1],
                [46],
                [2311341],
                [-5]
            ];
        }

        /**
         * @return array
         */
        public function boolProvider() {
            return [
                [true],
                [false]
            ];
        }

    }

