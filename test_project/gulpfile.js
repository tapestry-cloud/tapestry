var gulp = require('gulp');
var elixir = require('laravel-elixir');
var argv = require('yargs').argv;

elixir.config.assetsPath = 'source/_assets';
elixir.config.publicPath = 'source';

elixir(function (mix) {
    var env = argv.e || argv.env || 'local';
    var port = argv.p || argv.port || 3000;

    mix.less('main.less')
        .exec('php ../bin/tapestry.php build --quiet --env=' + env, ['./source/*', './source/**/*', '!./source/_assets/**/*'])
        .browserSync({
            port: port,
            server: {baseDir: 'build_' + env},
            proxy: null,
            files: ['build_' + env + '/**/*']
        });
});