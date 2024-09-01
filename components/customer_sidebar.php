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
                <a href="/mips/account.php" class="<?= isActive('profile.php', $currentPage); ?>"><i class="bi bi-grid-1x2-fill"></i>
                    <h4>My Account</h4>
                </a>
            </li>
            <li>
                <a href="/mips/purchase.php" class="<?= isActive('purchase.php', $currentPage); ?>"><i class="bi bi-grid-1x2-fill"></i>
                    <h4>My Purchase</h4>
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