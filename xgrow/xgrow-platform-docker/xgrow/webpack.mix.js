const mix = require('laravel-mix');

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
// Original
// mix.js('resources/js/app.js', 'public/js')
//    .sass('resources/sass/app.scss', 'public/css');


mix.js('resources/js/app.js', 'public/js/bundle').vue()
    .js('resources/js/course/experience.js', 'public/js/bundle/experience.js').vue()
    .js('resources/js/reports/research.js', 'public/js/bundle/researchReport.js').vue()
    .js('resources/js/reports/progress.js', 'public/js/bundle/progressReport.js').vue()
    .js('resources/js/reports/simplified-progress.js', 'public/js/bundle/simplifiedProgressReport.js').vue()
    .js('resources/js/gamification/dashboard.js', 'public/js/bundle/gamification-dashboard.js').vue()
    .js('resources/js/gamification/configurations.js', 'public/js/bundle/gamification-config.js').vue()
    .js('resources/js/gamification/challenges.js', 'public/js/bundle/gamification-challenges.js').vue()
    .js('resources/js/gamification/reports.js', 'public/js/bundle/gamification-reports.js').vue()
    .js('resources/js/dashboard/index.js', 'public/js/bundle/dashboard.js').vue()
    .js('resources/js/platforms/platforms.js', 'public/js/bundle/platforms.js').vue()
    .js('resources/js/platforms/register.js', 'public/js/bundle/register.js').vue()
    .js('resources/js/platforms/start-flow.js', 'public/js/bundle/start-flow.js').vue()
    .js('resources/js/platforms/client-data.js', 'public/js/bundle/client-data.js').vue()
    .js('resources/js/coproducer/coproducer.js', 'public/js/bundle/coproducer.js').vue()
    .js('resources/js/integrations/integrations.js', 'public/js/bundle/integrations.js').vue()
    .js('resources/js/pages/financial.js', 'public/js/bundle/reports-financial.js').vue()
    .js('resources/js/pages/subscriptions.js', 'public/js/bundle/subscriptions.js').vue()
    .js('resources/js/pages/learning-area.js', 'public/js/bundle/learning-area.js').vue()
    .js('resources/js/pages/resources.js', 'public/js/bundle/resources.js').vue()
    .js('resources/js/affiliates/invite.js', 'public/js/bundle/affiliates-invite.js').vue()
    .js('resources/js/pages/affiliate-area.js', 'public/js/bundle/affiliate-area.js').vue()
    .js('resources/js/pages/affiliates.js', 'public/js/bundle/affiliates.js').vue()
    .js('resources/js/pages/documents.js', 'public/js/bundle/documents.js').vue()
    .js('resources/js/pages/developer.js', 'public/js/bundle/developer.js').vue()
    .js('resources/js/pages/sales.js', 'public/js/bundle/sales.js').vue()
    .js('resources/js/pages/settings-subscribers.js', 'public/js/bundle/users-platforms.js').vue()
    .js('resources/js/products/edit.js', 'public/js/bundle/products-edit.js').vue()
    .js('resources/js/products/create-delivery.js', 'public/js/bundle/products-create-delivery.js').vue()
    .js('resources/js/pages/products.js', 'public/js/bundle/products.js').vue()
    .js('resources/js/pages/coupons.js', 'public/js/bundle/coupons.js').vue()
    .js('resources/js/pages/subscribers.js', 'public/js/bundle/subscribers.js').vue()
    .js('resources/js/pages/import-subscribers', 'public/js/bundle/import-subscribers.js').vue()
    .sass('resources/sass/products/affiliates.scss', 'public/css/bundle/products-affiliates.css')
    .postCss('resources/css/app.css', 'public/css/bundle', []);
