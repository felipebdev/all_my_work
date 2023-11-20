let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const sassOptions = { precision: 5 };

/* ==============================================================
 CORE STYLESHEETS -->
============================================================== */

const resourceFolder = `resources/vendor/wrappixel/monster-admin/4.2.1`;

mix
    //Template
    .sass(`${resourceFolder}/monster/scss/style.scss`,       'public/css/monster/style.css', sassOptions)

    //Color Palettes
    .sass(`${resourceFolder}/monster/scss/colors/blue.scss`,           'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/blue-dark.scss`,      'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/default.scss`,        'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/default-dark.scss`,   'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/green.scss`,          'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/green-dark.scss`,     'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/megna.scss`,          'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/megna-dark.scss`,     'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/purple.scss`,         'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/purple-dark.scss`,    'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/red.scss`,            'public/css/colors', sassOptions)
    .sass(`${resourceFolder}/monster/scss/colors/red-dark.scss`,       'public/css/colors', sassOptions);

/* ==============================================================
END CORE STYLESHEETS -->
============================================================== */

/* ==============================================================
 APPLICATION FILES -->
============================================================== */

mix
    .js('resources/js/home-one.js','public/js').vue()
    //.js('resources/js/home-two.js','public/js').vue()
;

/* ==============================================================
 END APPLICATION FILES -->
============================================================== */

mix.version();
