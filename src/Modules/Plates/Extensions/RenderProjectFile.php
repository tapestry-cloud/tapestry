<?php

namespace Tapestry\Modules\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension;
use Tapestry\Entities\ProjectFile;

/**
 * Class Site.
 *
 * The Site extension to Plates provides the user with the `site` method.
 * This allows you to get site configuration by key.
 */
class RenderProjectFile implements Extension
{
    /**
     * Register the `site` helper with Plates.
     *
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->addMethods([
            'renderProjectFile' => function (Engine $plates, ProjectFile $file) {
                $c = $plates->getContainer();

                $rt = new \League\Plates\RenderTemplate\FileSystemRenderTemplate([
                    [
                        \League\Plates\Template\matchExtensions($c->get('config')['php_extensions']),
                        new \League\Plates\RenderTemplate\PhpRenderTemplate($c->get('renderTemplate.bind')),
                    ],
                    [
                        \League\Plates\Template\matchExtensions($c->get('config')['image_extensions']),
                        \League\Plates\RenderTemplate\MapContentRenderTemplate::base64Encode(new \League\Plates\RenderTemplate\StaticFileRenderTemplate()),
                    ],
                    [
                        \League\Plates\Template\matchStub(true),
                        new \League\Plates\RenderTemplate\StaticFileRenderTemplate(),
                    ],
                ]);
                if ($c->get('config')['validate_paths']) {
                    $rt = new \League\Plates\RenderTemplate\ValidatePathRenderTemplate($rt, $c->get('fileExists'));
                }
                $rt = array_reduce($c->get('renderTemplate.factories'), function ($rt, $create) {
                    return $create($rt);
                }, $rt);
                $rt = new \League\Plates\RenderTemplate\ComposeRenderTemplate($rt, $c->get('compose'));

                return $rt;
            },
        ]);
    }
}
