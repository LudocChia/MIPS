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

// document.querySelector("#open-popup").addEventListener("click", function () {
//     document.getElementById('add-edit-data').showModal();
// });

if (document.getElementById('add-edit-data')) {
    document.querySelectorAll('#add-edit-data .cancel, #delete-confirm-dialog .cancel').forEach(button => {
        button.addEventListener('click', function () {
            const dialog = this.closest('dialog');
            if (dialog) {
                dialog.close();
                if (dialog.id === 'add-edit-data') {
                    dialog.querySelector('form').reset();
                }
            }
        });
    })
}