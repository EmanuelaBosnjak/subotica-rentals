var root = document.documentElement;
var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
var vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);

let regexp = /android|iphone|kindle|ipad/i;
const isMobileDevice = regexp.test(navigator.userAgent);

var navbar = document.getElementById('navbar');
var isNavbarExpanded = false;
var userMenuVisible = false;
let expandButton = document.getElementById('navbar-expand-button');
expandButton.addEventListener('pointerup', (e) => {
    if (isNavbarExpanded) {
        collapseNavbar();
    } else {
        expandNavbar();
    }
});
if (!isMobileDevice) {
    navbar.addEventListener('mouseenter', (e) => expandNavbar());
    navbar.addEventListener('mouseleave', (e) => collapseNavbar());
    document.getElementById('user-nav').addEventListener('mouseenter', (e) => setUserMenuVisible(true));
    document.getElementById('user-nav').addEventListener('mouseleave', (e) => setUserMenuVisible(false));
    document.querySelector('.user-menu').addEventListener('mouseenter', (e) => setUserMenuVisible(true));
    document.querySelector('.user-menu').addEventListener('mouseleave', (e) => setUserMenuVisible(false));
} else {
    document.getElementById('user-nav').addEventListener('click', (e) => {
        if (userMenuVisible) setUserMenuVisible(false);
        else setUserMenuVisible(true);
    });
}
document.querySelector('.user-menu').addEventListener('click', (e) => setUserMenuVisible(false));

function expandNavbar() {
    isNavbarExpanded = true;
    root.style.setProperty('--navbar-width', '140px');
    root.style.setProperty('--nav-label-width', 'fit-content');
    setTimeout(() => {
        root.style.setProperty('--nav-label-opactiy', '1');
    }, 270);
    expandButton.innerHTML = "<i class='bi bi-chevron-left'></i> <span class='label'>Collapse</span>";
}

function collapseNavbar() {
    isNavbarExpanded = false;
    root.style.setProperty('--nav-label-opactiy', '0');
    setTimeout(() => {
        root.style.setProperty('--navbar-width', '55px');
        root.style.setProperty('--nav-label-width', '0px');
    }, 170);
    expandButton.innerHTML = "<i class='bi bi-chevron-right'></i> <span class='label'>Collapse</span>";
    setUserMenuVisible(false);
}

function setUserMenuVisible(isVisible) {
    userMenuVisible = isVisible;
    root.style.setProperty('--user-menu-opacity', isVisible ? '1' : '0');
    root.style.setProperty('--user-menu-events', isVisible ? 'all' : 'none');
}

$('#logout-btn').on('click', (e) => {
    window.location.replace('logout.php');
});

if (typeof loginTime === 'undefined' || loginTime === null) {
    console.log('loginTime not defined');
    $('#post-ad-btn').click((e) => {
        root.style.setProperty('--modal-blur', '5px');
        root.style.setProperty('--user-popup-opactiy', '1');
        root.style.setProperty('--user-popup-events', 'unset');
    });
}

function showLoginRegPopup() {
    root.style.setProperty('--modal-blur', '5px');
    root.style.setProperty('--user-popup-opactiy', '1');
    root.style.setProperty('--user-popup-events', 'unset');
}

$('#admin-users-btn').on('click', function (e) {
    window.location.href = 'admin_users.php';
});

$('#admin-adverts-btn').on('click', function (e) {
    window.location.href = 'admin_adverts.php';
});

$('#admin-all-adverts-btn').on('click', function (e) {
    window.location.href = 'admin_all_adverts.php';
});

$('#my-favs-btn').on('click', function (e) {
    window.location.href = 'my_favorites.php';
});

$('#my-props-btn').on('click', function (e) {
    window.location.href = 'my_properties.php';
});

$('#my-ads-btn').on('click', function (e) {
    window.location.href = 'my_advertisements.php';
});

$('#user-settings-btn').on('click', function (e) {
    window.location.href = 'user_settings.php';
});