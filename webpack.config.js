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
    .addEntry('homepage', './assets/js/homepage.js')
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('user', './assets/js/user.js')
    .copyFiles({
        from: './assets/images'
    })

    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
