<?php namespace Tapestry;

interface Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project);
}