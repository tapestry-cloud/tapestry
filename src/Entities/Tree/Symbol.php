<?php

namespace Tapestry\Entities\Tree;

class Symbol
{
    const SYMBOL_UNKNOWN = 0;
    const SYMBOL_KERNEL = 1;
    const SYMBOL_CONFIGURATION = 2;
    const SYMBOL_CONTENT_TYPE = 3;
    const SYMBOL_SOURCE = 4;

    /**
     * Unique identifier for this symbol.
     *
     * @var string
     */
    public $id;

    /**
     * @var int
     */
    public $type = self::SYMBOL_UNKNOWN;

    /**
     * The sha1 hash of the symbol if applicable.
     * E.g. for SYMBOL_SOURCE, SYMBOL_KERNEL, SYMBOL_CONFIGURATION this
     *      will be the file content, while for SYMBOL_CONTENT_TYPE as
     *      its built from configuration it isn't nessessary to have a
     *      hash value.
     *
     * @var string
     */
    public $hash;

    /**
     * The last time the symbol was modified.
     *
     * @var int
     */
    public $mTime;

    /**
     * Symbol constructor.
     * @param string $id
     * @param int $type
     * @param int $mTime
     */
    public function __construct(string $id, int $type, int $mTime)
    {
        $this->id = $id;
        $this->type = $type;
        $this->mTime = $mTime;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }

    /**
     * Compare a Symbol (from cache) to see if this symbol is valid.
     *
     * Useful for reducing the tree of Symbols to just those that have
     * been modified.
     *
     * Will return false if the symbol being compared is newer or different.
     *
     * Must be used against symbols of the same id, will throw an exception
     * if the id is different.
     *
     * @param Symbol $symbol
     * @return bool
     * @throws \Exception
     */
    public function isSame(self $symbol): bool
    {
        if ($symbol->id !== $this->id) {
            throw new \Exception('Symbol being compared must have the same identifier.');
        }

        if ($symbol->hash !== $this->hash) {
            return false;
        }

        if ($this->mTime > 0 && $symbol->mTime > $this->mTime) {
            return false;
        }

        return true;
    }
}
