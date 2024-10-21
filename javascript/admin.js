document.addEventListener('DOMContentLoaded', function () {
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelectorAll(".menu-btn");
    const closeBtn = document.querySelector("#close-btn");
    // const userBtn = document.querySelector("#user-btn");
    // const profileMenu = document.querySelector(".profile-menu");
    const pageCount = parseInt(document.body.dataset.pageCount, 10);
    const currentPage = parseInt(document.body.dataset.currentPage, 10);
    const ExclamationConfirmDialog = document.querySelector('#exclamation-confirm-dialog');
    const CheckConfirmDialog = document.querySelector('#check-confirm-dialog');
    let currentForm = null;
    // const themeToggler = document.querySelector(".theme-toggler");

    // Show Sidebar
    if (menuBtn && sideMenu) {
        menuBtn.forEach(button => {
            button.addEventListener("click", () => {
                sideMenu.classList.toggle("active");
            });
        });
    }

    // Close Sidebar
    if (closeBtn && sideMenu) {
        closeBtn.addEventListener("click", () => {
            sideMenu.classList.remove("active");
        });
    }

    // Close Sidebar on Resize
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1200) {
            sideMenu.classList.remove("active");
        }
    });

    document.querySelectorAll('.bookshop-btn, .user-btn, .ebook-btn').forEach(button => {
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
    if (pageCount > 1) {
        const links = document.querySelectorAll('.page-numbers > a');
        if (links.length >= currentPage) {
            links[currentPage - 1].classList.add("active");
        }
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

        if (currentForm && CheckConfirmDialog) {
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#check-confirm-dialog h1').textContent = "This data will be recovered!";
            document.querySelector('.confirm').textContent = "Recover";
            CheckConfirmDialog.showModal();

            CheckConfirmDialog.addEventListener('close', function () {
                if (CheckConfirmDialog.returnValue === 'confirm' && currentForm) {
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
                                window.location.href = window.location.href;
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

        if (currentForm && ExclamationConfirmDialog) {
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#exclamation-confirm-dialog h1').textContent = "This data will be deactivated!";
            document.querySelector('.confirm').textContent = "Deactivate";
            ExclamationConfirmDialog.showModal();

            ExclamationConfirmDialog.addEventListener('close', function () {
                if (ExclamationConfirmDialog.returnValue === 'confirm' && currentForm) {
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
                                const currentPage = parseInt(new URLSearchParams(window.location.search).get('page-nr')) || 1;
                                const totalPages = result.total_pages;

                                console.log(`Current page: ${currentPage}, Total pages: ${totalPages}`);

                                let redirectPage = currentPage;

                                if (currentPage > totalPages) {
                                    redirectPage = totalPages > 0 ? totalPages : 1;
                                }

                                const url = new URL(window.location.href);
                                url.searchParams.set('page-nr', redirectPage);
                                window.location.href = url.toString();
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

        if (currentForm && ExclamationConfirmDialog) {
            const actionType = currentForm.querySelector('input[name="action"]').value;
            const idField = currentForm.querySelector('input[name*="_id"]').name;
            const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

            document.querySelector('#exclamation-confirm-dialog h1').textContent = "This data will be Deleted!";
            document.querySelector('.confirm').textContent = "Delete";
            ExclamationConfirmDialog.showModal();

            ExclamationConfirmDialog.addEventListener('close', function () {
                if (ExclamationConfirmDialog.returnValue === 'confirm' && currentForm) {
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
                                // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!如果 actionType 是 delete_event，则重定向到 adminMain.php - Kai Long
                                if (actionType === 'delete_event') {
                                    window.location.href = '/mips/admin/admin_meal/adminMain.php';
                                } else {
                                    window.location.href = window.location.href;

                                }
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
