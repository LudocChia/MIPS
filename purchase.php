<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = "My Purchase - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container aside-main">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="profile">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Purchase History</h1>
                    </div>
                </div>
                <form id="filter-form">
                    <div class="scrollable-tabs-container">
                        <div class="left-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </div>
                        <ul>
                            <li>
                                <button class="category-link active" data-category-id="all">All</button>
                            </li>
                            <li>
                                <button class="category-link" data-category-id="pending">Pending</button>
                            </li>
                            <li>
                                <button class="category-link" data-category-id="received">To Receive</button>
                            </li>
                            <li>
                                <button class="category-link" data-category-id="completed">Completed</button>
                            </li>
                            <li>
                                <button class="category-link" data-category-id="underpaid">Underpaid</button>
                            </li>
                            <li>
                                <button class="category-link" data-category-id="canceled">Return Refund</button>
                            </li>
                        </ul>
                        <div class="right-arrow active">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </form>
                <div class="orders">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="orders"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryLinks = document.querySelectorAll('.category-link');
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    categoryLinks.forEach(lnk => lnk.classList.remove('active'));
                    this.classList.add('active');
                    const status = this.dataset.categoryId;

                    history.pushState({
                        status: status
                    }, '', '?status=' + status);

                    fetchOrders(status);
                });
            });

            // Get the initial status from URL or default to 'all'
            const urlParams = new URLSearchParams(window.location.search);
            const initialStatus = urlParams.get('status') || 'all';
            fetchOrders(initialStatus);

            // Set the initial active tab
            document.querySelector(`.category-link[data-category-id="${initialStatus}"]`).classList.add('active');
        });

        function fetchOrders(status) {
            const statusParam = encodeURIComponent(status);
            const parentId = encodeURIComponent(<?php echo json_encode($_SESSION['user_id']); ?>);
            fetch(`/mips/ajax.php?action=get_orders&parent_id=${parentId}&status=${statusParam}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        displayOrders(data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                });
        }

        function displayOrders(data) {
            const ordersContainer = document.querySelector('#orders');
            if (data.length === 0) {
                ordersContainer.innerHTML = '<tr><td colspan="5">No orders found.</td></tr>';
            } else {
                let html = '';
                data.data.forEach(order => {
                    html += `<tr>
                                <td>${order.order_id}</td>
                                <td>${new Date(order.order_datetime).toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                                <td>RM ${parseFloat(order.order_price).toFixed(2)}</td>
                                <td>${order.payment_status}</td>
                                <td><a href="/mips/order_details.php?order_id=${encodeURIComponent(order.order_id)}">View Details</a></td>
                            </tr>`;
                });
                ordersContainer.innerHTML = html;
            }
        }
    </script>
</body>

</html>