// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/vue/')
    .setPublicPath('/vue')
    .addEntry('vue', './web/main.js')
    .enableSassLoader()
    .autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVueLoader(function(options){
        options.loaders = {
            i18n: '@kazupon/vue-i18n-loader'
        }
    })
    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()
;
module.exports = Encore.getWebpackConfig();

