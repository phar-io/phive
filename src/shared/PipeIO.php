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
     * @param $pipe
     * @param $content
     */
    public function writeToPipe($pipe, $content) {
        fwrite($this->pipeHandles[$pipe], $content);
        fflush($this->pipeHandles[$pipe]);
        fclose($this->pipeHandles[$pipe]);
        $this->pipeHandles[$pipe] = false;
    }

    /**
     * @return string
     */
    public function readFromStatus() {
        stream_set_blocking($this->pipeHandles[3], 0);
        stream_set_read_buffer($this->pipeHandles[3], 0);
        $status = '';
        while (!feof($this->pipeHandles[3])) {
            $status .= fread($this->pipeHandles[3], 1);
        }
        return $status;
    }

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
        $this->pipeDefinitions = array_merge(
            [
                self::PIPE_STDIN => ['pipe', 'r'],
                self::PIPE_STDOUT => ['pipe', 'w'],
                self::PIPE_STDERR => ['pipe', 'w'],
                self::PIPE_FD_STATUS => ['pipe', 'w'],
            ],
            $pipes
        );
        return $this->pipeDefinitions;
    }

}