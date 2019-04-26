require('../css/transactions.css');

$(document).ready(function() {
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
    var transaction_amount = $("#transactionAmount").val();
    var transaction_note = $("#transactionNote").val();

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
            transactionAmount: transaction_amount,
            transactionNote: transaction_note
        },
        success: function (data, textStatus, xhr) {
            if(xhr.status === 200 && data.status === 1) {
                $("#errorAddTransaction").hide();
                $("#successAddTransaction").html(data.text).show();
            }
            else{
                $("#successAddTransaction").hide();
                $("#errorAddTransaction").html(data.text).show();
            }
        }
    });
});

$('#addTransaction').on('hidden.bs.modal', function () {
    location.reload();
});