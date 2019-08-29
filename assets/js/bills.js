require('../css/bills.css');

$(document).ready(function() {
    initializeDatePickers();
    initializeSubCategory();
    initializeSubCategoryFilter();

    $('#bills_table').DataTable({
        sorting: false,
        searching: false,
        paging: true
    });

} );


$("#addBillForm").on('submit', function (e) {
    e.preventDefault();

    //gather all the data
    // var user_id = $("#user_id").val();
    var bill_name = $("#billName").val();
    var bill_account_type = $("#billAccountType").val();
    var bill_category = $("#billCategory").val();
    var bill_subcategory = $("#billSubcategory").val();
    var bill_amount = $("#billAmount").val();
    var bill_note = $("#billNote").val();
    var bill_duedate = $("#billDueDate").val();
    var recurring_bill = $('#recurring_bill').is(':checked')? 1 : 0;

    $.ajax({
        type:"POST",
        url: "/addBill",
        dataType: "json",
        data: {
            name: bill_name,
            amount: bill_amount,
            note: bill_note,
            date_due: bill_duedate,
            category: bill_category,
            subcategory: bill_subcategory,
            account: bill_account_type,
            recurring_bill: recurring_bill
        },
        success: function (data, textStatus, xhr) {
            if(xhr.status === 200 && data.status === 1) {
                toastr.success('Successfully added your bill');

                setTimeout(function() {
                    //reset fields
                    $("#billName").val('');
                    $("#billAccountType").val(1);
                    $("#billCategory").val(1);
                    $("#billSubcategory").val(1);
                    $("#billAmount").val('');
                    $("#billNote").val('');
                    $("#billDueDate").val('');
                }, 2000);
            }
            else{
                toastr.error('Error adding a bill. Try again later');
            }
        }
    });
});


$(".edit_bill").on('click', function (e) {
    e.preventDefault();

    var bill_id = $(this).data('id');

    //enable fields for edit
    $(".bill_name_"+bill_id).prop('disabled', false);
    $(".bill_account_"+bill_id).prop('disabled', false);
    $(".bill_category_"+bill_id).prop('disabled', false);
    $(".bill_subcategory_"+bill_id).prop('disabled', false);
    $(".bill_note_"+bill_id).prop('disabled', false);
    $(".bill_amount_"+bill_id).prop('disabled', false);
    $(".update_bill_"+bill_id).show();

    //add update event listener for transaction button
    $(".update_bill_"+bill_id).on('click', function (e) {
        e.preventDefault();
        var bill_name = $(".bill_name_"+bill_id).val();
        var bill_category = $(".bill_category_"+bill_id).val();
        var bill_subcategory = $(".bill_subcategory_"+bill_id).val();
        var bull_account = $(".bill_account_"+bill_id).val();
        var bill_note = $(".bill_note_"+bill_id).val();
        var bill_amount = $(".bill_amount_"+bill_id).val();


        var confirm_dialog = confirm("Are you sure you want to update this transaction?");

        //location to post to
        if(confirm_dialog === true){
            $.ajax({
                type:"POST",
                url: "/updateBill",
                data: {
                    id: bill_id,
                    name: bill_name,
                    category: bill_category,
                    subcategory: bill_subcategory,
                    account : bull_account,
                    note : bill_note,
                    amount : bill_amount
                },
                success: function (data, textStatus, xhr) {
                    if(xhr.status == 200)
                    {
                        toastr.success('Successfully updated your bill');

                        $(".update_bill_"+bill_id).hide();
                        $(".bill_name_"+bill_id).prop('disabled', true);
                        $(".bill_account_"+bill_id).prop('disabled', true);
                        $(".bill_category_"+bill_id).prop('disabled', true);
                        $(".bill_subcategory_"+bill_id).prop('disabled', true);
                        $(".bill_note_"+bill_id).prop('disabled', true);
                        $(".bill_amount_"+bill_id).prop('disabled', true);

                    }
                    else
                    {
                        toastr.error('All fields needs to be filled');
                    }
                }
            });
        }
    });

});


$(".delete_bill").on('click', function (e) {
    e.preventDefault();

    var bill_id = $(this).data('id');
    var bill_group_id = $(this).data('group_id');

    var confirm_dialog = confirm("Are you sure you want to update this bill?");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/deleteBill",
            data: {
                bill_id: bill_id,
                bill_group_id: bill_group_id
            },
            success: function (data, textStatus, xhr) {
                toastr.success('Successfully deleted your bill');

                //reset form
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    }
});



$('#addBill').on('hidden.bs.modal', function () {
    location.reload();
});


$("#billCategory").on('change', function () {
    var category_id = $("#billCategory").val();

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $("#billSubcategory").html(data.html);
        }
    });
});

$(".bill_category").on('change', function () {
    var category_id = $(this).val();
    var bill_id = $(this).data('id');

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $(".bill_subcategory_"+bill_id).html(data.html);
        }
    });
});

function initializeDatePickers() {

    $('#billDueDate').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true
    });

    $('#dueDateFromFilter').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true
    });

    $('#dueDateToFilter').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true
    });
}

$("#billCategoryFilter").on('change', function () {
    initializeSubCategoryFilter();
});

function initializeSubCategoryFilter() {
    var category_id = $("#billCategoryFilter").val();

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $("#billSubcategoryFilter").html(data.html);
        }
    });
}

function initializeSubCategory(){
    var category_id = $("#billCategory").val();

    $.ajax({
        type:"POST",
        url: "/subCategories",
        dataType: 'json',
        data: {
            category_id: category_id
        },
        success: function (data, textStatus, xhr) {
            $("#billSubcategory").html(data.html);
        }
    });
}

$("#billAccountTypeFilter, #billCategoryFilter, #billSubcategoryFilter, #dueDateFromFilter, #dueDateToFilter"). on('change', function () {

    var user_id = $("#user_id").val();
    var accountType = $("#billAccountTypeFilter").val();
    var category = $("#billCategoryFilter").val();
    var subcategory = $("#billSubcategoryFilter").val();
    var dateFrom = $("#dueDateFromFilter").val();
    var dateTo = $("#dueDateToFilter").val();

    $.ajax({
        type:"POST",
        url: "/filterBills",
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
            $(".bills_list").html(data.html);
        }
    });

});