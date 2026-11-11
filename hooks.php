<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once __DIR__ . '/email_verification_required.php';

add_hook('ShoppingCartValidateCheckout', 1, function ($vars) {
    $isEnabled = email_verification_required_is_enabled();
    $isAdmin = email_verification_required_is_admin_request($vars);
    if (!$isEnabled || $isAdmin) {
        return array();
    }

    $clientId = email_verification_required_current_client_id();
    $isVerified = $clientId > 0 && email_verification_required_client_is_verified($clientId);

    return email_verification_required_checkout_errors(
        true,
        false,
        $clientId,
        $isVerified
    );
});