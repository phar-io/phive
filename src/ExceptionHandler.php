<?php
namespace TheSeer\Phive {

    class ExceptionHandler {

        /**
         *
         */
        public static function register() {
            set_exception_handler([self::class, 'handleException']);
        }

        /**
         * @param \Exception $e
         */
        public static function handleException(\Exception $e) {
            fwrite(STDERR, $e->getMessage() . "\n\n");
            exit(1);
        }

    }

}

