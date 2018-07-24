// webpack.config.js
var Encore = require('@symfony/webpack-encore');
var path = require('path');

function resolve (dir) {
    return path.join(__dirname, dir)
}

Encore
    .setOutputPath('public/vue/')
    .setPublicPath('/vue')
    .addEntry('vue', './web/main.js')
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVueLoader(function(options){
        options.loaders = {
            i18n: '@kazupon/vue-i18n-loader'
        }
    })
    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()
    .addLoader({
        test: /\.js$/,
        loader: 'babel-loader',
        include: [
            resolve('node_modules/webpack-dev-server/client'),
            resolve('node_modules/markdown-palettes/src'),
            resolve('node_modules/ali-oss/dist'),
            resolve('node_modules/ali-oss/lib'),
            resolve('node_modules/ali-oss/shims'),
            resolve('node_modules/vue-material/src'),
            resolve('node_modules/vue-material/src')
        ]
    })
;
module.exports = Encore.getWebpackConfig();

