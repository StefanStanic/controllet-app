require('../css/budget.css');

$( document ).ready(function() {
    populateBudgetProgressBars();
});


$("#addBudgetForm").on('submit', function (e) {
    e.preventDefault();

    var category_id = $("#category").val();
    var account_id = $("#account_type").val();
    var user_id = $("#user_id").val();
    var budget_amount = $("#budget_amount").val();
    var budget_name = $("#budget_name").val();

    var confirm_dialog = confirm("Are you sure you want to add this budget?");

    //location to post to
    if(confirm_dialog === true){
        $.post('/addBudget', {'category': category_id, 'account_type': account_id, 'user_id': user_id, 'budget_amount' : budget_amount, 'budget_name': budget_name}, function (data) {
            if(parseInt(data.status) === 1)
                    {
                        $("#successAddBudget").show();
                        $("#failAddBudget").hide();
                    } else
                    {
                        $("#successAddBudget").hide();
                        $("#failAddBudget").show();
                    }
        }, 'json');
    }
});


$(".update_budget").on('click', function (e) {

    //get all data so i can populate the popup
    var budget_id = this.value;
    var accountType = $("#budgetAccountType_"+budget_id).val();
    var category = $("#budgetCategory_"+budget_id).val();
    var budgetName = $("#budgetName_"+budget_id).html();
    var budgetAmount = parseInt($("#budgetAmount_"+budget_id).html());

    //update the data in feelds
    $("#update_budget_name").val(budgetName);
    $("#update_account_type").val(accountType);
    $("#update_category").val(category);
    $("#update_budget_amount").val(budgetAmount);


    $("#updateBudgetButton").on('click', function (e) {

        var accountType = $("#update_account_type").val();
        var category = $("#update_category").val();
        var budgetName = $("#update_budget_name").val();
        var budgetAmount = $("#update_budget_amount").val();

        e.preventDefault();
        var confirm_dialog = confirm("Are you sure you want to update this budget?");

        //location to post to
        if(confirm_dialog === true){
            $.ajax({
                type:"POST",
                url: "/updateBudget",
                data: {
                    budget_id: budget_id,
                    accountType: accountType,
                    category: category,
                    budgetName : budgetName,
                    budgetAmount : budgetAmount
                },
                success: function (data, textStatus, xhr) {
                    if(xhr.status == 200)
                    {
                        $("#successUpdateBudget").show();
                        $("#failUpdateBudget").hide();
                    }
                    else
                    {
                        $("#successUpdateBudget").hide();
                        $("#failUpdateBudget").show();
                    }
                }
            });
        }

    })
});


//on delete
$(".delete_budget").on('click', function (e) {
    e.preventDefault();

    var confirm_dialog = confirm("Are you sure you want to delete this budget?");

    //get button id value
    var budget_id = this.value;

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/deleteBudget",
            data: {
                budget_id: budget_id,
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

$('#addBudget').on('hidden.bs.modal', function () {
    location.reload();
});

function populateBudgetProgressBars(){
    $.each($(".account_id"), function () {
        var account_id = $(this).val();

        //ajax for each budget
        $.post('/getExpenseByAccountTotal', {'account_id': account_id}, function (data) {
            if(parseInt(data.status) === 1)
            {
                var total_expense = parseInt(data.data[0].total_expenses);
                var total_allowed = parseInt($("#budget_amount_"+account_id).val());

                //calculate percentage
                var percentage = Math.max(Math.round(100 - (total_expense/total_allowed)*100), 0);

                //populate data
                $("#budget_from_"+account_id).html(total_expense);
                $("#budget_to_"+account_id).html(total_allowed);

                if(percentage != 0){
                    $("#budget_progress_"+account_id).css('width', percentage+'%')
                }
            }
        }, 'json');
    });
}