<?php
// Old URL of the news page, kept so existing links keep working.
require __DIR__ . '/inc/bootstrap.php';
header('Location: ' . url('news.php'), true, 301);
