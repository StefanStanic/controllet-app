function fetch_statistics_data(){
    $.fn.jQuerySimpleCounter = function( options ) {
        var settings = $.extend({
            start:  0,
            end:    100,
            easing: 'swing',
            duration: 400,
            complete: ''
        }, options );

        var thisElement = $(this);

        $({count: settings.start}).animate({count: settings.end}, {
            duration: settings.duration,
            easing: settings.easing,
            step: function() {
                var mathCount = Math.ceil(this.count);
                thisElement.text(mathCount);
            },
            complete: settings.complete
        });
    };

    var userCount = 1;
    var billCount = 1;
    var transactionCount = 1;
    var accountCount = 1;


    $.get('/getHomePageStatistics', {}, function (data) {
        userCount = data.users;
        billCount = data.bills;
        transactionCount = data.transactions;
        accountCount = data.accounts;

        $('#counter_users').jQuerySimpleCounter({end: userCount,duration: 3000});
        $('#counter_transactions').jQuerySimpleCounter({end: transactionCount,duration: 3000});
        $('#counter_bills').jQuerySimpleCounter({end: billCount,duration: 2000});
        $('#counter_accounts').jQuerySimpleCounter({end: accountCount,duration: 2500});

    }, 'json');
}

$(document).ready(function () {
    fetch_statistics_data();
});