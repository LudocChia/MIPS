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
});

// document.addEventListener('DOMContentLoaded', function () {
//     const userBtn = document.querySelector("#user-btn");
//     const profileMenu = document.querySelector(".profile-menu");

//     // Show Profile Menu
//     if (userBtn && profileMenu) {
//         userBtn.addEventListener('click', function (event) {
//             event.stopPropagation();
//             profileMenu.classList.toggle('active');
//         });

//         document.addEventListener('click', function (event) {
//             if (!profileMenu.contains(event.target) && !userBtn.contains(event.target)) {
//                 profileMenu.classList.remove('active');
//             }
//         });

//         window.addEventListener('resize', function () {
//             if (profileMenu.classList.contains('active')) {
//                 profileMenu.classList.remove('active');
//             }
//         });
//     }

//     // Sidebar active item logic
//     const activeListItem = document.querySelector('.sidebar ul ul li a.active');
//     if (activeListItem) {
//         const parentUl = activeListItem.closest('ul');
//         if (parentUl) {
//             parentUl.style.display = 'block';
//             const parentA = parentUl.previousElementSibling;
//             if (parentA) {
//                 const icon = parentA.querySelector('i.bi.bi-chevron-down');
//                 if (icon) {
//                     icon.classList.add('rotate');
//                 }
//             }
//         }
//     }

//     const menuIcon = document.getElementById("menuIcon");
//     const nav = document.getElementById("nav");

//     if (menuIcon && nav) {
//         menuIcon.addEventListener("click", function () {
//             console.log('menuIcon clicked');
//             nav.classList.toggle("navactive");
//             console.log('nav class list:', nav.classList);
//         });

//         document.addEventListener("click", function (event) {
//             if (!nav.contains(event.target) && !menuIcon.contains(event.target)) {
//                 nav.classList.remove("navactive");
//             }
//         });
//     }

//     const formAjax = document.getElementById('form-ajax');
//     if (formAjax) {
//         formAjax.addEventListener('keydown', function (e) {
//             if (e.key === 'Enter') {
//                 e.preventDefault();
//                 document.querySelector('.confirm').click();
//             }
//         });
//     }

//     // Popup Image
//     document.querySelectorAll('.product-image img').forEach(image => {
//         image.onclick = () => {
//             document.querySelector('.popup-image').style.display = 'block';
//             document.querySelector('.popup-image img').src = image.getAttribute('src');
//         }
//     });

//     document.querySelector('.popup-image span').onclick = () => {
//         document.querySelector('.popup-image').style.display = 'none';
//     }


//     const thumbnails = document.querySelectorAll('.thumbnail');
//     const mainImage = document.querySelector('.product-image img');

//     if (thumbnails.length > 0) {
//         thumbnails[0].classList.add('active');
//         const firstImageSrc = thumbnails[0].getAttribute('data-src');
//         mainImage.setAttribute('src', firstImageSrc);
//         mainImage.setAttribute('alt', firstImageSrc);
//     }

//     thumbnails.forEach(thumbnail => {
//         thumbnail.addEventListener('click', function () {
//             thumbnails.forEach(thumb => thumb.classList.remove('active'));

//             this.classList.add('active');

//             const newSrc = this.getAttribute('data-src');
//             mainImage.setAttribute('src', newSrc);
//             mainImage.setAttribute('alt', newSrc);
//         });
//     });
// });