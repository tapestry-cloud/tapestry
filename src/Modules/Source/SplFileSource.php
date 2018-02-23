<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\Permalink;

class SplFileSource extends AbstractSource implements SourceInterface
{
    public function getUid(): string
    {
        // TODO: Implement getUid() method.
    }

    public function setUid(string $uid)
    {
        // TODO: Implement setUid() method.
    }

    public function setData(string $key, $value = null)
    {
        // TODO: Implement setData() method.
    }

    public function setDataFromArray(array $data)
    {
        // TODO: Implement setDataFromArray() method.
    }

    public function getData(string $key = null, $default = null)
    {
        // TODO: Implement getData() method.
    }

    public function hasData(string $key): bool
    {
        // TODO: Implement hasData() method.
    }

    public function getRawContent(): string
    {
        // TODO: Implement getRawContent() method.
    }

    public function setRenderedContent(string $content)
    {
        // TODO: Implement setRenderedContent() method.
    }

    public function getRenderedContent(): string
    {
        // TODO: Implement getRenderedContent() method.
    }

    public function getPermalink(): Permalink
    {
        // TODO: Implement getPermalink() method.
    }

    public function getCompiledPermalink(): string
    {
        // TODO: Implement getCompiledPermalink() method.
    }

    public function setOverloaded(string $key, $value)
    {
        // TODO: Implement setOverloaded() method.
    }

    public function getFilename(bool $overloaded = true): string
    {
        // TODO: Implement getFilename() method.
    }

    public function getExtension(bool $overloaded = true): string
    {
        // TODO: Implement getExtension() method.
    }

    public function hasChanged(): bool
    {
        // TODO: Implement hasChanged() method.
    }

    public function setHasChanged(bool $value = true)
    {
        // TODO: Implement setHasChanged() method.
    }

    public function isRendered(): bool
    {
        // TODO: Implement isRendered() method.
    }

    public function setRendered(bool $value = true)
    {
        // TODO: Implement setRendered() method.
    }

    public function isToCopy(): bool
    {
        // TODO: Implement isToCopy() method.
    }

    public function setToCopy(bool $value = true)
    {
        // TODO: Implement setToCopy() method.
    }

    public function isIgnored(): bool
    {
        // TODO: Implement isIgnored() method.
    }

    public function setIgnored(bool $value = true)
    {
        // TODO: Implement setIgnored() method.
    }
}