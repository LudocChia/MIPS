const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const userBtn = document.querySelector("#user-btn");
const profileMenu = document.querySelector(".profile-menu");
const dialog = document.querySelector('dialog');

// const themeToggler = document.querySelector(".theme-toggler");

// Show Sidebar
menuBtn.addEventListener("click", () => {
    sideMenu.style.display = "block";
});

// Close Sidebar
closeBtn.addEventListener("click", () => {
    sideMenu.style.display = "none";
});

// Show Profile Menu
userBtn.addEventListener('click', function (event) {
    event.stopPropagation(); // 阻止事件冒泡
    profileMenu.classList.toggle('active');
});

document.addEventListener('click', function (event) {
    if (!profileMenu.contains(event.target) && !userBtn.contains(event.target)) {
        profileMenu.classList.remove('active');
    }
});

window.addEventListener('resize', function () {
    if (profileMenu.classList.contains('active')) {
    }
});

document.querySelector("#open-popup").addEventListener("click", function () {
    dialog.showModal();
});
dialog.querySelector(".close-btn").addEventListener("click", function () {
    dialog.close();
});

// Change Theme
// themeToggler.addEventListener("click", () => {
//     document.body.classList.toggle('dark-theme-variables');

//     themeToggler.querySelector('i:nth-child(1)').classList.toggle('active');
//     themeToggler.querySelector('i:nth-child(2)').classList.toggle('active');
// })