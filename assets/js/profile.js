require('../css/profile.css');

//initialize date-pickers
var options={
    format: 'dd-mm-yyyy',
    todayHighlight: true,
    autoclose: true,
};
$("#date_of_birth").datepicker(options);


$("#updateUserInfo").on('submit', function (e) {
    e.preventDefault();

    var user_id = $("#user_id").val();
    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var date_of_birth = $("#date_of_birth").val();
    var company = $("#company").val();
    var city = $("#city").val();
    var country = $("#country").val();

    $.post('/updateProfile',
        {
            'user_id' : user_id,
            'first_name': first_name,
            'last_name': last_name ,
            'date_of_birth': date_of_birth,
            'company': company,
            'city': city,
            'country': country,
        }, function (data) {
            if(data.status === 1){
                toastr.success('Successfully updated your profile');
            }else{
                toastr.success('Error updating your profile');
            }
    }, 'json');
});