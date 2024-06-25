let prev = document.getElementById('prev');
let next = document.getElementById('next');

var allItems = document.querySelectorAll('.carousel .item');
var activeItem = document.querySelector('.carousel .active');

let activeIndex = -1;
allItems.forEach((element, key, parent) => {
    if (activeItem == element) activeIndex = key;
});
if (activeIndex == -1) {
    activeIndex = 0;
    activeItem = allItems.item(activeIndex);
    activeItem.classList.add('active');
}

console.log(`${activeIndex} / ${allItems.length}`);

next.addEventListener('click', (e) => {
    activeItem.classList.remove('active');
    activeIndex = (activeIndex + 1) % allItems.length;
    activeItem = allItems.item(activeIndex);
    activeItem.classList.add('active');
});

prev.addEventListener('click', (e) => {
    activeItem.classList.remove('active');
    activeIndex = (activeIndex - 1 >= 0) ? (activeIndex - 1) : (allItems.length - 1);
    activeItem = allItems.item(activeIndex);
    activeItem.classList.add('active');
});
