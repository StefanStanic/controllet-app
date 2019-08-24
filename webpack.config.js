var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js')
    .addEntry('registration', './assets/js/registration.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('navbar', './assets/js/navbar.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('addAccount', './assets/js/addAccount.js')
    .addEntry('transactions', './assets/js/transactions.js')
    .addEntry('profile', './assets/js/profile.js')
    .addEntry('budget', './assets/js/budget.js')
    .addEntry('bills', './assets/js/bills.js')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')
    .copyFiles({
        from: './assets/images'
    })

    .splitEntryChunks()

    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
