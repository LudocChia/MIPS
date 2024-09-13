<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = "My Purchase - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";





// Prepare and execute the query to search for donators based on event_meal_id
$parent_id = $_SESSION['user_id'];
$stmt2 = $pdo->prepare("SELECT * FROM `event_meal` 
                                INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                                INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                                INNER JOIN `event` ON event_meal.event_id = event.event_id 
                                INNER JOIN `donator` ON event_meal.event_meal_id = donator.event_meal_id 
                                where donator.parent_id = :parent_id
                                ");

// Bind the parameters
$stmt2->bindParam(':parent_id', $parent_id);
$stmt2->execute();

$donators = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Display the meals if available
if ($donators) {
    // echo '<h2>Donator Information</h2>';
    foreach ($donators as $donator) {
        // echo '<p>Donator_ID: ' . htmlspecialchars($donator['donator_id']) . '</p>';
        // echo '<p>Name: ' . htmlspecialchars($donator['parent_name']) . '</p>';
        // echo '<p>Time: ' . htmlspecialchars($donator['date']) . '</p>';
        // echo '<p>Meal Name: ' . htmlspecialchars($donator['meal_name']) . '</p>';
        // echo '<p>Quantity: ' . htmlspecialchars($donator['p_set']) . '</p>';
        // Uncomment below if you want to show descriptions too
        // echo '<hr>';
    }
} else {
    // echo '<p>No donators found for this meal type.</p>';
}
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container aside-main">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="purchase">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Donation History</h1>
                    </div>
                </div>
                
                <div >
                    <table>
                        <thead>
                            <tr>
                                <th>Donator ID</th>
                                <th>Event</th>
                                <th>Time</th>
                                <th>Meal Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($donators)): ?>
                            <?php foreach ($donators as $donator): ?>
                                    <div class="donatorBox">
                                        <tr id="tableRow">
                                            <td id="tableData">
                                                <p>  <?=htmlspecialchars($donator['donator_id'])?></p>
                                            </td>
                                            <td id="tableData">
                                                <p> <?=htmlspecialchars($donator['name'])?> </p>
                                            </td>
                                            <td id="tableData">
                                                <p><?=htmlspecialchars($donator['date'])?></p>
                                            </td>
                                            <td id="tableData">
                                                <p> <?=htmlspecialchars($donator['meal_name'])?> </p>
                                            </td>
                                            <td id="tableData">
                                                <p> <?=htmlspecialchars($donator['p_set'])?> </p>
                                            </td>
                                        </tr>
                                    </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p id="nRecord">No donators for now.</p>
                        <?php endif; ?>
                        </tbody>
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