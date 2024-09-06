<?php
try {
    $stmt->execute();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} catch (PDOException $e) {
    echo "<script>
    alert('Database error: " . $e->getMessage() . "');
</script>";
}
