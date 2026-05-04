<?php
session_start();
session_destroy();
header("Location: /WHITEGYM/index.html");
exit;
