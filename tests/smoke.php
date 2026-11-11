<?php

namespace WHMCS\Database {
    class Capsule
    {
    }
}

namespace {
    define('WHMCS', true);

    $hooks = array();
    function add_hook($name, $priority, $callback)
    {
        global $hooks;
        $hooks[$name][] = array('priority' => $priority, 'callback' => $callback);
    }

    require_once dirname(__DIR__) . '/email_verification_required.php';
    require_once dirname(__DIR__) . '/hooks.php';

    function email_verification_required_smoke_assert($condition, $message)
    {
        if (!$condition) {
            fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
            exit(1);
        }
        echo 'PASS: ' . $message . PHP_EOL;
    }

    email_verification_required_smoke_assert(EMAIL_VERIFICATION_REQUIRED_MODULE === 'email_verification_required', 'Addon sabiti tanımlı');
    email_verification_required_smoke_assert(isset(email_verification_required_config()['fields']['enabled']), 'Etkinleştirme ayarı tanımlı');
    email_verification_required_smoke_assert(isset($hooks['ShoppingCartValidateCheckout']), 'Checkout doğrulama hooku kayıtlı');
    email_verification_required_smoke_assert(email_verification_required_checkout_errors(true, false, 0, false) !== array(), 'Giriş yapmamış müşteri sipariş veremez');
    email_verification_required_smoke_assert(email_verification_required_checkout_errors(true, false, 12, false) !== array(), 'Doğrulanmamış müşteri sipariş veremez');
    email_verification_required_smoke_assert(email_verification_required_checkout_errors(true, false, 12, true) === array(), 'Doğrulanmış müşteri sipariş verebilir');
    email_verification_required_smoke_assert(email_verification_required_checkout_errors(false, false, 12, false) === array(), 'Modül kapalıyken checkout engellenmez');
    email_verification_required_smoke_assert(email_verification_required_checkout_errors(true, true, 12, false) === array(), 'Admin işlemi engellenmez');

    echo 'E-posta doğrulama zorunluluğu smoke testleri tamamlandı.' . PHP_EOL;
}