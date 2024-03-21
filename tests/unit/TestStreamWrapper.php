<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use const STREAM_URL_STAT_QUIET;
use function array_keys;
use function array_pop;
use function explode;
use function file_exists;
use function filesize;
use function fopen;
use function fread;
use function implode;
use function is_readable;
use function stat;
use function stream_wrapper_register;
use function stream_wrapper_unregister;
use function strlen;
use function substr;

class TestStreamWrapper {
    public static $proto = 'test';

    /** @var string */
    public static $basedir;

    public static $protocolMaps = [];

    public static $map = [];

    public $context;

    protected $_protocol;

    /** @var string */
    protected $_data;

    /** @var int */
    protected $_dataSize = 0;

    /** @var int */
    protected $_position = 0;

    /** @var string */
    protected $_key;

    /** @var string */
    protected $_path;

    /**
     * @param string $proto
     * @param string $dir
     */
    public static function register($proto, $dir): void {
        $protocol                        = ($proto == null) ? static::$proto : $proto;
        static::$protocolMaps[$protocol] = [];

        static::$basedir = $dir;

        stream_wrapper_register($protocol, static::class);
    }

    public static function unregister(): void {
        foreach (array_keys(static::$protocolMaps) as $protocol) {
            stream_wrapper_unregister($protocol);
        }

        static::$protocolMaps = [];
        static::$map          = [];
    }

    /**
     * @param int $count
     */
    public function stream_read($count): string {
        $result = substr($this->_data, $this->_position, $count);
        $this->_position += $count;

        if (!$result) {
            return '';
        }

        return $result;
    }

    public function stream_close(): bool {
        return true;
    }

    public function stream_eof(): bool {
        return $this->_position >= $this->_dataSize;
    }

    public function stream_stat(): array {
        return [
            'size' => $this->_dataSize,
        ];
    }

    /**
     * @param string $path
     * @param string $mode
     * @param string $options
     * @param string $opened_path
     */
    public function stream_open($path, $mode, $options, &$opened_path): bool {
        $this->_path        = $this->_translate($path, static::$basedir);
        [$foo, $this->_key] = explode('://', $path);

        if ($mode == 'r' || $mode == 'rb') {
            if (!is_readable($this->_path)) {
                return false;
            }
            $fp = fopen($this->_path, $mode);

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
     * @return array|bool
     */
    public function url_stat($path, $flags) {
        $translatedPath = $this->_translate($path, static::$basedir);

        // Suppress warnings if requested or if the file or directory does not
        // exist. This is consistent with PHP's plain filesystem stream wrapper.
        if ($flags & STREAM_URL_STAT_QUIET || !file_exists($translatedPath)) {
            return @stat($translatedPath);
        }

        return stat($translatedPath);
    }

    /**
     * @param string $uri
     * @param string $baseDir
     */
    protected function _translate($uri, $baseDir): string {
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

        return $baseDir . '/' . implode('/', $sane);
    }

    protected function _setDataSize($data): void {
        $this->_dataSize = strlen($data);
    }
}
