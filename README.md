A basic static site generator written in PHP.
 
 Build Workflow
 
 1. Initiate Configuration
 2. Initiate Kernel
 3. Build Content Types from default/configuration
    * For each content type identify taxonomy
 4. Boot Kernel
 5. Search through source directory and build tree of files found
 6. Bucket each file into a content type and build taxonomy collections
 7. Cache Content Tree
 8. Traverse content tree and build output
 9. If not watching then exit, else wait for filesystem change and then update content tree cache before starting at 7.
 
 Note: If watching a folder for changes do so against the cached content tree and update it as necessary.
 
 ## Content Types
 
 Content type configuration can have the following properties:
 * {**string**} name
 * {**string**} permalink
 * {**array**} taxonomy
 
 When a content type object is created, the `name` will be prefixed with an underscore, slugified and used as the source path within the working directory. Therefore a _blog_ content type will have its content searched for in `_blog`; while its templates will be loaded from the `blog` folder.
 
 This way the following directory structure is valid for a blog content type:
 
 ```
 ├─ source
 │  ├─ _blog
 │  │  └─ 11-02-2016-hello-world.md
 │  ├─ blog
 │  │  ├─ single.phtml
 │  │  └─ list.phtml
 │  ├─ about-us.blade.php
 │  └─ index.blade.php
 └─ config.php
 ```
 
The `blog/single.phtml` template file will be used to generate each blog post, while the `blog/list.phtml` file will have passed to it the collection of blog posts - it can then paginate them if it so chooses via requesting so within its frontmatter.
 
A content types taxonomy can also be listed and the engine will look for `list-{taxonomy_name}.phtml` within the template folder path. The permalink url for this defaults to `/blog/{category_name}/{?page}` but can be set in the template front matter.

