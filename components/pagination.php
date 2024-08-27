<div class="pagination">
    <div class="page-info">
        <?php
        if (!isset($_GET['page-nr'])) {
            $page = 1;
        } else {
            $page = $_GET['page-nr'];
        } ?>
        <p>Showing <?php echo $page; ?> of <?php echo $pageCount; ?></p>
    </div>
    <a href="?page-nr=1">First</a>

    <?php if (isset($_GET['page-nr']) && $_GET['page-nr'] > 1) { ?>
        <a href="?page-nr=<?= $_GET['page-nr'] - 1 ?>">Previous</a>
    <?php } else { ?>
        <a>Previous</a>
    <?php } ?>
    <div class="page-numbers">
        <?php
        for ($counter = 1; $counter <= $pageCount; $counter++) {
        ?>

            <a href="?page-nr=<?= $counter; ?>"><?= $counter; ?></a>

        <?php
        }
        ?>
    </div>

    <?php if (!isset($_GET['page-nr'])) { ?>
        <a href="?page-nr=2">Next</a>
        <?php } else {
        if ($_GET['page-nr'] >= $pageCount) { ?>
            <a>Next</a>
        <?php } else { ?>
            <a href="?page-nr=<?= $_GET['page-nr'] + 1 ?>">Next</a>
        <?php } ?>
        <a href="?page-nr=<?php echo $pageCount; ?>">Last</a>
    <?php } ?>
</div>