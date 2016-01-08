<?php
namespace PharIo\Phive\Cli;

use PharIo\Phive\PhiveVersion;

class Runner {

    /**
     * @var CommandLocator
     */
    private $locator;

    /**
     * @var ConsoleOutput
     */
    private $ouput;

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * Runner constructor.
     *
     * @param CommandLocator $locator
     * @param ConsoleOutput  $output
     */
    public function __construct(CommandLocator $locator, ConsoleOutput $output, PhiveVersion $version) {
        $this->locator = $locator;
        $this->ouput = $output;
        $this->version = $version;
    }

    /**
     * @param Request $request
     */
    public function run(Request $request) {
        try {
            $this->locator->getCommandForRequest($request)->execute();
        } catch (CommandLocatorException $e) {
            if ($e->getCode() == CommandLocatorException::UnknownCommand) {
                $this->ouput->writeError(
                    sprintf("Unknown command '%s'\n\n", $request->getCommand())
                );
            } else {
                $this->showError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        } catch (\Throwable $t) {
            $this->showError($t->getMessage());
        }
    }

    /**
     * @param $e
     */
    private function showError($error) {
        $this->ouput->writeError(
            sprintf("An error occured while processing your request:\n          %s\n\n", $error)
        );
    }

}
