<?php namespace Tapestry\Entities;

use Tapestry\ArrayContainer;

class Project extends ArrayContainer
{
    public function __construct($currentWorkingDirectory, $environment)
    {
        parent::__construct([]);
    }
}