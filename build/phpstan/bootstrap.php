<?php declare(strict_types=1);

if (!class_exists('gnupg')) {
    class gnupg {
        public function import($p): array {
        }

        public function verify($p, $p2): array {
        }

        public function keyinfo(string $id): array {
        }
    }
}

if (!class_exists('DOMNodeList')) {
    /**
     * @template-covariant  TNode as DomElement
     * @template-implements Traversable<int, TNode>
     */
    class DOMNodeList implements Traversable, Countable
    {

        /**
         * @var int
         *
         * @since 5.0
         * The number of nodes in the list. The range of valid child node indices is 0 to length - 1 inclusive.
         * @link  http://php.net/manual/en/class.domnodelist.php#domnodelist.props.length
         */
        public $length;

        /**
         * @return TNode|null
         */
        public function item($index): void {
        }
    }
}

