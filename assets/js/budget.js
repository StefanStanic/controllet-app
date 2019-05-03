$("#addBudgetForm").on('submit', function (e) {
    e.preventDefault();

    var category_id = $("#category").val();
    var account_id = $("#account_type").val();
    var user_id = $("#user_id").val();
    var budget_amount = $("#budget_amount").val();

    var confirm_dialog = confirm("Are you sure you want to add this budget?");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/addBudget",
            data: {
                category: category_id,
                account_type: account_id,
                user_id: user_id,
                budget_amount : budget_amount
            },
            success: function (data, textStatus, xhr) {
                if(xhr.status == 200)
                {
                    $("#successAddBudget").show();
                    $("#failAddBudget").hide();
                }
                else
                {
                    $("#successAddBudget").hide();
                    $("#failAddBudget").show();
                }
            }
        });
    }
});


$('#addBudget').on('hidden.bs.modal', function () {
    location.reload();
});