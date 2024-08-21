<?php
session_start();
if (session_destroy()) {
    header("location: /mahans/admin/login.php");
}
