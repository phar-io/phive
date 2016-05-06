<?php
namespace PharIo\Phive;

class PipeIO {

    const PIPE_STDIN = 0;
    const PIPE_STDOUT = 1;
    const PIPE_STDERR = 2;
    const PIPE_FD_STATUS = 3;
    
    /**
     * @var array
     */
    private $pipeDefinitions = [];

    /**
     * @var resource[]
     */
    private $pipeHandles = [];

    /**
     * @var resource
     */
    private $proc;

    /**
     * @param int $pipe
     * @param string $content
     */
    public function writeToPipe($pipe, $content) {
        fwrite($this->pipeHandles[$pipe], $content);
        fflush($this->pipeHandles[$pipe]);
        fclose($this->pipeHandles[$pipe]);
        $this->pipeHandles[$pipe] = false;
    }

    /**
     * @param int $pipe
     *
     * @return string
     */
    public function readFromPipe($pipe) {
        stream_set_blocking($this->pipeHandles[$pipe], 0);
        stream_set_read_buffer($this->pipeHandles[$pipe], 0);
        $status = '';
        while (!feof($this->pipeHandles[$pipe])) {
            $status .= fread($this->pipeHandles[$pipe], 1);
        }
        return $status;
    }

    /**
     * @param string $executable
     * @param array $params
     * @param array $pipes
     */
    public function open($executable, array $params, array $pipes = []) {
        $this->proc = proc_open(
            $this->buildCLICommand($executable, $params),
            $this->buildPipes($pipes),
            $this->pipeHandles
        );
    }

    /**
     * @return int
     */
    public function close() {
        foreach ($this->pipeHandles as $id => $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
                $this->pipeHandles[$id] = false;
            }
        }
        return proc_close($this->proc);
    }

    /**
     * @param string $executable
     * @param array $params
     *
     * @return string
     */
    private function buildCLICommand($executable, array $params) {
        return join(' ', array_merge([$executable], $params));
    }

    /**
     * @param array $pipes
     *
     * @return array
     */
    private function buildPipes(array $pipes) {
        $this->pipeDefinitions =
            [
                self::PIPE_STDIN => ['pipe', 'r'],
                self::PIPE_STDOUT => ['pipe', 'w'],
                self::PIPE_STDERR => ['pipe', 'w'],
                self::PIPE_FD_STATUS => ['pipe', 'w'],
            ]
            + $pipes;
        return $this->pipeDefinitions;
    }

}