import ApexCharts from 'apexcharts'
require('../css/dashboard.css');

// var options = {
//     chart: {
//         type: 'line'
//     },
//     series: [{
//         name: 'sales',
//         data: [30,40,35,50,49,60,70,91,125]
//     }],
//     xaxis: {
//         categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
//     }
// }
//
// var balance_chart = new ApexCharts(document.querySelector("#balance_change"), options);
// var expense_chart = new ApexCharts(document.querySelector("#expense_change"), options);
//
// balance_chart.render();
// expense_chart.render();


initialize_charts_by_filters(1);
initialize_charts_by_filters(2);


//initialize date-pickers
var options={
    format: 'dd-mm-yyyy',
    todayHighlight: true,
    autoclose: true,
};
$("#date_from").datepicker(options);
$("#date_to").datepicker(options);

//on delete
$(".delete_account").on('click', function (e) {
    e.preventDefault();

    var confirm_dialog = confirm("Are you sure you want to delete the account?");

    //get button id value
    var id_to_delete = this.value;
    var user_id = this.getAttribute("data-user_id_delete");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/deleteAccount",
            data: {
                account_id: id_to_delete,
                user_id : user_id
            },
            success: function (data, textStatus, xhr) {
                if(xhr.status == 200)
                {
                    location.reload();
                }

            }

        });
    }
});

$(".update_account").on('click', function (e) {

    //get all data so i can populate the popup
    var account_id = this.value;
    var accountName = $("#accountName_"+account_id).html();
    var accountBalance = parseInt($("#accountBalance_"+account_id).html());
    var user_id = this.getAttribute("data-user_id_update");

    //update the data in feelds
    $("#accountName").val(accountName);
    $("#accountBalance").val(parseInt(accountBalance));

    $("#updateAccountModal").on('click', function (e) {

        accountName = $("#accountName").val();
        accountBalance = parseInt($("#accountBalance").val());

        e.preventDefault();
        var confirm_dialog = confirm("Are you sure you want to update this account?");

        //location to post to
        if(confirm_dialog === true){
            $.ajax({
                type:"POST",
                url: "/updateAccount",
                data: {
                    accountName: accountName,
                    accountBalance: accountBalance,
                    user_id : user_id,
                    account_id : account_id
                },
                success: function (data, textStatus, xhr) {
                    if(xhr.status == 200)
                    {
                        $("#sucessUpdate").show();
                        $("#failUpdate").hide();
                    }
                    else
                    {
                        $("#sucessUpdate").hide();
                        $("#failUpdate").show();
                    }
                }
            });
        }

    })
});

$("#account_type_filter").on('change', function (e) {
    apply_filters();
});

$("#category_filter").on('change', function (e) {
    apply_filters();
});

$("#date_from").on('change', function (e) {
    apply_filters();
});

$("#date_to").on('change', function (e) {
    apply_filters();
});

$(".edit_transaction").on('click', function (e) {
    e.preventDefault();

    var transaction_id = $(this).data('id');

    //enable fields for edit
    $(".transaction_category_"+transaction_id).prop('disabled', false);
    $(".transaction_note_"+transaction_id).prop('disabled', false);
    $(".transaction_amount_"+transaction_id).prop('disabled', false);
    $(".update_transaction_"+transaction_id).show();

    //add update event listener for transaction button
    $(".update_transaction_"+transaction_id).on('click', function (e) {
        e.preventDefault();
        var transaction_category = $(".transaction_category_"+transaction_id).val();
        var transaction_note = $(".transaction_note_"+transaction_id).val();
        var transaction_amount = $(".transaction_amount_"+transaction_id).val();


        var confirm_dialog = confirm("Are you sure you want to update this transaction?");

        //location to post to
        if(confirm_dialog === true){
            $.ajax({
                type:"POST",
                url: "/updateTransaction",
                data: {
                    transaction_id: transaction_id,
                    transaction_category: transaction_category,
                    transaction_note : transaction_note,
                    transaction_amount : transaction_amount
                },
                success: function (data, textStatus, xhr) {
                    if(xhr.status == 200)
                    {
                        $("#transaction_response_"+transaction_id).html("Transaction successfully updated.");
                        $(".update_transaction_"+transaction_id).hide();
                    }
                    else
                    {
                        $("#transaction_response_"+transaction_id).html("All transaction fields need to be filled.");
                    }
                }
            });
        }
    });

});

$(".delete_transaction").on('click', function (e) {
   e.preventDefault();

    var transaction_id = $(this).data('id');

    var confirm_dialog = confirm("Are you sure you want to update this transaction?");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/deleteTransaction",
            data: {
                transaction_id: transaction_id,
            },
            success: function (data, textStatus, xhr) {
            }
        });
    }
});


$('#addTransaction').on('hidden.bs.modal', function () {
    location.reload();
});

function apply_filters() {
    var user_id = $("#user_id").val();
    var category_id = $("#category_filter").val();
    var account_id = $("#account_type_filter").val();
    var start_date = $("#date_from").val();
    var end_date = $("#date_to").val();

    $.post('/filterTransactions', {'user_id': user_id, 'account_id': account_id , 'category_id': category_id,  'date_from': start_date, 'date_to': end_date}, function (data) {
        if(data.html === ''){
            $('.transaction_list').html('No transactions based on current filter.');
        }else{
            $('.transaction_list').html(data.html);
        }
    }, 'json');
}

function initialize_charts_by_filters(data_type = ''){

    if(data_type === ''){
        return false;
    }

    var user_id = $("#user_id").val();
    var category_id = $("#category_filter").val();
    var account_id = $("#account_type_filter").val();
    var start_date = $("#date_from").val();
    var end_date = $("#date_to").val();

    $.post('/getChartDataByFiltersAndType', {'data_type': data_type, 'user_id': user_id, 'account_id': account_id , 'category_id': category_id, 'date_from': start_date, 'date_to': end_date}, function (data) {
        //data
        var chart_data = [];
        var chart_series = [];

        $.each(data.data, function (key, value) {
            chart_data.push(value.total_daily_expense);
            chart_series.push(value.transaction_day);
        })

        //options
        var options = {
            chart: {
                type: 'line'
            },
            series: [{
                name: (data_type == 1)? 'Expenses': 'Income',
                data: chart_data
            }],
            xaxis: {
                categories: chart_series
            }
        }

        // var balance_chart = new ApexCharts(document.querySelector("#balance_change"), options);
        var chart = new ApexCharts(document.querySelector((data_type == 1)? "#expense_change" : "#income_change"), options);

        // balance_chart.render();
        chart.render();
    }, 'json');
}