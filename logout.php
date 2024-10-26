<?php
session_start();
session_destroy();
header("Location: IS.html");
exit();
?>
