<div class="pagination">
    <div class="page-info">
        <?php
        if (!isset($_GET['page-nr'])) {
            $page = 1;
        } else {
            $page = (int)$_GET['page-nr'];
        }
        ?>
        <p>Showing <?php echo $page; ?> of <?php echo $pageCount; ?></p>
    </div>

    <a href="?page-nr=1" class="<?php echo $page == 1 ? 'disabled' : ''; ?>">First</a>
    <a href="?page-nr=<?= $page > 1 ? $page - 1 : 1 ?>" class="<?php echo $page == 1 ? 'disabled' : ''; ?>">Previous</a>

    <div class="page-numbers">
        <?php
        for ($counter = 1; $counter <= $pageCount; $counter++) { ?>
            <a href="?page-nr=<?= $counter; ?>" class="<?= $counter == $page ? 'active' : ''; ?>"><?= $counter; ?></a>
        <?php
        }
        ?>
    </div>

    <a href="?page-nr=<?= $page < $pageCount ? $page + 1 : $pageCount ?>" class="<?php echo $page == $pageCount ? 'disabled' : ''; ?>">Next</a>

    <a href="?page-nr=<?= $pageCount ?>" class="<?php echo $page == $pageCount ? 'disabled' : ''; ?>">Last</a>
</div>