var elixir = require('laravel-elixir');

var paths = {
    'jquery': './node_modules/jquery/dist/',
    'fontawesome': './node_modules/font-awesome/',
    'chartsjs': './node_modules/chart.js/dist/',
    'css': './resources/css/',
    'js': './resources/js/',
    'images': './resources/images/',
    'fonts': './resources/fonts/',
    'storage': './storage/app/',
    'weapons': './resources/images/weapons/',
    'semantic': './semantic/dist/'
};

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
 mix
     .copy(paths.jquery + 'jquery.min.js', 'public/js/jquery.min.js')
     .copy(paths.chartsjs + 'Chart.bundle.min.js', 'public/js/chart.js')
     .copy(paths.fontawesome + 'css/font-awesome.min.css', 'public/css/font-awesome.min.css')
     .copy(paths.fontawesome + 'fonts', 'public/fonts/')
     .copy(paths.fonts, 'public/fonts')
     .copy(paths.images, 'public/images/')
     .copy(paths.storage + 'resources/images/h5-medals.png', 'public/css/images/h5-medals.png')
     .copy(paths.images + 'bg.png', 'public/css/images/bg.png')
     .copy(paths.semantic + '/themes/', 'public/build/css/themes/')
     .copy(paths.css, 'public/css/')
     .copy(paths.js, 'public/js/')
     .styles([
         paths.css + 'fonts.css',
         paths.css + 'main.css',
         paths.semantic + 'semantic.css',
         paths.storage + 'resources/css/h5-sprites.css'
     ], "public/css/app.css")
     .scripts([
         paths.js + 'main.js',
         paths.semantic + 'semantic.min.js',
         paths.js + 'tablesort.js'
     ], "public/js/app.js")
     .version(["public/css/app.css", "public/js/app.js"]);
});
