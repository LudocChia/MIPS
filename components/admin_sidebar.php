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
                <a href="index.php" class="<?= isActive('index.php', $currentPage); ?>"><i class="bi bi-grid-1x2-fill"></i>
                    <h4>Dashboard</h4>
                </a>
            </li>
            <li>
                <a href="#" class="bookshop-btn">
                    <i class="bi bi-shop-window"></i>
                    <h4>Bookshop</h4>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="bookshop-show" style="display: <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'block' : 'none'; ?>">
                    <li><a href="mainCategory.php" class="<?= isActive('mainCategory.php', $currentPage); ?>"><i class="bi bi-tags-fill"></i>
                            <h4>Main Category</h4>
                        </a>
                    </li>
                    <li><a href="subcategory.php" class="<?= isActive('subcategory.php', $currentPage); ?>"><i class="bi bi-tag-fill"></i>
                            <h4>Subcategory</h4>
                        </a>
                    </li>
                    <li><a href="size.php" class="<?= isActive('size.php', $currentPage); ?>"><i class="bi bi-aspect-ratio-fill"></i>
                            <h4>Product Size</h4>
                        </a>
                    </li>
                    <li><a href="product.php" class="<?= isActive('product.php', $currentPage); ?>"><i class="bi bi-box-seam-fill"></i>
                            <h4>All Product</h4>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="order.php" class="<?= isActive('order.php', $currentPage); ?>">
                    <i class="bi bi-receipt"></i>
                    <h4>Order</h4>
                </a>
            </li>
            <li>
                <a href="grade.php" class="<?= isActive('grade.php', $currentPage); ?>">
                    <i class="bi bi-people-fill"></i>
                    <h4>Grade</h4>
                </a>
            </li>
            <li>
                <a href="class.php" class="<?= isActive('class.php', $currentPage); ?>">
                    <i class="bi bi-people-fill"></i>
                    <h4>Class</h4>
                </a>
            </li>
            <li>
                <a href="announment.php" class="<?= isActive('announment.php', $currentPage); ?>">
                    <i class="bi bi-megaphone-fill"></i>
                    <h4>Announment</h4>
                </a>
            </li>
            <li>
                <a href="#" class="user-btn">
                    <i class="bi bi-person-fill"></i>
                    <h4>User Type</h4>
                    <i class="bi bi-chevron-down second <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, 'admin') !== false || strpos($currentPage, 'teacher') !== false || strpos($currentPage, 'parent') !== false ? 'block' : 'none'; ?>">
                    <li><a href="admin.php" class="<?= isActive('admin.php', $currentPage); ?>"><i class="bi bi-person-fill-gear"></i>
                            <h4>All Admin</h4>
                        </a>
                    </li>
                    <li><a href="teacher.php" class="<?= isActive('teacher.php', $currentPage); ?>"><i class="bi bi-mortarboard-fill"></i>
                            <h4>All Teacher</h4>
                        </a>
                    </li>
                    <li>
                        <a href="parent.php" class="<?= isActive('parent.php', $currentPage); ?>"><i class="bi bi-people-fill"></i>
                            <h4>All Parent</h4>
                        </a>
                    </li>
                    <li>
                        <a href="student.php" class="<?= isActive('student.php', $currentPage); ?>"><i class="bi bi-people-fill"></i>
                            <h4>All Student</h4>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="deactivate.php" class="<?= isActive('deactivate.php', $currentPage); ?>">
                    <i class="bi bi-trash2-fill"></i>
                    <h4>Deactivate List</h4>
                </a>
            </li>
            <li><a href="#">
                    <i class="bi bi-file-text-fill"></i>
                    <h4>Report</h4>
                    <i class="bi bi-chevron-down first"></i>
                </a>
                <ul>
                    <li>

                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>