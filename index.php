<?php
$public_pages = ['welcome.html', 'login.html', 'products.html', 'add_product.html', 'edit_product.html', 'add_review.html'];

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if (in_array($page, $public_pages)) {
        include $page;
    } else {
        include 'index.html';
    }
} else {
    include 'index.html';
}
?>
