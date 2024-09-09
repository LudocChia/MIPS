<?php
function isActive($targetPage, $currentPage)
{
    return $currentPage === $targetPage ? 'active' : '';
}


?>
<aside>
    <div class="actions">
        <button id="close-btn">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>
    <div class="sidebar">
        <ul>
            <li>
                <a href="/mips/admin" class="<?= isActive('/mips/admin/', $currentPage); ?>"><i class="bi bi-grid-1x2-fill"></i>
                    <h6>Dashboard</h6>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="bookshop-btn">
                    <i class="bi bi-shop-window"></i>
                    <h6>Bookshop</h6>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="bookshop-show" style="display: <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'block' : 'none'; ?>">
                    <li><a href="/mips/admin/bookshop/mainCategory.php" class="<?= isActive('/mips/admin/bookshop/mainCategory.php', $currentPage); ?>"><i class="bi bi-tags-fill"></i>
                            <h6>Main Category</h6>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/subcategory.php" class="<?= isActive('/mips/admin/bookshop/subcategory.php', $currentPage); ?>"><i class="bi bi-tag-fill"></i>
                            <h6>Subcategory</h6>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/size.php" class="<?= isActive('/mips/admin/bookshop/size.php', $currentPage); ?>"><i class="bi bi-aspect-ratio-fill"></i>
                            <h6>Product Size</h6>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/product.php" class="<?= isActive('/mips/admin/bookshop/product.php', $currentPage); ?>"><i class="bi bi-box-seam-fill"></i>
                            <h6>All Product</h6>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/order.php" class="<?= isActive('/mips/admin/order.php', $currentPage); ?>">
                    <i class="bi bi-receipt"></i>
                    <h6>Order</h6>
                    <span class="count" id="pending-order-count"></span>
                </a>
            </li>
            <li>
                <a href="/mips/admin/grade.php" class="<?= isActive('/mips/admin/grade.php', $currentPage); ?>">
                    <i class="bi bi-mortarboard-fill"></i>
                    <h6>Grade</h6>
                </a>
            </li>
            <li>
                <a href="/mips/admin/class.php" class="<?= isActive('/mips/admin/class.php', $currentPage); ?>">
                    <i class="bi bi-easel2-fill"></i>
                    <h6>Class</h6>
                </a>
            </li>
            <li>
                <a href="/mips/admin/announcement.php" class="<?= isActive('/mips/admin/announcement.php', $currentPage); ?>">
                    <i class="bi bi-megaphone-fill"></i>
                    <h6>Announment</h6>
                </a>
            </li>
            <li>
                <a href="/mips/admin/admin_meal/adminMain.php" class="<?= isActive('/mips/admin/admin_meal/adminMain.php', $currentPage); ?>">
                    <i class="fa fa-cutlery" aria-hidden="true"></i>
                    <h6>Meal Donation</h6>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="user-btn">
                    <i class="bi bi-person-fill"></i>
                    <h6>User Type</h6>
                    <i class="bi bi-chevron-down second <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'block' : 'none'; ?>">
                    <li>
                        <a href="/mips/admin/user/admin.php" class="<?= isActive('/mips/admin/user/admin.php', $currentPage); ?>"><i class="bi bi-person-fill-gear"></i>
                            <h6>All Admin</h6>
                        </a>
                    </li>
                    <!-- <li><a href="/mips/admin/user/teacher.php" class="</?= isActive('teacher.php', $currentPage); ?>"><svg width="20px" data-name="Layer 1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 11.5H15a1.5 1.5 0 0 0 1.5-1.5h0A1.5 1.5 0 0 0 15 8.5H4.5a3 3 0 0 0-3 3v2a3 3 0 0 0 1.456 2.573" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <path d="M7.5 16.5v6H9a1.5 1.5 0 0 0 1.5-1.5v-9.5M7.5 22.5H6A1.5 1.5 0 0 1 4.5 21v-9.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <circle cx="7.5" cy="4.5" r="2.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></circle>
                                <path d="M12 3.5h10.5v12h-10" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                            </svg>
                            <h4>All Teacher</h4>
                        </a>
                    </li> -->
                    <li>
                        <a href="/mips/admin/user/parent.php" class="<?= isActive('/mips/admin/user/parent.php', $currentPage); ?>"><i class="bi bi-people-fill"></i>
                            <h6>All Parent</h6>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/user/student.php" class="<?= isActive('/mips/admin/user/student.php', $currentPage); ?>"><i class='bx bxs-book-reader'></i>
                            <h6>All Student</h6>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/deactivated.php" class="<?= isActive('/mips/admin/deactivated.php', $currentPage); ?>">
                    <i class="bi bi-trash2-fill"></i>
                    <h6>Deactivated</h6>
                </a>
            </li>
        </ul>
    </div>
</aside>
<Script>
    $(document).ready(function() {
        $.ajax({
            url: '/mips/admin/ajax.php?action=get_pending_count',
            type: 'GET',
            success: function(response) {
                if (parseInt(response) == 0) {
                    $('#pending-order-count').hide();
                } else {
                    $('#pending-order-count').text(response);
                }
            },
            error: function() {
                $('#pending-order-count').hide();
            }
        });
    });
</Script>