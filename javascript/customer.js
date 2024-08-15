if (document.getElementById('login-form')) {
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
