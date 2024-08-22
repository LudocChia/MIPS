document.addEventListener('DOMContentLoaded', function () {
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelector("#menu-btn");
    const closeBtn = document.querySelector("#close-btn");
    const userBtn = document.querySelector("#user-btn");
    const profileMenu = document.querySelector(".profile-menu");
    const ConfirmDialog = document.querySelector('#confirm-dialog');
    let currentForm = null;
    // const themeToggler = document.querySelector(".theme-toggler");

    // Show Sidebar
    if (menuBtn && sideMenu) {
        menuBtn.addEventListener("click", () => {
            sideMenu.style.display = "block";
        });
    }

    // Close Sidebar
    if (closeBtn && sideMenu) {
        closeBtn.addEventListener("click", () => {
            sideMenu.style.display = "none";
        });
    }

    // Show Profile Menu
    if (userBtn && profileMenu) {
        userBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            if (!profileMenu.contains(event.target) && !userBtn.contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        });

        window.addEventListener('resize', function () {
            if (profileMenu.classList.contains('active')) {
                profileMenu.classList.remove('active');
            }
        });
    }
    });