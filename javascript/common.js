document.addEventListener('DOMContentLoaded', () => {
    const userBtn = document.getElementById('user-btn');
    const profileMenu = document.querySelector('.profile-menu');

    if (userBtn && profileMenu) {
        const toggleProfileMenu = (event) => {
            event.stopPropagation();
            profileMenu.classList.toggle('active');
        };

        const hideProfileMenu = (event) => {
            if (!profileMenu.contains(event.target) && !userBtn.contains(event.target)) {
                profileMenu.classList.remove('active');
            }
        };

        userBtn.addEventListener('click', toggleProfileMenu);
        document.addEventListener('click', hideProfileMenu);
        window.addEventListener('resize', () => profileMenu.classList.remove('active'));
    }

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

    const menuIcon = document.getElementById('menuIcon');
    const nav = document.getElementById('nav');

    if (menuIcon && nav) {
        const toggleNav = (event) => {
            event.stopPropagation();
            nav.classList.toggle('navactive');
        };

        const hideNav = (event) => {
            if (!nav.contains(event.target) && !menuIcon.contains(event.target)) {
                nav.classList.remove('navactive');
            }
        };

        menuIcon.addEventListener('click', toggleNav);
        document.addEventListener('click', hideNav);
    }

    const formAjax = document.getElementById('form-ajax');
    if (formAjax) {
        const confirmButton = formAjax.querySelector('.confirm');
        formAjax.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (confirmButton) confirmButton.click();
            }
        });
    }

    const productImages = document.querySelectorAll('.product-image img');
    const popupImageContainer = document.querySelector('.popup-image');
    if (popupImageContainer) {
        const popupImage = popupImageContainer.querySelector('img');
        const popupCloseBtn = popupImageContainer.querySelector('span');

        productImages.forEach(image => {
            image.addEventListener('click', () => {
                popupImageContainer.style.display = 'block';
                popupImage.src = image.src;
            });
        });

        popupCloseBtn.addEventListener('click', () => {
            popupImageContainer.style.display = 'none';
        });
    }

    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.product-image img');

    if (thumbnails.length > 0 && mainImage) {
        const updateMainImage = (newSrc) => {
            mainImage.src = newSrc;
            mainImage.alt = newSrc;
        };

        const firstThumbnail = thumbnails[0];
        firstThumbnail.classList.add('active');
        updateMainImage(firstThumbnail.getAttribute('data-src'));

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                thumbnail.classList.add('active');
                const newSrc = thumbnail.getAttribute('data-src');
                updateMainImage(newSrc);
            });
        });
    }

    const backToTop = document.querySelector(".back-to-top");

    window.addEventListener("scroll", () => {
        if (window.pageYOffset > 650) {
            backToTop.classList.add("active");
        } else {
            backToTop.classList.remove("active");
        }
    });

    backToTop.addEventListener('click', function (event) {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

});