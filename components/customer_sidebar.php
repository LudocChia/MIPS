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
                <!-- <a href="/mips/account.php" class="<?= isActive('account.php', $currentPage); ?>"><i class="bi bi-person-circle"></i> -->
                <a href="javascript:void(0)" class="<?= isActive('account.php', $currentPage); ?>"><i class="bi bi-person-circle"></i>
                    <h6>My Account</h6>
                </a>
            </li>
            <li>
                <a href="/mips/purchase.php" class="<?= isActive('purchase.php', $currentPage); ?>"><i class="bi bi-receipt"></i>
                    <h6>My Purchase</h6>
                </a>
            </li>
            <li>
                <a href="/mips/parent/donationHistory.php" class="<?= isActive('donation.php', $currentPage); ?>"><i class="bi bi-wallet"></i></i>
                    <h6>My Donation</h6>
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