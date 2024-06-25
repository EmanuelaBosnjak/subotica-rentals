$('.property-selector>.item').click(function (event) {
    let prop_id = event.target.id;
    let property_id = (prop_id.split('_'))[1];
    let propertyField = $('#property-id');
    if (propertyField.val() != -1) {
        $(`#prop_${propertyField.val()}`).removeClass('selected');
    }
    $(`#${prop_id}`).addClass('selected');
    propertyField.val(property_id);
    $('#property-id-error').text("");
});

var isValid = true;

$('#ad-form').submit(function (e) {
    e.preventDefault();
    let formData = {
        userId: $('#user-id').val(),
        propertyId: $('#property-id').val(),
        task: 'publish',
        rentalPeriod: $('#rental-period').val(),
        rentalPrice: $('#rental-price').val(),
        description: $('#description').val(),
    };
    console.log(formData);
    isValid = true;
    if (formData.propertyId == -1) {
        isValid = false;
        $('#property-id-error').text("Please select a property to advertise!");
    }
    if (formData.rentalPeriod < 0) {
        isValid = false;
        $('#rental-period-error').text("The period should be 0 or more.");
    }
    if (formData.rentalPrice <= 0) {
        isValid = false;
        $('#rental-price-error').text("The price should be positive.");
    }
    if (formData.description.length < 50) {
        isValid = false;
        $('#description-error').text("The description must be at least 50 chars long.");
    }
    if (isValid) {
        console.log("form is valid");
        $.ajax({
            type: "POST",
            url: "post_advert.php",
            data: { formData: formData },
            dataType: 'json',
        }).done(function (response) {
            console.log(response);
            if (response.status == 201) {
                alert('Advertisement saved successfully!');
                $returnUrl = $('#cancel-btn').attr('href');
                window.location.replace($returnUrl);
            } else if (response.status == 400) {
                alert('Invalid data entered, please check and try again!');
            } else {
                alert(`Unknown error occured, please try again after some time. ${response.message}`);
            }
        });
    }
});

$('#save-draft-btn').click(function (e) {
    e.preventDefault();
    let formData = {
        userId: $('#user-id').val(),
        propertyId: $('#property-id').val(),
        task: 'draft',
        rentalPeriod: $('#rental-period').val(),
        rentalPrice: $('#rental-price').val(),
        description: $('#description').val(),
    };
    console.log(formData);
    isValid = true;
    if (formData.propertyId == -1) {
        isValid = false;
        $('#property-id-error').text("Please select a property to advertise!");
    }
    if (formData.rentalPeriod < 0) {
        isValid = false;
        $('#rental-period-error').text("The period should be 0 or more.");
    }
    if (formData.rentalPrice <= 0) {
        isValid = false;
        $('#rental-price-error').text("The price should be positive.");
    }
    if (formData.description.length < 50) {
        isValid = false;
        $('#description-error').text("The description must be at least 50 chars long.");
    }
    if (isValid) {
        console.log("form is valid");
        $.ajax({
            type: "POST",
            url: "post_advert.php",
            data: { formData: formData },
            dataType: 'json',
        }).done(function (response) {
            console.log(response);
            if (response.status == 201) {
                alert('Advertisement draft saved successfully!');
                $returnUrl = $('#cancel-btn').attr('href');
                window.location.replace($returnUrl);
            } else if (response.status == 400) {
                alert('Invalid data entered, please check and try again!');
            } else {
                alert(`Unknown error occured, please try again after some time. ${response.message}`);
            }
        });
    }
});

$('#rental-period').on('input change', function(e) {
    if (!isValid && e.target.value < 0) {
        $('#rental-period-error').text("The period should be 0 or more.");
    } else {
        $('#rental-period-error').text("");
    }
});

$('#rental-price').on('input change', function(e) {
    if (!isValid && e.target.value <= 0) {
        $('#rental-price-error').text("The price should be positive.");
    } else {
        $('#rental-price-error').text("");
    }
});

$('#description').on('input change', function (event) {
    if (!isValid && event.target.value.length < 50) {
        $('#description-error').text("The description must be at least 50 chars long.");
    } else {
        $('#description-error').text("");
    }
});