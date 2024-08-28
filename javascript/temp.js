let slider = document.querySelector('.slider .list');
let items = document.querySelectorAll('.slider .list .item');
let next = document.getElementById('next');
let prev = document.getElementById('prev');
let dots = document.querySelectorAll('.slider .dots li');
let isAutoSliding = false;

if (slider && items.length && next && prev && dots.length) {
    let lengthItems = items.length - 1;
    let active = 0;


    next.onclick = function () {
        active = active + 1 <= lengthItems ? active + 1 : 0;
        reloadSlider();
    }

    prev.onclick = function () {
        active = active - 1 >= 0 ? active - 1 : lengthItems;
        reloadSlider();
    }

    let refreshInterval = setInterval(() => {
        isAutoSliding = true;
        next.click();
    }, 3000);

    function reloadSlider() {
        slider.style.left = -items[active].offsetLeft + 'px';

        document.querySelector('.slider .dots li.active')?.classList.remove('active');
        dots[active].classList.add('active');

        clearInterval(refreshInterval);
        refreshInterval = setInterval(() => {
            isAutoSliding = true;
            next.click();
        }, 10000);

        setTimeout(() => { isAutoSliding = false; }, 500);
    }

    dots.forEach((li, key) => {
        li.addEventListener('click', () => {
            active = key;
            reloadSlider();
        })
    });

    window.onresize = function () {
        reloadSlider();
    };

    reloadSlider();
}

if (document.querySelector("#login-btn")) {
    document.querySelector("#login-btn").addEventListener("click", function () {
        scrollPosition = window.pageYOffset;
        const productId = new URLSearchParams(window.location.search).get('pid');

        document.body.style.overflowY = 'hidden';
        document.body.style.paddingRight = '15px';
        document.body.style.backgroundColor = 'white';

        const loginForm = document.getElementById('login-form');
        if (productId) {
            loginForm.querySelector('form').action += `?pid=${encodeURIComponent(productId)}`;
        }
        loginForm.showModal();
    });
}

if (document.getElementById('login-form')) {
    document.querySelector('#login-form .cancel').addEventListener('click', function () {
        const dialog = document.getElementById('login-form');
        dialog.close();
        dialog.querySelector('form').reset();

        document.body.style.overflowY = '';
        document.body.style.paddingRight = '';
        document.body.style.backgroundColor = '';

        window.scrollTo(0, scrollPosition);
    });
}

if (document.getElementById('add-edit-data')) {
    document.querySelectorAll('#add-edit-data .cancel, #delete-confirm-dialog .cancel').forEach(button => {
        button.addEventListener('click', function () {
            const dialog = this.closest('dialog');

        });
    })
}