<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Modules\Config\LoadConfig;
use Tapestry\Modules\Content\Clear;
use Tapestry\Modules\Content\LoadMetaTypes;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\Content\ParseContentTypes;
use Tapestry\Modules\Content\ParseFrontMatter;
use Tapestry\Modules\Content\Write;
use Tapestry\Modules\Kernel\LoadKernel;
use Tapestry\Modules\Scripts\After;
use Tapestry\Modules\Scripts\Before;
use Tapestry\Modules\Scripts\LoadContentTypes;

class CompileStepsServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'Compile.Steps'
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
            LoadConfig::class,
            LoadKernel::class,
            Before::class,
            LoadContentTypes::class,
            //LoadMetaTypes::class,
            LoadSourceFiles::class,
            ParseFrontMatter::class,
            ParseContentTypes::class,
            Clear::class,
            Write::class,
            After::class
        ];

        $this->getContainer()->add('Compile.Steps', $steps);
    }
}