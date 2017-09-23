<?php

namespace Tapestry\Entities\WorkspaceScaffold\Blog;

use Tapestry\Entities\WorkspaceScaffold;

class BlogPostWorkspaceScaffold extends WorkspaceScaffold
{
    /**
     * BlogPostWorkspaceScaffold constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'Blog Post',
            'Create a new blog post',
            [
                new WorkspaceScaffold\Blog\Steps\AskForTitle(),
                new WorkspaceScaffold\Blog\Steps\WriteToDisk()
            ]
        );
    }
}