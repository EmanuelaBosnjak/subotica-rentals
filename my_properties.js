$('.photo-reel>.prev-btn').click(function (e) {
    let subjects = e.target.parentNode.childNodes;
    let photos = [], activeIdx = -1;
    for (let i = 0; i < subjects.length; i++) {
        let obj = $(subjects[i]);
        if (obj.is('img')) {
            photos.push(obj);
            if (obj.hasClass('active')) {
                activeIdx = photos.length - 1;
            }
        }
    }
    photos[activeIdx].removeClass('active');
    activeIdx = (activeIdx - 1 >= 0) ? (activeIdx - 1) : (photos.length - 1);
    photos[activeIdx].addClass('active');
});

$('.photo-reel>.next-btn').click(function (e) {
    let subjects = e.target.parentNode.childNodes;
    let photos = [], activeIdx = -1;
    for (let i = 0; i < subjects.length; i++) {
        let obj = $(subjects[i]);
        if (obj.is('img')) {
            photos.push(obj);
            if (obj.hasClass('active')) {
                activeIdx = photos.length - 1;
            }
        }
    }
    photos[activeIdx].removeClass('active');
    activeIdx = (activeIdx + 1) % photos.length;
    photos[activeIdx].addClass('active');
});

$(".property-list>.item>.info>.btn-bar>a[id$='hist']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let propId = $(`#${id}`).data('prop-id');
        window.location.href = "rent_history.php?return-url=my_properties.php&property-id=" + propId;
    }
});

$(".property-list>.item>.info>.btn-bar>a[id$='edit']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let propId = $(`#${id}`).data('prop-id');
        window.location.href = "edit_property.php?return-url=my_properties.php&property-id=" + propId;
    }
});

$(".property-list>.item>.info>.btn-bar>a[id$='delete']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let propId = $(`#${id}`).data('prop-id');
        if (confirm('Are you sure you want to delete this property?')) {
            console.log('gonna delete ' + propId);
            let formData = {
                userId: userId,
                propId: propId, 
            };
            $.ajax({
                type: "POST",
                url: "delete_property.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Property deleted successfully!');
                    window.location.reload();
                } else {
                    alert(`Unknown error occured, please try again after some time. ${response.message}`);
                }
            });
        }
    }
});

$(".advert-list>.item>.info>.btn-bar>a[id$='view']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let adId = $(`#${id}`).data('ad-id');
        window.location.href = "view_advertisement.php?return-url=my_advertisements.php&ad-id=" + adId;
    }
});

$(".advert-list>.item>.info>.btn-bar>a[id$='edit']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let adId = $(`#${id}`).data('ad-id');
        window.location.href = "edit_advertisement.php?return-url=my_advertisements.php&ad-id=" + adId;
    }
});

$(".advert-list>.item>.info>.btn-bar>a[id$='delete']").click(function (e) {
    e.preventDefault();
    let id = e.target.id;
    if (id != '') {
        let adId = $(`#${id}`).data('ad-id');
        if (confirm('Are you sure you want to delete this advertisement?')) {
            console.log('gonna delete ' + adId);
            let formData = {
                userId: userId,
                adId: adId, 
            };
            $.ajax({
                type: "POST",
                url: "delete_advertisement.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Advertisement deleted successfully!');
                    window.location.reload();
                } else {
                    alert(`Unknown error occured, please try again after some time. ${response.message}`);
                }
            });
        }
    }
});