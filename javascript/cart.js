window.addEventListener('load', function () {
    let tbody = document.querySelector('#order-items');
    let all = document.querySelector('#all');
    let selectAll = document.querySelector('#selectAll');
    let delall = document.querySelector('.del-all');
    let totalPrice = document.querySelector('.total-price');
    let totalCount = document.querySelector('#totalCount');
    let parentId = document.querySelector('#user-id').value; // 假设页面中有隐藏字段存储 parent_id

    function get_cart_items() {
        return new Promise((resolve, reject) => {
            $.post('ajax.php?action=get_cart_items', { parent_id: parentId }, function (response) {
                const result = JSON.parse(response);
                if (result.error) {
                    console.error(result.error);
                    alert('Failed to load cart items.');
                    reject(result.error);
                } else {
                    resolve(result);
                }
            });
        });
    }

    let data = [];

    get_cart_items().then(cartItems => {
        data = cartItems.map(item => ({
            ...item,
            state: false
        }));
        init();
    }).catch(error => {
        console.error('Error loading cart items:', error);
    });

    function init() {
        let strHtml = '';
        let count = 0;
        let num = 0;
        let activeItemCount = 0;
        data.forEach(function (item, index) {
            const totalPriceItem = (item.product_price * item.product_quantity);
            const isDeleted = item.is_deleted === 1;

            strHtml += `<tr>
                            <td>${!isDeleted ? `<input type="checkbox" class="ckh" id="ckh-${index}" ${item.state ? "checked" : ""}/>` : ''}</td>
                            <td class="product-image ${isDeleted ? 'deleted-product-image' : ''}">
                                <div class="image-container">
                                    <img src="uploads/product/${item.image_url}" alt="" />
                                    ${isDeleted ? '<div class="discontinued-text"><div>This product has</div><div>been discontinued</div></div>' : ''}
                                </div>
                            </td>
                            <td class="product-name">${item.product_name}</td>
                            <td class="product-price">
                                MYR 
                                ${item.product_price}
                            </td>
                            <td class="product-quantity">
                                    <button class="add" id="add-${index}">+</button>
                                    <input type="text" style="width:40px" value="${item.product_quantity}" data-cart-item-id="${item.cart_item_id}"/>;
                                    <button class="reduce" id="reduce-${index}">-</button>
                            </td>
                            <td class="product-total">RM ${totalPriceItem.toFixed(2)}</td>
                            <td></td>
                            <td class="product-action">
                            <button class="delete" data-cart-item-id="${item.cart_item_id}"><i class="bi bi-trash3-fill"></i> Delete</button>
                            </td>
                        </tr>`;
            if (item.state) {
                count++;
                num += item.actual_price * item.product_quantity;
            }

            if (!isDeleted) {
                activeItemCount++;
            }
        });
        tbody.innerHTML = strHtml;
        all.checked = activeItemCount > 0 && count === activeItemCount;
        selectAll.checked = all.checked;
        totalCount.innerHTML = count;
        totalPrice.innerHTML = num.toFixed(2);
    }

    function updateSelection() {
        let isChecked = this.checked;
        data.forEach(item => {
            if (item.is_deleted !== 1) {
                item.state = isChecked;
            }
        });
        init();
    }

    all.addEventListener('change', updateSelection);
    selectAll.addEventListener('change', updateSelection);

    tbody.addEventListener('click', function (e) {
        let index = e.target.closest('tr').rowIndex - 1;
        if (e.target.matches('.add')) {
            data[index].product_quantity++;
            updateCartItem(data[index].cart_item_id, data[index].product_quantity);
        } else if (e.target.matches('.reduce')) {
            if (data[index].product_quantity > 1) {
                data[index].product_quantity--;
                updateCartItem(data[index].cart_item_id, data[index].product_quantity);
            }
        } else if (e.target.matches('.ckh')) {
            data[index].state = !data[index].state;
            updateSelectionDisplay();
        } else if (e.target.closest('.delete')) {
            let cartItemId = data[index].cart_item_id;
            deleteCartItem(cartItemId, index);
        }
        init();
    });

    function updateSelectionDisplay() {
        totalCount.innerHTML = data.filter(item => item.state).length;
        totalPrice.innerHTML = data.filter(item => item.state)
            .reduce((total, item) => total + (item.product_price * item.product_quantity), 0).toFixed(2);
    }

    function updateCartItem(cartItemId, quantity) {
        $.post('ajax.php?action=update_cart_item', { cart_item_id: cartItemId, quantity: quantity }, function (response) {
            const result = JSON.parse(response);
            if (result.error) {
                console.error(result.error);
                alert('Failed to update cart item.');
            }
        });
    }

    function deleteCartItem(cartItemId, index) {
        $.post('ajax.php?action=delete_cart_item', { cart_item_id: cartItemId }, function (response) {
            const result = JSON.parse(response);
            if (result.success) {
                data.splice(index, 1);
                init();
            } else {
                console.error(result.error);
                alert('Failed to delete cart item.');
            }
        });
    }

    delall.addEventListener('click', function () {
        let selectedIds = data.filter(item => item.state).map(item => item.cart_item_id);
        if (selectedIds.length > 0) {
            $.post('ajax.php?action=delete_selected', { cart_item_ids: selectedIds }, function (response) {
                const result = JSON.parse(response);
                if (result.success) {
                    data = data.filter(item => !item.state);
                    init();
                    location.reload();
                } else {
                    console.error(result.error);
                    alert('Failed to delete selected items.');
                }
            });
        }
    });

    document.querySelector('.clear').addEventListener('click', function () {
        $.post('ajax.php?action=clear_cart', { parent_id: parentId }, function (response) {
            const result = JSON.parse(response);
            if (result.success) {
                data = [];
                init();
                location.reload();
            } else {
                console.error(result.error);
                alert('Failed to clear cart.');
            }
        });
    });

    document.querySelector('.pay').addEventListener('click', function () {
        let selectedIds = data.filter(item => item.state).map(item => item.cart_item_id);

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No items selected',
                text: 'Please select at least one item to proceed to checkout.',
                confirmButtonText: 'OK'
            });
            return;
        }

        $.post('ajax.php?action=checkout', { selected_item_ids: selectedIds }, function (response) {
            const result = JSON.parse(response);
            if (result.success) {
                var orderId = result.orderId;
                window.location.href = 'checkout&payment.php?order_id=' + orderId;
            } else {
                console.error(result.error);
                alert('Failed to proceed to checkout.');
            }
        }, 'json');
    });
});
