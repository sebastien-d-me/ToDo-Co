const Encore = require("@symfony/webpack-encore");

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "prod");
}

Encore
    .setOutputPath("public/build/")
    .setPublicPath("/build")
    .addEntry("assets", "./assets/scripts/script.js")
    .copyFiles({
        from: "./assets/images",
        to: "images/[path][name].[ext]",
    })
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = "3.23";
    })
    .enableSassLoader()
    ;

module.exports = Encore.getWebpackConfig();