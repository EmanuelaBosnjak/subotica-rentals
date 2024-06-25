$(".users-list>.item>.btn-bar>a[id$='active']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let modUserId = $(`#${id}`).data('mod-user-id');
        if (confirm('Are you sure you want to unblock this account?')) {
            console.log('gonna unblock ' + modUserId);
            let formData = {
                userId: userId,
                modUserId: modUserId,
                task: 'unblock',
            };
            $.ajax({
                type: "POST",
                url: "admin_users.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function (response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Account un-blocked successfully!');
                    window.location.reload();
                } else {
                    alert(`Unknown error occured, please try again after some time. ${response.message}`);
                }
            });
        }
    }
});

$(".users-list>.item>.btn-bar>a[id$='block']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let modUserId = $(`#${id}`).data('mod-user-id');
        if (confirm('Are you sure you want to block this account?')) {
            console.log('gonna block ' + modUserId);
            let formData = {
                userId: userId,
                modUserId: modUserId,
                task: 'block',
            };
            $.ajax({
                type: "POST",
                url: "admin_users.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function (response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Account blocked successfully!');
                    window.location.reload();
                } else {
                    alert(`Unknown error occurred, please try again after some time. ${response.message}`);
                }
            });
        }
    }
});