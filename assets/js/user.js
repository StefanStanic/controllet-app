$(".ban_user").on('click', function (e) {
    e.preventDefault();

    var user_id = $(this).data('id');

    var confirm_dialog = confirm("Are you sure you want to ban this user?");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/userStatusSwitch",
            data: {
                user_id: user_id,
                active: 0
            },
            success: function (data, textStatus, xhr) {
                location.reload();
            }
        });
    }
});

$(".unban_user").on('click', function (e) {
    e.preventDefault();

    var user_id = $(this).data('id');

    var confirm_dialog = confirm("Are you sure you want to unban this user?");

    //location to post to
    if(confirm_dialog === true){
        $.ajax({
            type:"POST",
            url: "/userStatusSwitch",
            data: {
                user_id: user_id,
                active: 1
            },
            success: function (data, textStatus, xhr) {
                location.reload();
            }
        });
    }
});