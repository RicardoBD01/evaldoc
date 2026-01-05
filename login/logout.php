<?php
session_start();
session_destroy();
header('Location: /evaldoc/login');
exit();
?>
