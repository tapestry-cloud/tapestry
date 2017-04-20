<?php

namespace Tapestry;

class Version
{
    public static function build()
    {
        $buildManifestPath = __DIR__ . '/build.json';
        if (!file_exists($buildManifestPath)) {
            return Tapestry::VERSION;
        }

        $build = json_decode(file_get_contents($buildManifestPath));
        return Tapestry::VERSION . ' [' . $build->hash . '] ' . $build->date;
    }
}