document.addEventListener('DOMContentLoaded', function () {
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelector("#menu-btn");
    const closeBtn = document.querySelector("#close-btn");
    // const cancelBtn = document.querySelector(".cancle");
    const userBtn = document.querySelector("#user-btn");
    const profileMenu = document.querySelector(".profile-menu");
    // const dialog = document.querySelector('dialog');
    // const themeToggler = document.querySelector(".theme-toggler");

    // Show Sidebar
    menuBtn.addEventListener("click", () => {
        sideMenu.style.display = "block";
    });

    // Close Sidebar
    closeBtn.addEventListener("click", () => {
        sideMenu.style.display = "none";
    });

    // Show Profile Menu
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

    // sidebar
    const activeListItem = document.querySelector('.sidebar ul ul li a.active');
    if (activeListItem) {
        const parentUl = activeListItem.closest('ul');
        parentUl.style.display = 'block';
        const parentA = parentUl.previousElementSibling;
        parentA.querySelector('i.bi.bi-chevron-down').classList.add('rotate');
    }

    document.querySelectorAll('.bookshop-btn, .user-btn').forEach(button => {
        button.addEventListener('click', function () {
            const sublist = this.nextElementSibling;
            const icon = this.querySelector('i.bi.bi-chevron-down');
            if (sublist.style.display === 'block') {
                sublist.style.display = 'none';
                icon.classList.remove('rotate');
            } else {
                sublist.style.display = 'block';
                icon.classList.add('rotate');
            }
        });
    });

    // dialog modal
    document.querySelector("#open-popup").addEventListener("click", function () {
        document.getElementById('add-edit-data').showModal();
    });

    document.querySelector('#add-edit-data .cancel').addEventListener('click', function () {
        const dialog = document.getElementById('add-edit-data');
        dialog.close();
        dialog.querySelector('form').reset();
    });

    // Handle delete confirmation dialog
    const deleteConfirmDialog = document.getElementById('delete-confirm-dialog');
    let deleteForm = null;

    window.showDeleteConfirmDialog = function (event) {
        event.preventDefault();
        deleteForm = event.target;
        deleteConfirmDialog.showModal();
    }

    deleteConfirmDialog.addEventListener('close', function () {
        if (deleteConfirmDialog.returnValue === 'confirm') {
            deleteForm.submit();
        }
    });
});