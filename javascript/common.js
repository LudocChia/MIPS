document.addEventListener('DOMContentLoaded', function () {
    const userBtn = document.querySelector("#user-btn");
    const profileMenu = document.querySelector(".profile-menu");

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

    // Sidebar active item logic
    const activeListItem = document.querySelector('.sidebar ul ul li a.active');
    if (activeListItem) {
        const parentUl = activeListItem.closest('ul');
        if (parentUl) {
            parentUl.style.display = 'block';
            const parentA = parentUl.previousElementSibling;
            if (parentA) {
                const icon = parentA.querySelector('i.bi.bi-chevron-down');
                if (icon) {
                    icon.classList.add('rotate');
                }
            }
        }
    }

    const menuIcon = document.getElementById("menuIcon");
    const nav = document.getElementById("nav");

    if (menuIcon && nav) {
        menuIcon.addEventListener("click", function () {
            console.log('menuIcon clicked');
            nav.classList.toggle("navactive");
            console.log('nav class list:', nav.classList);
        });

        document.addEventListener("click", function (event) {
            if (!nav.contains(event.target) && !menuIcon.contains(event.target)) {
                nav.classList.remove("navactive");
            }
        });
    }

    const formAjax = document.getElementById('form-ajax');
    if (formAjax) {
        formAjax.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.confirm').click();
            }
        });
    }
});