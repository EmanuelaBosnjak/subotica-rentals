document.getElementById('login-reg-btn').addEventListener('click', (e) => {
    root.style.setProperty('--modal-blur', '5px');
    root.style.setProperty('--user-popup-opactiy', '1');
    root.style.setProperty('--user-popup-events', 'unset');
});

document.getElementById('close-btn').addEventListener('click', (e) => {
    root.style.setProperty('--modal-blur', '0px');
    root.style.setProperty('--user-popup-opactiy', '0');
    root.style.setProperty('--user-popup-events', 'none');
});

document.querySelector('.user-popup').addEventListener('click', (e) => {
    if (e.target == e.currentTarget) {
        root.style.setProperty('--modal-blur', '0px');
        root.style.setProperty('--user-popup-opactiy', '0');
        root.style.setProperty('--user-popup-events', 'none');
    }
});

$('#login-tab').click((e) => {
    $('#login-tab').addClass('active');
    $('#register-tab').removeClass('active');
    $('#login-form').css('display', 'flex');
    $('#register-form').css('display', 'none');
});

$('#register-tab').click((e) => {
    $('#register-tab').addClass('active');
    $('#login-tab').removeClass('active');
    $('#register-form').css('display', 'flex');
    $('#login-form').css('display', 'none');
});

$('#login-form').submit((e) => {
    e.preventDefault();
    loginData = {
        email: $('#login-email').val(),
        password: $('#login-password').val(),
    };
    console.log(loginData);
    let isValid = true;
    if (loginData.email.length == 0) {
        isValid = false;
        $('#login-email-error').text('Please enter an email address.');
    } else if (!validateEmail(loginData.email)) {
        isValid = false;
        $('#login-email-error').text('Please enter a valid email address.');
    } else $('#login-email-error').text('');
    if (loginData.password.length < 8) {
        isValid = false;
        $('#login-password-error').text('Please enter a valid password, must be atleast 8 chars long.');
    } else $('#login-password-error').text('');
    if (isValid) {
        console.log('login form is valid');
        $.ajax({
            type: "POST",
            url: "login_popup.php",
            data: { loginData: loginData },
            dataType: 'json',
        }).done(function (response) {
            console.log(response);
            if (response.status == 200) {
                console.log('LOGIN SUCCESS');
                window.location.reload();
            } else if (response.status == 404) {
                $('#login-result').text('Given email is not registered.');
                $('#login-result').css('display', 'unset');
                $('#login-result').css('color', '#88293d');
            } else if (response.status == 403) {
                if (response.message == 'NOT_VERIFIED') {
                    $('#login-result').text('Account not verified, please check your email for the verification link.');
                } else if (response.message == 'BLOCKED') {
                    $('#login-result').text('Your account has been blocked due to suspicious activity.');
                } else {
                    $('#login-result').text('Wrong password entered, please try again.');
                }
                $('#login-result').css('display', 'unset');
                $('#login-result').css('color', 'red');
            }
        });
    }
});

$('#forgot-pw-btn').on('click', function(e) {
    let email = prompt("Please enter your registered email:");
    if (email == null || email == '') return;
    if (!validateEmail(email)) {
        alert('Please enter a valid email :(');
        return;
    }
    let formData = {
        email: email,
        task: 'send',
    };
    $.ajax({
        type: "POST",
        url: "reset_password.php",
        data: { formData: formData },
        dataType: 'json',
    }).done(function (response) {
        console.log(response);
        if (response.status == 200) {
            alert('Password reset link sent! Please check your email.');            
        } else {
            alert('Email is not registered.');
        }
    });
});

$('#register-form').submit((e) => {
    e.preventDefault();
    registerData = {
        firstName: $('#first-name').val(),
        lastName: $('#last-name').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        cnfPassword: $('#cnf-password').val(),
    };
    console.log(registerData);
    let isValid = true;
    if (registerData.firstName.length == 0) {
        isValid = false;
        $('#first-name-error').text('Please enter your first name.');
    } else $('#first-name-error').text('');
    if (registerData.lastName.length == 0) {
        isValid = false;
        $('#last-name-error').text('Please enter your last name.');
    } else $('#last-name-error').text('');
    if (registerData.phone.toString().length != 9) {
        isValid = false;
        $('#phone-error').text('Please enter a valid phone number.');
    } else $('#phone-error').text('');
    if (registerData.email.length == 0) {
        isValid = false;
        $('#email-error').text('Please enter an email address.');
    } else if (!validateEmail(registerData.email)) {
        isValid = false;
        $('#email-error').text('Please enter a valid email address.');
    } else $('#email-error').text('');
    if (registerData.password.length < 8) {
        isValid = false;
        $('#password-error').text('Please enter a valid password, must be atleast 8 chars long.');
    } else $('#password-error').text('');
    if (registerData.cnfPassword != registerData.password) {
        isValid = false;
        $('#cnf-password-error').text('Passwords do not match.');
    } else $('#cnf-password-error').text('');
    if (isValid) {
        console.log('register form is valid');
        $.ajax({
            type: "POST",
            url: "login_popup.php",
            data: { registerData: registerData },
            dataType: 'json',
        }).done(function (response) {
            console.log(response);
            if (response.status == 201) {
                $('#register-result').text('Registration successful! Please check your email for a link to verify your account.');
                $('#register-result').css('display', 'unset');
                $('#register-result').css('color', 'green');
            } else {
                $('#register-result').text('Invalid data entered, please revise and try again!');
                $('#register-result').css('display', 'unset');
                $('#register-result').css('color', 'red');
            }
        });
    }
});