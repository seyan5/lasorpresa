<?php
session_start();
session_destroy();
header('Location: login.php?action=logout');
exit();
