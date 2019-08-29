$(document).ready(function () {
    $("#contact-form").on('submit', function (e) {
        e.preventDefault();
        var name = $("#name").val();
        var email = $("#email").val();
        var subject = $("#subject").val();
        var message = $("#message").val();


        $.post('/sendContactFormData', {'name': name, 'email': email, 'subject': subject, 'message': message}, function (data) {
            if(data.status == '1'){
                toastr.success('Form successfully submitted');
            }
        }, 'json');
    });
});