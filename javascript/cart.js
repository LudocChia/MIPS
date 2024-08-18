window.addEventListener('load', function () {
    let tbody = document.querySelector('#order-items');
    let all = document.querySelector('#all');
    let selectAll = document.querySelector('#selectAll');
    let delall = document.querySelector('.del-all');
    let totalPrice = document.querySelector('.total-price');
    let totalCount = document.querySelector('#totalCount');

    let data = cartData.map(item => ({
        ...item,
        state: false
    }));

    function init() {
        let strHtml = '';
        let count = 0;
        let num = 0;
        let activeItemCount = 0; // Add a variable to count active items
        data.forEach(function (item, index) {
            const discountPrice = item.actual_price * 1;
            const totalPriceItem = item.actual_price * item.product_quantity;

            const isDeleted = item.is_deleted === 1;

            strHtml += `<tr>
                            <td></td>
                            <td>${!isDeleted ? `<input type="checkbox" class="ckh" id="ckh-${index}" ${item.state ? "checked" : ""}/>` : ''}</td>
                            <td class="product-image ${isDeleted ? 'deleted-product-image' : ''}">
                                <div class="image-container">
                                    <img src="uploads/${item.image_url}" alt="" />
                                    ${isDeleted ? '<div class="discontinued-text"><div>This product has</div><div>been discontinued</div></div>' : ''}
                                </div>
                            </td>
                            <td class="product-name">${item.product_name}</td>
                            <td class="product-price">
                                MYR ${discountPrice.toFixed(2)}
                                ${item.product_price !== item.actual_price && item.product_price > item.actual_price ? `<br><small>Discount MYR ${(item.product_price - item.actual_price).toFixed(2)}</small>` : ""}
                            </td>
                            <td class="product-quantity">
                                    <button class="add" id="add-${index}">+</button>
                                    <input type="text" style="width:40px" value="${item.product_quantity}" data-cart-item-id="${item.cart_item_id}"/>
                                    <button class="reduce" id="reduce-${index}">-</button>
                            </td>
                            <td class="product-total">RM ${totalPriceItem.toFixed(2)}</td>
                            <td class="product-action">
                            <button class="delete" data-cart-item-id="${item.cart_item_id}"><i class="bi bi-trash3-fill"></i> Delete</button>
                            </td>
                            <td></td>
                        </tr>`;

            if (item.state) {
                count++;
                num += item.actual_price * item.product_quantity;
            }

            if (!isDeleted) {
                activeItemCount++; // Increment the active item count
            }
        });
        tbody.innerHTML = strHtml;
        all.checked = activeItemCount > 0 && count === activeItemCount; // Compare count with active item count
        selectAll.checked = all.checked;
        totalCount.innerHTML = count;
        totalPrice.innerHTML = num.toFixed(2);
    }

    init();

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
        $.post('ajax.php', { update_quantity: true, cart_item_id: cartItemId, quantity: quantity }, function (response) {
            const result = JSON.parse(response);
            if (result.error) {
                console.error(result.error);
                alert('Failed to update cart item.');
            }
        });
    }

    function deleteCartItem(cartItemId, index) {
        $.post('ajax.php', { delete_item: true, cart_item_id: cartItemId }, function (response) {
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
            $.post('ajax.php', { delete_selected: true, cart_item_ids: selectedIds }, function (response) {
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
        $.post('ajax.php', { clear_cart: true }, function (response) {
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

        $.post('ajax.php', { checkout: true, selected_item_ids: selectedIds }, function (response) {
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
