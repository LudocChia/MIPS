document.addEventListener('DOMContentLoaded', function () {
    // Common Variables
    const parentId = document.querySelector('#user-id') ? document.querySelector('#user-id').value : null;
    const dialog = document.getElementById('add-edit-data');
    const form = dialog ? dialog.querySelector('form') : null;
    let data = [];

    // Common Functions
    function fetchData(url, method, body) {
        return fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(body)
        }).then(response => response.json());
    }

    function handleError(error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    }

    // Cart Functions
    function getCartItems() {
        return fetchData('/mips/ajax.php?action=get_cart_items', 'POST', { parent_id: parentId })
            .then(cartItems => {
                if (cartItems.error) {
                    handleError(cartItems.error);
                    throw new Error(cartItems.error);
                } else {
                    return cartItems;
                }
            });
    }

    function updateCartItem(cartItemId, quantity) {
        fetchData('/mips/ajax.php?action=update_cart_item', 'POST', {
            cart_item_id: cartItemId,
            quantity: quantity
        }).then(result => {
            if (result.error) {
                handleError(result.error);
            } else {
                initCart();
            }
        }).catch(handleError);
    }

    function deleteCartItem(cartItemId, index) {
        fetchData('/mips/ajax.php?action=delete_cart_item', 'POST', {
            cart_item_id: cartItemId
        }).then(result => {
            if (result.success) {
                data.splice(index, 1);
                initCart();
            } else {
                handleError(result.error);
            }
        }).catch(handleError);
    }

    function deleteSelectedItems(cartItemIds) {
        fetchData('/mips/ajax.php?action=delete_selected', 'POST', {
            cart_item_ids: cartItemIds.join(',')
        }).then(result => {
            if (result.success) {
                data = data.filter(item => !cartItemIds.includes(item.cart_item_id));
                initCart();
                location.reload();
            } else {
                handleError(result.error);
            }
        }).catch(handleError);
    }

    function initCart() {
        const tbody = document.querySelector('#order-items');
        const totalPrice = document.querySelector('.total-price');
        const totalCount = document.querySelector('#totalCount');

        if (!tbody) return;

        let strHtml = '';
        let count = 0;
        let num = 0;

        data.forEach(function (item, index) {
            const totalPriceItem = (item.product_price * item.product_quantity).toFixed(2);
            const isDeleted = item.status === 1;

            let childrenHtml = '';
            if (item.children) {
                childrenHtml = item.children.split(',').map(child => {
                    const [id, name] = child.split(':');
                    return `
                        <div class="child-checkbox">
                            <input type="checkbox" id="child-${item.cart_item_id}-${id}" name="child[]" value="${id}" data-cart-item-id="${item.cart_item_id}">
                            <label for="child-${item.cart_item_id}-${id}">${name}</label>
                        </div>`;
                }).join('');
            }

            strHtml += `<tr>
                            <td>${!isDeleted ? `<input type="checkbox" class="ckh" id="ckh-${index}" ${item.state ? "checked" : ""}/>` : ''}</td>
                            <td class="product-image ${isDeleted ? 'deleted-product-image' : ''}">
                                <div class="image-container">
                                    <img src="/mips/uploads/product/${item.image_url}" alt="" />
                                    ${isDeleted ? '<div class="discontinued-text"><div>This product has</div><div>been discontinued</div></div>' : ''}
                                </div>
                            </td>
                            <td class="product-name">${item.product_name}</td>
                            <td class="product-size">${item.product_size || '-'}</td>
                            <td class="product-quantity">
                                <button class="add" id="add-${index}">+</button>
                                <input type="text" style="width:40px" value="${item.product_quantity}" data-cart-item-id="${item.cart_item_id}"/>
                                <button class="reduce" id="reduce-${index}">-</button>
                            </td>
                            <td class="product-total">RM ${totalPriceItem}</td>
                            <td class="child-selection">${childrenHtml}</td>
                            <td class="product-action">
                                <a href="/mips/item.php?pid=${item.product_id}"><button class="details" data-cart-item-id="${item.cart_item_id}"><i class="bi bi-info-circle-fill"></i></button></a>
                                <button class="delete" data-cart-item-id="${item.cart_item_id}"><i class="bi bi-trash3-fill"></i></button>
                            </td>
                        </tr>`;
            if (item.state) {
                count++;
                num += item.product_price * item.product_quantity;
            }
        });

        tbody.innerHTML = strHtml;
        totalCount.textContent = count;
        totalPrice.textContent = num.toFixed(2);
        initializeCartEventListeners();

        document.querySelectorAll('.child-checkbox input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const cartItemId = this.dataset.cartItemId;
                const childId = this.value;
                const isChecked = this.checked;

                const item = data.find(item => item.cart_item_id === cartItemId);
                if (item) {
                    if (!item.selectedChildren) {
                        item.selectedChildren = [];
                    }
                    if (isChecked) {
                        item.selectedChildren.push(childId);
                    } else {
                        item.selectedChildren = item.selectedChildren.filter(id => id !== childId);
                    }
                }
            });
        });
    }

    function initializeCartEventListeners() {
        const all = document.querySelector('#all');
        const selectAll = document.querySelector('#selectAll');
        const delall = document.querySelector('.delete-selected');

        document.querySelectorAll('.add').forEach(button => {
            button.addEventListener('click', function () {
                const index = this.id.split('-')[1];
                data[index].product_quantity++;
                updateCartItem(data[index].cart_item_id, data[index].product_quantity);
            });
        });

        document.querySelectorAll('.reduce').forEach(button => {
            button.addEventListener('click', function () {
                const index = this.id.split('-')[1];
                if (data[index].product_quantity > 1) {
                    data[index].product_quantity--;
                    updateCartItem(data[index].cart_item_id, data[index].product_quantity);
                }
            });
        });

        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function () {
                const cartItemId = this.dataset.cartItemId;
                const index = data.findIndex(item => item.cart_item_id === cartItemId);
                deleteCartItem(cartItemId, index);
            });
        });

        delall.addEventListener('click', function () {
            const selectedIds = data.filter(item => item.state).map(item => item.cart_item_id);
            if (selectedIds.length > 0) {
                deleteSelectedItems(selectedIds);
            }
        });

        all.addEventListener('change', function () {
            const isChecked = this.checked;
            data.forEach((item, index) => {
                if (!item.status) {
                    item.state = isChecked;
                    document.querySelector(`#ckh-${index}`).checked = isChecked;
                }
            });
            updateSelectionDisplay();
        });

        selectAll.addEventListener('change', function () {
            const isChecked = this.checked;
            data.forEach((item, index) => {
                if (!item.status) {
                    item.state = isChecked;
                    document.querySelector(`#ckh-${index}`).checked = isChecked;
                }
            });
            updateSelectionDisplay();
        });

        document.querySelectorAll('.ckh').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const index = this.id.split('-')[1];
                data[index].state = this.checked;
                updateSelectionDisplay();
            });
        });

        if (document.querySelector('.pay')) {
            document.querySelector('.pay').addEventListener('click', function () {
                const selectedItems = data.filter(item => item.state);

                if (selectedItems.length === 0) {
                    alert('Please select at least one item to proceed to checkout.');
                    return;
                }

                form.reset();

                const productIds = selectedItems.map(item => item.product_id).join(',');
                const sizeIds = selectedItems.map(item => item.product_size_id).join(',');
                const totalPrice = selectedItems.reduce((sum, item) => sum + (item.product_price * item.product_quantity), 0).toFixed(2);

                form.querySelector('#product-id').value = productIds;
                form.querySelector('#size-id').value = sizeIds;
                form.querySelector('#product-price').value = totalPrice;

                const tableBody = form.querySelector('#checkout-items-body');
                tableBody.innerHTML = '';

                selectedItems.forEach((item) => {
                    const selectedChildren = item.selectedChildren || [];
                    const totalPriceItem = (item.product_price * item.product_quantity).toFixed(2);

                    if (selectedChildren.length > 0) {
                        const childrenNames = item.children.split(',').map(child => {
                            const [id, name] = child.split(':');
                            if (selectedChildren.includes(id)) {
                                return `${name}`;
                            }
                            return '';
                        }).filter(name => name).join(', ');

                        if (childrenNames) {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.product_size || '-'}</td>
                                    <td>${childrenNames}</td>
                                    <td>MYR ${totalPriceItem}</td>
                                </tr>`;
                        }
                    }
                });
                document.querySelector('#total-price-display').value = totalPrice;

                dialog.showModal();
            });
        }
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            handlePurchase();
        });
    }


    function handlePurchase() {
        const selectedItems = data.filter(item => item.state);

        const productIds = selectedItems.map(item => item.product_id).join(',');
        const sizeIds = selectedItems.map(item => item.product_size_id).join(',');
        const quantities = selectedItems.map(item => item.product_quantity).join(',');
        const totalPriceItems = selectedItems.map(item => item.product_price * item.product_quantity).join(',');
        const childrenIds = selectedItems.map(item => item.selectedChildren.join(',')).join(';');
        const totalPrice = document.querySelector('#total-price-display').value;

        const formData = new FormData(form);
        formData.append('product_id', productIds);
        formData.append('size_id', sizeIds);
        formData.append('total_item_quantities', quantities);
        formData.append('total_price_items', totalPriceItems);
        formData.append('children', childrenIds);
        formData.append('total_price', totalPrice);

        fetch('/mips/ajax.php?action=purchase', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data = [];
                    alert('Purchase successful!');
                    document.querySelector('#add-edit-data').close();
                    initCart();
                    location.reload();
                } else {
                    alert('Failed to complete purchase: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error completing purchase::', error);
                alert('An error occurred. Please try again later.');
            });
    }

    function updateSelectionDisplay() {
        const selectedItems = data.filter(item => item.state);
        const totalSelected = selectedItems.reduce((sum, item) => sum + item.product_quantity, 0);
        const totalAmount = selectedItems.reduce((sum, item) => sum + (item.product_price * item.product_quantity), 0).toFixed(2);

        document.querySelector('#totalCount').textContent = totalSelected;
        document.querySelector('.total-price').textContent = totalAmount;

        if (form && document.querySelector('#total-price-display')) {
            document.querySelector('#total-price-display').value = totalAmount;
        }

        const allSelected = data.every(item => item.state || item.status);
        document.querySelector('#all').checked = allSelected;
        document.querySelector('#selectAll').checked = allSelected;
    }


    if (document.querySelector('#order-items')) {
        getCartItems().then(cartItems => {
            if (cartItems.length > 0) {
                data = cartItems.map(item => ({
                    ...item,
                    state: false,
                    selectedChildren: []
                }));
                document.getElementById('cart-content').style.display = 'none';
                document.getElementById('cart-items').style.display = 'block';
                initCart();
            }
        }).catch(handleError);
    }

    if (document.querySelector('.slider')) {
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
            };

            prev.onclick = function () {
                active = active - 1 >= 0 ? active - 1 : lengthItems;
                reloadSlider();
            };

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
                });
            });

            window.onresize = function () {
                reloadSlider();
            };

            reloadSlider();
        }
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
                if (dialog) {
                    dialog.close();
                    if (dialog.id === 'add-edit-data') {
                        dialog.querySelector('form').reset();
                    }
                }
            });
        });
    }
});

