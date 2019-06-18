require('../css/budget.css');

$("#addBudgetForm").on('submit', function (e) {
    e.preventDefault();

    var category_id = $("#category").val();
    var account_id = $("#account_type").val();
    var user_id = $("#user_id").val();
    var budget_amount = $("#budget_amount").val();

    var confirm_dialog = confirm("Are you sure you want to add this budget?");

    //location to post to
    if(confirm_dialog === true){
        // $.ajax({
        //     type:"POST",
        //     url: "/addBudget",
        //     dataType: 'application/json',
        //     data: {
        //         category: category_id,
        //         account_type: account_id,
        //         user_id: user_id,
        //         budget_amount : budget_amount
        //     },
        //     success: function (data, textStatus, xhr) {
        //         console.log(data.status);
        //         if(xhr.status == 200 && parseInt(data.status) === 1)
        //         {
        //             $("#successAddBudget").show();
        //             $("#failAddBudget").hide();
        //         } else
        //         {
        //             $("#successAddBudget").hide();
        //             $("#failAddBudget").show();
        //         }
        //     }
        // });
        $.post('/addBudget', {'category': category_id, 'account_type': account_id, 'user_id': user_id, 'budget_amount' : budget_amount}, function (data) {
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


$('#addBudget').on('hidden.bs.modal', function () {
    location.reload();
});