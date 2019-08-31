require('../css/transactions.css');

$(document).ready(function() {
    initializeDatePickers();
    initializeSubCategory();

    $('#transaction_table').DataTable({
        sorting: false,
        searching: false,
        paging: true
    });

} );


$("#addTransactionForm").on('submit', function (e) {
    e.preventDefault();

    //gather all the data
    var user_id = $("#user_id").val();
    var transaction_name = $("#transactionName").val();
    var transaction_account_type = $("#transactionAccountType").val();
    var transaction_type = $("#transactionType").val();
    var transaction_category = $("#transactionCategory").val();
    var transaction_subcategory = $("#transactionSubcategory").val();
    var transaction_amount = $("#transactionAmount").val();
    var transaction_note = $("#transactionNote").val();
    var transaction_currency = $("#transactionCurrency").val();

    $.ajax({
        type:"POST",
        url: "/addTransaction",
        dataType: "json",
        data: {
            user_id : user_id,
            transactionName: transaction_name,
            transactionAccountType: transaction_account_type,
            transactionType: transaction_type,
            transactionCategory: transaction_category,
            transactionSubCategory: transaction_subcategory,
            transactionAmount: transaction_amount,
            transactionNote: transaction_note,
            transaction_currency: transaction_currency
        },
        success: function (data, textStatus, xhr) {
            if(xhr.status === 200 && data.status === 1) {
                toastr.success('Successfully added a transaction');

                //reset form
                setTimeout(function() {
                    $("#transactionName").val('');
                    $("#transactionType").val(1);
                    $("#transactionCategory").val(1);
                    $("#transactionSubcategory").val(1);
                    $("#transactionAmount").val('');
                    $("#transactionNote").val('');
                    $("#transactionCurrency").val(1);
                }, 2000);
            }
            else{
                toastr.error('Error adding a transaction. Try Again later')
            }
        }
    });
});

$('#addTransaction').on('hidden.bs.modal', function () {
    location.reload();
});

$(".edit_transaction").on('click', function (e) {
    e.preventDefault();

    var transaction_id = $(this).data('id');

    //enable fields for edit
    $(".transaction_category_"+transaction_id).prop('disabled', false);
    $(".transaction_subcategory_"+transaction_id).prop('disabled', false);
    $(".transaction_note_"+transaction_id).prop('disabled', false);
    $(".transaction_amount_"+transaction_id).prop('disabled', false);
    $(".update_transaction_"+transaction_id).show();

    //add update event listener for transaction button
    $(".update_transaction_"+transaction_id).on('click', function (e) {
        e.preventDefault();
        var transaction_category = $(".transaction_category_"+transaction_id).val();
        var transaction_subcategory = $(".transaction_subcategory_"+transaction_id).val();
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
                    transaction_subcategory: transaction_subcategory,
                    transaction_note : transaction_note,
                    transaction_amount : transaction_amount
                },
                success: function (data, textStatus, xhr) {
                    if(xhr.status == 200)
                    {
                        toastr.success('Successfully updated your transaction');

                        $(".update_transaction_"+transaction_id).hide();
                        $(".transaction_category_"+transaction_id).prop('disabled', true);
                        $(".transaction_subcategory_"+transaction_id).prop('disabled', true);
                        $(".transaction_note_"+transaction_id).prop('disabled', true);
                        $(".transaction_amount_"+transaction_id).prop('disabled', true);

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
                toastr.success('Successfully deleted your transaction');

                //reset form
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    }
});

$(".transaction_category").on('change', function () {
    var category_id = $(this).val();
    var transaction_id = $(this).data('id');

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $(".transaction_subcategory_"+transaction_id).html(data.html);
        }
    });
});

function initializeDatePickers() {

    $('#dateFromFilter').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true
    });

    $('#dateToFilter').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true
    });
}

function initializeSubCategory(){
    var category_id = $("#transactionCategoryFilter").val();

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $("#transactionSubcategoryFilter").html(data.html);
        }
    });
}

$("#transactionCategoryFilter").on('change', function () {
    var category_id = $("#transactionCategoryFilter").val();

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $("#transactionSubcategoryFilter").html(data.html);
        }
    });
});

$("#transactionAccountTypeFilter, #transactionCategoryFilter, #transactionSubcategoryFilter, #dateFromFilter, #dateToFilter"). on('change', function () {

    var user_id = $("#user_id").val();
    var accountType = $("#transactionAccountTypeFilter").val();
    var category = $("#transactionCategoryFilter").val();
    var subcategory = $("#transactionSubcategoryFilter").val();
    var dateFrom = $("#dateFromFilter").val();
    var dateTo = $("#dateToFilter").val();

    $.ajax({
        type:"POST",
        url: "/filterTransactions",
        dataType: 'json',
        data: {
            account_id: accountType,
            category_id: category,
            user_id: user_id,
            subcategory_id: subcategory,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function (data, textStatus, xhr) {
            $(".transaction_list").html(data.html);
        }
    });

});