<?php

namespace Tapestry\Providers;

use Tapestry\Modules\Api\Json;
use Tapestry\Modules\Content\Clean;
use Tapestry\Modules\Content\Clear;
use Tapestry\Modules\Content\Copy;
use Tapestry\Modules\Scripts\After;
use Tapestry\Modules\Scripts\Before;
use Tapestry\Modules\Content\Compile;
use Tapestry\Modules\Content\ReadCache;
use Tapestry\Modules\Kernel\BootKernel;
use Tapestry\Modules\Content\WriteCache;
use Tapestry\Modules\Content\WriteFiles;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\ContentTypes\LoadContentTypes;
use Tapestry\Modules\ContentTypes\ParseContentTypes;
use Tapestry\Modules\Renderers\LoadContentRenderers;
use Tapestry\Modules\Generators\LoadContentGenerators;
use League\Container\ServiceProvider\AbstractServiceProvider;

class CompileStepsServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'Compile.Steps',
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $steps = [
            BootKernel::class,
            ReadCache::class,
            Before::class,
            Clear::class,
            LoadContentTypes::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            LoadSourceFiles::class,
            Json::class,
            ParseContentTypes::class,
            Compile::class,
            WriteFiles::class,
            WriteCache::class,
            Copy::class,
            Clean::class,
            After::class,
        ];

        $this->getContainer()->add('Compile.Steps', $steps);
    }
}
