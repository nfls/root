// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addEntry('vue', './web/main.js')
    .enableSassLoader()
    .autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVueLoader()
    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()
;
module.exports = Encore.getWebpackConfig();

