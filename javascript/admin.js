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

    document.querySelectorAll('.bookshop-btn, .user-btn, .deactivate-btn').forEach(button => {
        if (button) {
            button.addEventListener('click', function () {
                const sublist = this.nextElementSibling;
                const icon = this.querySelector('i.bi.bi-chevron-down');
                if (sublist && icon) {
                    if (sublist.style.display === 'block') {
                        sublist.style.display = 'none';
                        icon.classList.remove('rotate');
                    } else {
                        sublist.style.display = 'block';
                        icon.classList.add('rotate');
                    }
                }
            });
        }
    });

    const openPopupBtn = document.querySelector("#open-popup");
    if (openPopupBtn) {
        openPopupBtn.addEventListener("click", function () {
            const addEditDataDialog = document.getElementById('add-edit-data');
            if (addEditDataDialog) {
                addEditDataDialog.showModal();
            }
        });
    }

    window.showRecoverConfirmDialog = function (event) {
        event.preventDefault();
        const recoverForm = event.target;
        const recoverConfirmDialog = document.querySelector('#recover-confirm-dialog');

        if (recoverForm && recoverConfirmDialog) {
            document.querySelector('#add-edit-data h1').textContent = "Recover Bookshop Product";
            recoverConfirmDialog.showModal();
        }
    }

    const recoverConfirmDialog = document.querySelector('#recover-confirm-dialog');
    if (recoverConfirmDialog) {
        recoverConfirmDialog.addEventListener('close', function () {
            if (recoverConfirmDialog.returnValue === 'confirm' && recoverForm) {
                recoverForm.submit();
            }
        });
    }

    document.querySelectorAll('#add-edit-data .cancel, #delete-confirm-dialog .cancel, #detail-dialog .cancel').forEach(button => {
        if (button) {
            button.addEventListener('click', function () {
                const dialog = this.closest('dialog');
                if (dialog) {
                    dialog.close();
                    if (dialog.id === 'add-edit-data') {
                        const form = dialog.querySelector('form');
                        if (form) {
                            form.reset();
                        }
                    }
                }
            });
        }
    });

    window.showRecoverConfirmDialog = function (event) {
        event.preventDefault();
        currentForm = event.target;

        if (currentForm && ConfirmDialog) {
            document.querySelector('#confirm-dialog h1').textContent = "This data will be recovered!";
            document.querySelector('.confirm').textContent = "Recover";
            ConfirmDialog.showModal();
        }
    };

    if (ConfirmDialog) {
        ConfirmDialog.addEventListener('close', function () {
            if (ConfirmDialog.returnValue === 'confirm' && currentForm) {
                const parentId = currentForm.querySelector('input[name="parent_id"]').value;

                fetch(`/mahans/admin/ajax.php?action=recover_parent`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `parent_id=${parentId}`
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Parent successfully recovered.');
                            location.reload();
                        } else {
                            alert('Error recovering parent: ' + result.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while recovering the parent.');
                    });
            }
        });
    }
    window.showDeactivateConfirmDialog = function (event) {
        event.preventDefault();
        currentForm = event.target;

        if (currentForm && ConfirmDialog) {
            document.querySelector('#confirm-dialog h1').textContent = "This data will be deactivated!";
            document.querySelector('.confirm').textContent = "Deactivate";
            ConfirmDialog.showModal();
        }
    }

    if (ConfirmDialog) {
        ConfirmDialog.addEventListener('close', function () {
            if (ConfirmDialog.returnValue === 'confirm' && currentForm) {
                currentForm.submit();
            }
        });
    }
});
