var Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('js/custom', './build/js/custom.js')
    .addStyleEntry('css/custom', './build/css/custom.css')
    // .splitEntryChunks()
    // .enableSingleRuntimeChunk()
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    // .enableSourceMaps(!Encore.isProduction())
    // .enableVersioning(Encore.isProduction())
    .autoProvidejQuery()
    .enableVersioning(false)
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
;

module.exports = Encore.getWebpackConfig();