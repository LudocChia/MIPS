document.addEventListener("DOMContentLoaded", function () {
    const burgerMenu = document.getElementById("burger-menu");
    const navMenu = document.getElementById("nav-menu");
    const searchContainer = document.getElementById("search-container");

    burgerMenu.addEventListener("click", function () {
        navMenu.classList.toggle("active");
        searchContainer.classList.toggle("active");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const menuIcon = document.getElementById("menuIcon");
    const nav = document.getElementById("nav");

    menuIcon.addEventListener("click", function () {
        console.log('menuIcon clicked');
        nav.classList.toggle("navactive");
        console.log('nav class list:', nav.classList);
    });

    // Close the navigation menu if clicked outside
    document.addEventListener("click", function (event) {
        if (!nav.contains(event.target) && !menuIcon.contains(event.target)) {
            nav.classList.remove("navactive");
        }
    });
});