<?php
function isActive($pageName, $currentPage)
{
    return $pageName === $currentPage ? 'active' : '';
}
?>
<aside>
    <button id="close-btn">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>
    <div class="sidebar">
        <ul>
            <li>
                <a href="/mips/admin" class="<?= isActive('index.php', $currentPage); ?>"><i class="bi bi-grid-1x2-fill"></i>
                    <h4>Dashboard</h4>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="bookshop-btn">
                    <i class="bi bi-shop-window"></i>
                    <h4>Bookshop</h4>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="bookshop-show" style="display: <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'block' : 'none'; ?>">
                    <li><a href="/mips/admin/bookshop/mainCategory.php" class="<?= isActive('mainCategory.php', $currentPage); ?>"><i class="bi bi-tags-fill"></i>
                            <h4>Main Category</h4>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/subcategory.php" class="<?= isActive('subcategory.php', $currentPage); ?>"><i class="bi bi-tag-fill"></i>
                            <h4>Subcategory</h4>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/size.php" class="<?= isActive('size.php', $currentPage); ?>"><i class="bi bi-aspect-ratio-fill"></i>
                            <h4>Product Size</h4>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/product.php" class="<?= isActive('product.php', $currentPage); ?>"><i class="bi bi-box-seam-fill"></i>
                            <h4>All Product</h4>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/order.php" class="<?= isActive('order.php', $currentPage); ?>">
                    <i class="bi bi-receipt"></i>
                    <h4>Order</h4>
                    <span class="count" id="pending-order-count"></span>
                </a>
            </li>
            <li>
                <a href="/mips/admin/grade.php" class="<?= isActive('grade.php', $currentPage); ?>">
                    <i class="bi bi-mortarboard-fill"></i>
                    <h4>Grade</h4>
                </a>
            </li>
            <li>
                <a href="/mips/admin/class.php" class="<?= isActive('class.php', $currentPage); ?>">
                    <i class="bi bi-easel2-fill"></i>
                    <h4>Class</h4>
                </a>
            </li>
            <li>
                <a href="/mips/admin/announment.php" class="<?= isActive('announment.php', $currentPage); ?>">
                    <i class="bi bi-megaphone-fill"></i>
                    <h4>Announment</h4>
                </a>
            </li>
            <li>
                <a href="#" class="<?= isActive('#.php', $currentPage); ?>">
                    <i></i>
                    <h4>Meal Donation</h4>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="user-btn">
                    <i class="bi bi-person-fill"></i>
                    <h4>User Type</h4>
                    <i class="bi bi-chevron-down second <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'block' : 'none'; ?>">
                    <li>
                        <a href="/mips/admin/user/admin.php" class="<?= isActive('admin.php', $currentPage); ?>"><i class="bi bi-person-fill-gear"></i>
                            <h4>All Admin</h4>
                        </a>
                    </li>
                    <li><a href="/mips/admin/user/teacher.php" class="<?= isActive('teacher.php', $currentPage); ?>"><svg width="20px" data-name="Layer 1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 11.5H15a1.5 1.5 0 0 0 1.5-1.5h0A1.5 1.5 0 0 0 15 8.5H4.5a3 3 0 0 0-3 3v2a3 3 0 0 0 1.456 2.573" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <path d="M7.5 16.5v6H9a1.5 1.5 0 0 0 1.5-1.5v-9.5M7.5 22.5H6A1.5 1.5 0 0 1 4.5 21v-9.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <circle cx="7.5" cy="4.5" r="2.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></circle>
                                <path d="M12 3.5h10.5v12h-10" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                            </svg>
                            <h4>All Teacher</h4>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/user/parent.php" class="<?= isActive('parent.php', $currentPage); ?>"><i class="bi bi-people-fill"></i>
                            <h4>All Parent</h4>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/user/student.php" class="<?= isActive('student.php', $currentPage); ?>"><span class="material-symbols-outlined">local_library </span>
                            <h4>All Student</h4>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="deactivate-btn">
                    <i class="bi bi-trash2-fill"></i>
                    <h4>Deactivated</h4>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, 'user') !== false || strpos($currentPage, 'bookshop') !== false || strpos($currentPage, 'gradeclass') !== false || strpos($currentPage, 'announment') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul>
                    <li>
                        <a href="/mips/admin/deactivate/user.php" class="<?= isActive('user.php', $currentPage); ?>"><i class="bi bi-person-fill-slash"></i>
                            <h4>User</h4>
                        </a>

                    </li>
                    <li>
                        <a href="/mips/admin/deactivate/bookshop.php" class="<?= isActive('bookshop.php', $currentPage); ?>"><i class="bi bi-person-fill-slash"></i>
                            <h4>Bookshop</h4>
                        </a>

                    </li>
                    <li>
                        <a href="/mips/admin/deactivate/gradeclass.php" class="<?= isActive('gradeclass.php', $currentPage); ?>"><i class="bi bi-person-fill-slash"></i>
                            <h4>Grade and Class</h4>
                        </a>

                    </li>
                    <li>
                        <a href="/mips/admin/deactivate/announment.php" class="<?= isActive('announment.php', $currentPage); ?>"><i class="bi bi-person-fill-slash"></i>
                            <h4>Announment</h4>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/deactivate/payment.php" class="<?= isActive('payment.php', $currentPage); ?>"><i class="bi bi-person-fill-slash"></i>
                            <h4>Payment</h4>
                        </a>
                    </li>
                </ul>
            </li>
            <li><a href="javascript:void(0);">
                    <i class="bi bi-file-text-fill"></i>
                    <h4>Report</h4>
                    <i class="bi bi-chevron-down first"></i>
                </a>
                <ul>
                </ul>
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