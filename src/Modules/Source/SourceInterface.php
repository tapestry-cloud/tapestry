<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\Permalink;

interface SourceInterface
{
    public function getUid() : string;

    public function setUid(string $uid);

    public function setData(string $key, $value = null);

    public function setDataFromArray(array $data);

    public function getData(string $key = null, $default = null);

    public function hasData(string $key) : bool;

    public function getRawContent() : string;

    public function setRenderedContent(string $content);

    public function getRenderedContent() : string;

    public function getPermalink() : Permalink;

    public function getCompiledPermalink() : string;

    public function setOverloaded(string $key, $value);

    public function getFilename(bool $overloaded = true) : string;

    public function getExtension(bool $overloaded = true) : string;

    public function hasChanged() : bool;

    public function setHasChanged(bool $value = true);

    public function hasContent() : bool;

    public function isRendered() : bool;

    public function setRendered(bool $value = true);

    public function isToCopy() : bool;

    public function setToCopy(bool $value = true);

    public function isIgnored() : bool;

    public function setIgnored(bool $value = true);
}