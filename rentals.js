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

var filtersOpen = false;

$('#expand-filters-btn').on('click', function (e) {
    e.preventDefault();
    if (filtersOpen) {
        filtersOpen = false;
        $('.filters').css('height', '0');
        $('.filters').css('opacity', '0');
    } else {
        filtersOpen = true;
        $('.filters').css('height', '430px');
        $('.filters').css('opacity', '1');
    }
});

var filtersApplied = true;

function clearFilters() {
    $('#location-id').val("");
    $('#type-id').val("");
    $('#max-price').val("");
    $('#max-period').val("");
    $('#min-capacity').val("");
    $('#parking').prop("checked", false);
    $('#pets').prop("checked", false);
    $('#flexible').prop("checked", false);
    $('#search-term').val("");
    for (let i = 0; i < ads.length; i++) {
        ads[i].element.css('display', 'flex');
    }
}

$('#clear-filters-btn').on('click', function (e) {
    e.preventDefault();
    filtersOpen = false;
    filtersApplied = false;
    $('.filters').css('height', '0');
    $('.filters').css('opacity', '0');
    clearFilters();
});

$('#apply-filters-btn').on('click', function (e) {
    e.preventDefault();
    filtersOpen = false;
    filtersApplied = true;
    $('.filters').css('height', '0');
    $('.filters').css('opacity', '0');

    let filters = {
        locationId: $('#location-id').val(),
        typeId: $('#type-id').val(),
        maxPrice: parseInt($('#max-price').val()),
        maxPeriod: parseInt($('#max-period').val()),
        minCapacity: parseInt($('#min-capacity').val()),
        hasParking: $('#parking').is(":checked"),
        hasPets: $('#pets').is(":checked"),
        hasFlexible: $('#flexible').is(":checked"),
    };
    console.log(filters);
    for (let i = 0; i < ads.length; i++) {
        let ad = ads[i];
        let isHidden = (filters.locationId != '' && !ad.matchLocationId(filters.locationId)) ||
            (filters.typeId != '' && !ad.matchTypeId(filters.typeId)) ||
            (!isNaN(filters.maxPrice) && !ad.checkPrice(filters.maxPrice)) ||
            (!isNaN(filters.maxPeriod) && !ad.checkPeriod(filters.maxPeriod)) ||
            (!isNaN(filters.minCapacity) && !ad.checkCapacity(filters.minCapacity)) ||
            (filters.hasParking && !ad.hasParking()) ||
            (filters.hasPets && !ad.hasPets()) ||
            (filters.hasFlexible && !ad.hasFlexible());
        if (isHidden) ads[i].element.css('display', 'none');
        else ads[i].element.css('display', 'flex');
    }
});

function search() {
    let term = $('#search-term').val().toLowerCase();
    if (term == '') {
        clearFilters();
    }
    for (let i = 0; i < ads.length; i++) {
        let ad = ads[i];
        let isHidden = !(ad.name.toLowerCase().includes(term) ||
            ad.location.toLowerCase().includes(term) ||
            ad.type.toLowerCase().includes(term) ||
            ad.addr.toLowerCase().includes(term));
        if (isHidden) ads[i].element.css('display', 'none');
        else ads[i].element.css('display', 'flex');
    }
}

$('#search-term').on('search', function (e) {
    search();
});

$('#search-btn').on('click', function (e) {
    search();
});