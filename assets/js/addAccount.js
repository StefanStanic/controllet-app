require('../css/addAccount.css');

$('#addAccount').on('hidden.bs.modal', function () {
    location.reload();
});

$("#addAccountForm").on('submit', function (e) {
    e.preventDefault();

    //gather all the data
    var user_id = $("#user_id").val();
    var account_name = $("#accountNameAdd").val();
    var account_type = $("#accountType").val();
    var account_currency = $("#accountCurrency").val();
    var account_balance = $("#balance").val();

    $.ajax({
        type:"POST",
        url: "/addAccount",
        dataType: "json",
        data: {
            user_id : user_id,
            account_name: account_name,
            account_type: account_type,
            account_currency: account_currency,
            account_balance: account_balance,
        },
        success: function (data, textStatus, xhr) {
            if(xhr.status === 200 && data.status === 1) {
                toastr.success('Successfully added your account');

                //reset form
                setTimeout(function() {
                    $("#accountName").val('');
                    $("#accountType").val(1);
                    $("#accountCurrency").val(1);
                    $("#balance").val('');
                }, 2000);
            }
            else{
                toastr.error('Error adding your account. Try Again later')
            }
        }
    });
});