window.addEventListener('load', function () {
    const tbody = document.querySelector('#order-items');
    const all = document.querySelector('#all');
    const selectAll = document.querySelector('#selectAll');
    const delall = document.querySelector('.del-all');
    const totalPrice = document.querySelector('.total-price');
    const totalCount = document.querySelector('#totalCount');
    const parentId = document.querySelector('#user-id').value;
    const dialog = document.getElementById('add-edit-data');
    const form = dialog.querySelector('form');

    let data = [];

    function getCartItems() {
        return fetch('/mips/ajax.php?action=get_cart_items', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                parent_id: parentId
            })
        })
            .then(response => response.json())
            .then(cartItems => {
                if (cartItems.error) {
                    console.error(cartItems.error);
                    alert('Failed to load cart items.');
                    throw new Error(cartItems.error);
                } else {
                    return cartItems;
                }
            });
    }

    getCartItems().then(cartItems => {
        if (cartItems.length > 0) {
            data = cartItems.map(item => ({
                ...item,
                state: false,
                selectedChildren: []
            }));
            document.getElementById('cart-content').style.display = 'none';
            document.getElementById('cart-items').style.display = 'block';
            init();
        }
    }).catch(error => {
        console.error('Error loading cart items:', error);
    });

    function init() {
        let strHtml = '';
        let count = 0;
        let num = 0;

        data.forEach(function (item, index) {
            const totalPriceItem = (item.product_price * item.product_quantity).toFixed(2);
            const isDeleted = item.is_deleted === 1;

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
                                <button class="details" data-cart-item-id="${item.cart_item_id}"><i class="bi bi-info-circle-fill"></i></button>
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
        initializeEventListeners();
    }

    function initializeEventListeners() {
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
                if (!item.is_deleted) {
                    item.state = isChecked;
                    document.querySelector(`#ckh-${index}`).checked = isChecked;
                }
            });
            updateSelectionDisplay();
        });

        selectAll.addEventListener('change', function () {
            const isChecked = this.checked;
            data.forEach((item, index) => {
                if (!item.is_deleted) {
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

        document.querySelector('.pay').addEventListener('click', function () {
            const selectedItems = data.filter(item => item.state);

            if (selectedItems.length === 0) {
                alert('Please select at least one item to proceed to checkout.');
                return;
            }

            form.reset();

            const productIds = selectedItems.map(item => item.product_id).join(',');
            const sizeIds = selectedItems.map(item => item.size_id).join(',');
            const productPrices = selectedItems.map(item => item.product_price).join(',');

            form.querySelector('#product-id').value = productIds;
            form.querySelector('#size-id').value = sizeIds;
            form.querySelector('#product-price').value = productPrices;

            form.querySelector('#product-name-display').value = selectedItems.map(item => item.product_name).join(', ');
            form.querySelector('#selected-size-display').value = selectedItems.map(item => item.size_name || '-').join(', ');
            form.querySelector('#product-price-display').value = selectedItems.map(item => `RM ${item.product_price}`).join(', ');

            const childSelectionContainer = form.querySelector('.input-field');
            childSelectionContainer.innerHTML = '<h2>Select Child<sup>*</sup></h2>';

            selectedItems.forEach((item, index) => {
                if (item.children) {
                    const children = item.children.split(',').map(child => {
                        const [id, name] = child.split(':');
                        return `
                            <label>
                                <input type="checkbox" name="child[]" value="${id}" data-product-index="${index}">
                                ${name} (for ${item.product_name})
                            </label><br>`;
                    }).join('');
                    childSelectionContainer.innerHTML += children;
                }
            });

            dialog.showModal();
        });
    }

    function updateSelectionDisplay() {
        const selectedItems = data.filter(item => item.state);
        const totalSelected = selectedItems.reduce((sum, item) => sum + item.product_quantity, 0);
        const totalAmount = selectedItems.reduce((sum, item) => sum + (item.product_price * item.product_quantity), 0).toFixed(2);

        totalCount.textContent = totalSelected;
        totalPrice.textContent = totalAmount;

        const allSelected = data.every(item => item.state || item.is_deleted);
        all.checked = allSelected;
        selectAll.checked = allSelected;
    }

    function updateCartItem(cartItemId, quantity) {
        fetch('/mips/ajax.php?action=update_cart_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                cart_item_id: cartItemId,
                quantity: quantity
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    console.error(result.error);
                    alert('Failed to update cart item.');
                } else {
                    init();
                }
            })
            .catch(error => {
                console.error('Error updating cart item:', error);
            });
    }

    function deleteCartItem(cartItemId, index) {
        fetch('/mips/ajax.php?action=delete_cart_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                cart_item_id: cartItemId
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    data.splice(index, 1);
                    init();
                } else {
                    console.error(result.error);
                    alert('Failed to delete cart item.');
                }
            })
            .catch(error => {
                console.error('Error deleting cart item:', error);
            });
    }

    function deleteSelectedItems(cartItemIds) {
        fetch('/mips/ajax.php?action=delete_selected', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                cart_item_ids: cartItemIds.join(',')
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    data = data.filter(item => !cartItemIds.includes(item.cart_item_id));
                    init();
                    location.reload();
                } else {
                    console.error(result.error);
                    alert('Failed to delete selected items.');
                }
            })
            .catch(error => {
                console.error('Error deleting selected items:', error);
            });
    }
});