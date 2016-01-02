<?php
namespace PharIo\Phive;

class TestStreamWrapper {

    static $proto = 'test';

    /**
     * @var string
     */
    static $basedir;

    static $protocolMaps = [];

    /**
     * die gespeicherten Werte
     */
    static $map = [];

    protected $_protocol;

    /**
     * Contains the data read from DataPool
     *
     * @var string
     */
    protected $_data;

    /**
     * @var int
     */
    protected $_dataSize = 0;

    /**
     * @var int
     */
    protected $_position = 0;

    /**
     * Key of Slot
     *
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $_path;

    /**
     * registriert ein neues Protokoll
     *
     * @static
     *
     * @param string $proto
     * @param string $dir
     */
    public static function register($proto, $dir) {
        $protocol = ($proto == null) ? static::$proto : $proto;
        static::$protocolMaps[$protocol] = [];

        static::$basedir = $dir;

        stream_wrapper_register($protocol, get_called_class());
    }

    /**
     * meldet alle Protokolle ab und entfernt die Einträge
     *
     * @static
     */
    public static function unregister() {
        foreach (array_keys(static::$protocolMaps) as $protocol) {
            stream_wrapper_unregister($protocol);
        }

        static::$protocolMaps = [];
        static::$map = [];
    }

    /**
     * Binary-safe file or stream read
     *
     * @param int $count
     *
     * @return string
     */
    public function stream_read($count) {
        $result = substr($this->_data, $this->_position, $count);
        $this->_position += $count;

        if (!$result) {
            return '';
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function stream_close() {
        return true;
    }

    /**
     * @return bool
     */
    public function stream_eof() {
        return $this->_position >= $this->_dataSize;
    }

    /**
     * @return array
     */
    public function stream_stat() {
        return [
            'size' => $this->_dataSize,
        ];
    }

    /**
     * Opens file or stream
     *
     * @param string $path
     * @param string $mode
     * @param string $options
     * @param string $opened_path
     *
     * @return boolean
     */
    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->_path = $this->_translate($path, static::$basedir);
        list($foo, $this->_key) = explode('://', $path);

        if ($mode == 'r' || $mode == 'rb') {
            if (!is_readable($this->_path)) {
                return false;
            }
            $fp = fopen($this->_path, $mode, $options);
            if (!$fp) {
                return false;
            }
            $this->_data = fread($fp, filesize($this->_path));
            $this->_setDataSize($this->_data);

            return true;
        }

        // readonly for now
        return false;
    }

    /**
     * Translates the uri
     *
     * @param string $uri
     * @param        $baseDir
     *
     * @return string
     */
    protected function _translate($uri, $baseDir) {
        $parts = explode('://', $uri);

        $dirs = explode('/', $parts[1]);
        $sane = [];
        foreach ($dirs as $dir) {
            if ($dir == '.') {
                continue;
            }
            if ($dir == '..') {
                array_pop($sane);
            }
            $sane[] = $dir;
        }

        return $baseDir . '/' . join('/', $sane);
    }

    /**
     * Ermittelt die Größe von $data und speichert sie in einer Property
     *
     * @param $data
     *
     * @return void
     */
    protected function _setDataSize($data) {
        $this->_dataSize = strlen($data);
    }

    /**
     * Retrieve information about a file
     *
     * @param $path
     * @param $flags
     *
     * @return array|bool
     */
    public function url_stat($path, $flags) {
        $translatedPath = $this->_translate($path, static::$basedir);
        // Suppress warnings if requested or if the file or directory does not
        // exist. This is consistent with PHP's plain filesystem stream wrapper.
        if ($flags & STREAM_URL_STAT_QUIET || !file_exists($translatedPath)) {
            return @stat($translatedPath);
        } else {
            return stat($translatedPath);
        }
    }

}
