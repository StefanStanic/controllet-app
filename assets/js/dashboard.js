require('../css/dashboard.css');

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
    var user_id = $("#user_id").val();
    var account_id = $(this).val();
    var category_id = $("#category_filter").val();

    $.post('/filterTransactions', {'user_id': user_id, 'account_id': account_id , 'category_id': category_id}, function (data) {
        $('.transaction_list').html(data.html);
    }, 'json');
});

$("#category_filter").on('change', function (e) {
    var user_id = $("#user_id").val();
    var account_id = $("#account_type_filter").val();
    var category_id = $(this).val();

    $.post('/filterTransactions', {'user_id': user_id, 'account_id': account_id , 'category_id': category_id}, function (data) {
        if(data.html === ''){
            $('.transaction_list').html('No transactions based on current filter.');
        }else{
            $('.transaction_list').html(data.html);
        }
    }, 'json');
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