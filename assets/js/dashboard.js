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

initilize_spending_trend();
initilize_income();
initilize_expenses();

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

function initilize_spending_trend(){
    var options = {
        chart: {
            height: 350,
            type: "line",
            stacked: false
        },
        dataLabels: {
            enabled: false
        },
        colors: ["#FF1654", "#247BA0"],
        series: [
            {
                name: "Series A",
                data: [1.4, 2, 2.5, 1.5, 2.5, 2.8, 3.8, 4.6]
            },
            {
                name: "Series B",
                data: [20, 29, 37, 36, 44, 45, 50, 58]
            }
        ],
        stroke: {
            width: [4, 4]
        },
        plotOptions: {
            bar: {
                columnWidth: "20%"
            }
        },
        xaxis: {
            categories: [2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016]
        },
        yaxis: [
            {
                axisTicks: {
                    show: true
                },
                axisBorder: {
                    show: true,
                    color: "#FF1654"
                },
                labels: {
                    style: {
                        color: "#FF1654"
                    }
                },
                title: {
                    text: "Series A"
                }
            },
            {
                opposite: true,
                axisTicks: {
                    show: true
                },
                axisBorder: {
                    show: true,
                    color: "#247BA0"
                },
                labels: {
                    style: {
                        color: "#247BA0"
                    }
                },
                title: {
                    text: "Series B"
                }
            }
        ],
        tooltip: {
            shared: false,
            intersect: true,
            x: {
                show: false
            }
        },
        legend: {
            horizontalAlign: "left",
            offsetX: 40
        }
    };

    var chart = new ApexCharts(document.querySelector("#spending_trend"), options);

    chart.render();
}


function initilize_income(){
    var options = {
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        series: [44, 55, 13, 43, 22],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }

    var chart = new ApexCharts(
        document.querySelector("#chart2"),
        options
    );

    chart.render();
}

function initilize_expenses(){
    var options = {
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        series: [44, 55, 13, 43, 22],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }

    var chart = new ApexCharts(
        document.querySelector("#chart3"),
        options
    );

    chart.render();
}