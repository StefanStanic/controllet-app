import ApexCharts from "apexcharts";


initilize_income();
initilize_expenses();

function initilize_income(){
    var income = JSON.parse(get_chart_data(2));
    var pieLabels = [];
    var pieSeries = [];


    $.each(income.data, function (key, value) {
        pieLabels.push(value.category_name);
        pieSeries.push(parseInt(value.category_amount));
    });

    var options = {
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: pieLabels,
        series: pieSeries,
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

    if(income.data != false){
        chart.render();
    }
}

function initilize_expenses(){
    var income = JSON.parse(get_chart_data(1));
    var pieLabels = [];
    var pieSeries = [];

    $.each(income.data, function (key, value) {
        pieLabels.push(value.category_name);
        pieSeries.push(parseInt(value.category_amount));
    });

    var options = {
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: pieLabels,
        series: pieSeries,
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
    };

    var chart = new ApexCharts(
        document.querySelector("#chart3"),
        options
    );

    chart.render();
}

function get_chart_data(data_type) {
    //get data from the api
    var return_data ='';
    $.ajax({
        type:"POST",
        url: "/cashFlow",
        async: false,
        data: {
            type: data_type,
        },
        success: function (data, textStatus, xhr) {
            if(xhr.status == 200)
            {
                return_data = data;
            }

        }

    });

    return return_data;
}