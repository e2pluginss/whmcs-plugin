<?php
add_hook('AdminClientServicesTabFields', 1, function ($vars) {

});

add_hook('AdminAreaPage', 1, function (&$vars) {
    $extra_vars = array();
    if ($vars['filename'] == 'clientsservices') {
        $extra_vars['jquerycode'] = '$("#btnCreate").remove();$("#btnRenew").remove();';
    }
    return $extra_vars;
});

add_hook('ShoppingCartCheckoutCompletePage', 1, function ($vars) {
    $password = isset($_SESSION['wlvpn_password']) ? $_SESSION['wlvpn_password'] : '';
    $username = isset($_SESSION['wlvpn_username']) ? $_SESSION['wlvpn_username'] : '';
    $acct_id = isset($_SESSION['wlvpn_acct_id']) ? $_SESSION['wlvpn_acct_id'] : 0;
    if (isset($_SESSION['wlvpn_password'])) {
        unset($_SESSION['wlvpn_password'], $_SESSION['wlvpn_username'], $_SESSION['wlvpn_acct_id']);
        return '<div class="alert alert-warning">Please store your VPN login credentials somewhere secure.' .
            ' These will not be shown to you again. <br />If required, you can reset your password <a href="clientarea.php?action=productdetails&id=' . $acct_id . '"> here</a>.<br/>' .
            ' Username: <strong>' .
            $username .
            '</strong><br/> Password: <strong>' .
            $password .
            '</strong><br/></div>';
    } else {
        return '<div class="alert alert-danger">Something went wrong. please contact <a href="mailto:support@wlvpn.com">our support team</a>.</div>';
    }
});