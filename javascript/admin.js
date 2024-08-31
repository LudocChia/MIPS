document.addEventListener('DOMContentLoaded', function () {
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelectorAll(".menu-btn");
    const closeBtn = document.querySelector("#close-btn");
    const userBtn = document.querySelector("#user-btn");
    const profileMenu = document.querySelector(".profile-menu");
    const ConfirmDialog = document.querySelector('#confirm-dialog');
    let currentForm = null;
    // const themeToggler = document.querySelector(".theme-toggler");

    // Show Sidebar
    if (menuBtn && sideMenu) {
        menuBtn.forEach(button => {
            button.addEventListener("click", () => {
                sideMenu.classList.toggle("active"); // Toggle the 'active' class
            });
        });
    }

    // Close Sidebar
    if (closeBtn && sideMenu) {
        closeBtn.addEventListener("click", () => {
            sideMenu.classList.remove("active"); // Ensure the 'active' class is removed
        });
    }

    // Close Sidebar on Resize
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1200) {
            sideMenu.classList.remove("active"); // Ensure the sidebar stays hidden on large screens if closed
        }
    });

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

    // Pagination
    if (document.body.id) {
        let links = document.querySelectorAll('.page-numbers > a');
        let bodyId = parseInt(document.body.id) - 1;
        links[bodyId].classList.add("active");
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
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#confirm-dialog h1').textContent = "This data will be recovered!";
            document.querySelector('.confirm').textContent = "Recover";
            ConfirmDialog.showModal();

            ConfirmDialog.addEventListener('close', function () {
                if (ConfirmDialog.returnValue === 'confirm' && currentForm) {
                    fetch(`/mips/admin/ajax.php?action=${actionType}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `${idField}=${idValue}`
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                location.reload();
                            } else {
                                alert('Error recovering: ' + result.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while recovering.');
                        });
                }
            });
        }
    }

    window.showDeactivateConfirmDialog = function (event) {
        event.preventDefault();
        currentForm = event.target;

        if (currentForm && ConfirmDialog) {
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#confirm-dialog h1').textContent = "This data will be deactivated!";
            document.querySelector('.confirm').textContent = "Deactivate";
            ConfirmDialog.showModal();

            ConfirmDialog.addEventListener('close', function () {
                if (ConfirmDialog.returnValue === 'confirm' && currentForm) {
                    fetch(`/mips/admin/ajax.php?action=${actionType}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `${idField}=${idValue}`
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                location.reload();
                            } else {
                                alert('Error deactivating: ' + result.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deactivating.');
                        });
                }
            });
        }
    }

    window.showDeleteConfirmDialog = function (event) {
        event.preventDefault();
        currentForm = event.target;

        if (currentForm && ConfirmDialog) {
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#confirm-dialog h1').textContent = "This data will be Deleted!";
            document.querySelector('.confirm').textContent = "Delete";
            ConfirmDialog.showModal();

            ConfirmDialog.addEventListener('close', function () {
                if (ConfirmDialog.returnValue === 'confirm' && currentForm) {
                    fetch(`/mips/admin/ajax.php?action=${actionType}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `${idField}=${idValue}`
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                location.reload();
                            } else {
                                alert('Error deleting: ' + result.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting.');
                        });
                }
            });
        }
    }
});
