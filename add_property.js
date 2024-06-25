$('.location-selector>.item').click(function (event) {
    let loc_id = event.target.id;
    let location_id = (loc_id.split('_'))[1];
    let locationField = $('#location-id');
    if (locationField.val() != -1) {
        $(`#loc_${locationField.val()}`).removeClass('selected');
    }
    $(`#${loc_id}`).addClass('selected');
    locationField.val(location_id);
    $('#location-id-error').text("");
});

var uploadedPhotos = new Map();

$('#add-photo-btn').click(function (event) {
    $('#add-photo-input').click();
    event.preventDefault();
});

function addImagePreview(key, base64) {
    let item = $('<div>');
    item.addClass('item');
    let image = $('<img>');
    image.attr('src', base64);
    item.append(image);
    let removeBtn = $('<a>');
    removeBtn.attr('href', '#');
    removeBtn.attr('tooltip', 'Remove Photo');
    removeBtn.html('<i class="bi bi-x-square-fill"></i>');
    removeBtn.addClass('remove-btn');
    removeBtn.click((event) => {
        item.remove();
        uploadedPhotos.delete(key);
        if (uploadedPhotos.size == 0) {
            $('#no-photo-text').css('display', 'unset');
        }
        if (!isValid && uploadedPhotos.size < 3) {
            $('#photos-error').text("Please select at least 3 images.");
        }
    });
    item.append(removeBtn);
    $('.photo-uploader').append(item);
}

$('#add-photo-input').on('input', function (event) {
    if (event.target.files.length > 0) {
        let fileCount = uploadedPhotos.size;
        for (let i = 0; i < event.target.files.length; i++) {
            let file = event.target.files[i];
            if (fileCount >= 10) {
                alert('Sorry, you can only upload 10 or less images. Please remove a previously added image to proceed.');
                return;
            }
            if (uploadedPhotos.size == 0) {
                $('#no-photo-text').css('display', 'none');
            }
            let reader = new FileReader();
            reader.onloadend = () => {
                let base64 = reader.result;
                let key = Date.now();
                uploadedPhotos.set(key, base64);
                addImagePreview(key, base64);
                if (uploadedPhotos.size >= 3) {
                    $('#photos-error').text("");
                }
            };
            reader.readAsDataURL(file);
            fileCount++;
        }
    }
});

var isValid = true;

$('#prop-form').submit(function (e) {
    e.preventDefault();
    let formData = {
        userId: $('#user-id').val(),
        propName: $('#prop-name').val(),
        propType: $('#prop-type').val(),
        locationId: $('#location-id').val(),
        streetAddr: $('#street-addr').val(),
        capacity: parseInt($('#capacity').val()),
        parking: $('#parking').is(":checked"),
        pets: $('#pets').is(":checked"),
        photos: Array.from(uploadedPhotos.values()),
        description: $('#description').val(),
    };
    console.log(formData);
    isValid = true;
    if (formData.propName.length == 0) {
        isValid = false;
        $('#prop-name-error').text("Please enter a valid name.");
    } else if (formData.propName.length < 5) {
        isValid = false;
        $('#prop-name-error').text("The name must be at least 5 chars long.");
    }
    if (formData.propType == "") {
        isValid = false;
        $('#prop-type-error').text("Please select a property type.");
    }
    if (formData.locationId == -1) {
        isValid = false;
        $('#location-id-error').text("Please select a location.");
    }
    if (formData.streetAddr.length == 0) {
        isValid = false;
        $('#street-addr-error').text("Please enter a street address.");
    } else if (formData.streetAddr.length < 20) {
        isValid = false;
        $('#street-addr-error').text("The address must be at least 20 chars long.");
    }
    if (formData.capacity < 1 || formData.capacity > 15) {
        isValid = false;
        $('#capacity-error').text("Please enter a capacity between 1 and 15.");
    }
    if (formData.photos.length < 3) {
        isValid = false;
        $('#photos-error').text("Please select at least 3 images.");
    }
    if (formData.description.length < 50) {
        isValid = false;
        $('#description-error').text("The description must be at least 50 chars long.");
    }
    if (isValid) {
        console.log('form is valid');
        $.ajax({
            type: "POST",
            url: "add_property.php",
            data: { formData: formData },
            dataType: 'json',
        }).done(function (response) {
            console.log(response);
            if (response.status == 201) {
                alert('Property saved successfully!');
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

$('#prop-name').on('input change', function (event) {
    if (!isValid && event.target.value.length < 5) {
        $('#prop-name-error').text("The name must be at least 5 chars long.");
    } else {
        $('#prop-name-error').text("");
    }
});

$('#prop-type').on('input change', function (event) {
    if (!isValid && event.target.value == '') {
        $('#prop-type-error').text("Please select a property type.");
    } else {
        $('#prop-type-error').text("");
    }
});

$('#street-addr').on('input change', function (event) {
    if (!isValid && event.target.value.length < 20) {
        $('#street-addr-error').text("The address must be at least 20 chars long.");
    } else {
        $('#street-addr-error').text("");
    }
});

$('#capacity').on('input change', function (event) {
    if (!isValid && (event.target.value < 1 || event.target.value > 15)) {
        $('#capacity-error').text("Please enter a capacity between 1 and 15.");
    } else {
        $('#capacity-error').text("");
    }
});

$('#description').on('input change', function (event) {
    if (!isValid && event.target.value.length < 50) {
        $('#description-error').text("The description must be at least 50 chars long.");
    } else {
        $('#description-error').text("");
    }
});