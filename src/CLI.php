<?php
namespace TheSeer\Phive {

    class CLI {

        /**
         * @var CommandLocator
         */
        private $locator;

        public function __construct(CommandLocator $locator) {
            $this->locator = $locator;
        }

        public function run(CLIRequest $request) {
            try {
                $this->locator->getCommandForRequest($request)->execute();
            } catch (CommandLocatorException $e) {
                if ($e->getCode() == CommandLocatorException::UnknownCommand) {
                    fwrite(STDERR,
                        sprintf("Unknown command '%s'\n\n", $request->getCommand())
                    );
                } else {
                    throw $e;
                }
            }
        }

    }

}
