const Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/app.js")
    .addStyleEntry("header", "./assets/styles/header.css")
    .addStyleEntry("homepage", "./assets/styles/homepage.css")
    .addStyleEntry("settings", "./assets/styles/settings.css")
    .addStyleEntry("user_profiler", "./assets/styles/user_profiler.css")
    .addStyleEntry("media", "./assets/styles/media.css")
    .addStyleEntry("person", "./assets/styles/person.css")
    .addStyleEntry("login", "./assets/styles/login.css")
    .addStyleEntry("login_partial", "./assets/styles/login_partial.css")
    .addStyleEntry("detail", "./assets/styles/detail.css")
    .addStyleEntry("movie-detail", "./assets/styles/movie_detail.css")
    .addStyleEntry("show-detail", "./assets/styles/show_detail.css")
    .addStyleEntry("stream", "./assets/styles/stream.css")
    .addStyleEntry("errors", "./assets/styles/errors.css")
    .addStyleEntry("links", "./assets/styles/links.css")
    .addStyleEntry("searchbar", "./assets/styles/searchbar.css")

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()

    // Displays build status system notifications to the user
    // .enableBuildNotifications()

    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    //Copy content from assets images to public
    .copyFiles({
        from: "./assets/images",
        to: "images/[path][name].[ext]",
    })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = "3.38";
    });

// enables Sass/SCSS support
//.enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment if you use React
//.enableReactPreset()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
