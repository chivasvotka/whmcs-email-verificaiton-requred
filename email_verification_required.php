<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

const EMAIL_VERIFICATION_REQUIRED_MODULE = 'email_verification_required';

function email_verification_required_config()
{
    return array(
        'name' => 'E-posta Doğrulama Zorunluluğu',
        'description' => 'E-posta adresi WHMCS tarafından doğrulanmamış müşterilerin sipariş tamamlamasını engeller.',
        'version' => '1.0.0',
        'author' => 'Custom Module',
        'language' => 'turkish',
        'fields' => array(
            'enabled' => array(
                'FriendlyName' => 'Modül durumu',
                'Type' => 'yesno',
                'Default' => 'on',
                'Description' => 'Sipariş tamamlamadan önce WHMCS e-posta doğrulamasını zorunlu tutar.',
            ),
        ),
    );
}

function email_verification_required_activate()
{
    try {
        Capsule::table('tbladdonmodules')->updateOrInsert(
            array('module' => EMAIL_VERIFICATION_REQUIRED_MODULE, 'setting' => 'enabled'),
            array('value' => 'on')
        );
        return array('status' => 'success', 'description' => 'E-posta doğrulama zorunluluğu etkinleştirildi. WHMCS çekirdek tabloları değiştirilmedi.');
    } catch (Throwable $exception) {
        return array('status' => 'error', 'description' => 'Modül etkinleştirilemedi: ' . $exception->getMessage());
    }
}

function email_verification_required_deactivate()
{
    try {
        Capsule::table('tbladdonmodules')
            ->where('module', EMAIL_VERIFICATION_REQUIRED_MODULE)
            ->where('setting', 'enabled')
            ->update(array('value' => 'off'));
        return array('status' => 'success', 'description' => 'E-posta doğrulama zorunluluğu devre dışı bırakıldı. Müşteri verileri değiştirilmedi.');
    } catch (Throwable $exception) {
        return array('status' => 'error', 'description' => 'Modül devre dışı bırakılamadı: ' . $exception->getMessage());
    }
}

function email_verification_required_upgrade($vars)
{
    return array('status' => 'success', 'description' => 'E-posta doğrulama zorunluluğu modülü güncel.');
}

function email_verification_required_is_enabled()
{
    try {
        $value = Capsule::table('tbladdonmodules')
            ->where('module', EMAIL_VERIFICATION_REQUIRED_MODULE)
            ->where('setting', 'enabled')
            ->value('value');
        return in_array(strtolower((string) $value), array('on', 'yes', '1', 'true'), true);
    } catch (Throwable $exception) {
        return false;
    }
}

function email_verification_required_current_client_id()
{
    return !empty($_SESSION['uid']) && is_numeric($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
}

function email_verification_required_is_admin_request($vars = array())
{
    return !empty($_SESSION['adminid']) || !empty($vars['isAdmin']) || !empty($vars['isadmin']);
}

function email_verification_required_client_is_verified($clientId)
{
    $clientId = (int) $clientId;
    if ($clientId < 1) {
        return false;
    }

    try {
        $clientVerified = Capsule::table('tblclients')
            ->where('id', $clientId)
            ->value('email_verified');
        if ((int) $clientVerified === 1) {
            return true;
        }

        $ownerUserId = Capsule::table('tblusers_clients')
            ->where('client_id', $clientId)
            ->where('owner', 1)
            ->value('auth_user_id');
        if (!$ownerUserId) {
            return false;
        }

        $verifiedAt = Capsule::table('tblusers')
            ->where('id', (int) $ownerUserId)
            ->value('email_verified_at');
        return !empty($verifiedAt);
    } catch (Throwable $exception) {
        return false;
    }
}

function email_verification_required_checkout_errors($enabled, $isAdmin, $clientId, $isVerified)
{
    if (!$enabled || $isAdmin || ((int) $clientId > 0 && $isVerified)) {
        return array();
    }

    return array('Ürün siparişi verebilmek için önce WHMCS hesabınızdaki e-posta adresini doğrulayın. Doğrulama e-postasındaki bağlantıyı kullanın; e-posta ulaşmadıysa müşteri alanındaki doğrulama uyarısından yeniden gönderin.');
}