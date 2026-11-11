# WHMCS E-posta Doğrulama Zorunluluğu

Bu addon, e-posta adresi WHMCS tarafından doğrulanmamış müşterilerin checkout işlemini tamamlamasını engeller. WHMCS çekirdek dosyaları değiştirilmez; e-posta gönderimi ve doğrulama bağlantısı WHMCS'nin kendi sistemi tarafından yönetilir.

## Kurulum

1. `modules/addons/email_verification_required` klasörünü WHMCS kurulumundaki aynı konuma yükleyin.
2. WHMCS'nin **General Settings** bölümünde yerleşik e-posta doğrulama özelliğini etkinleştirin. Bu özellik kapalıysa müşteriler doğrulama e-postası alamaz ve sipariş veremez.
3. **Configuration > System Settings > Addon Modules** bölümünden **E-posta Doğrulama Zorunluluğu** addonunu etkinleştirin.
4. Modül için ilgili admin rolüne erişim izni verin.

Doğrulanmamış müşteriye checkout sırasında doğrulama talimatı gösterilir. Yeni müşteri önce hesabını oluşturup WHMCS doğrulama e-postasındaki bağlantıyı kullanmalı, ardından giriş yaparak sipariş vermelidir. Doğrulanmış müşterilerin sipariş akışı etkilenmez.

## Teknik notlar

- Kontrol, WHMCS'nin belgelenmiş `ShoppingCartValidateCheckout` hook'u üzerinden yapılır.
- Hem klasik `tblclients.email_verified` hem de WHMCS kullanıcı hesabındaki owner kaydının `tblusers.email_verified_at` alanı desteklenir.
- Admin tarafından başlatılan işlemler doğrulama kontrolünün dışındadır.
- Addon kendi müşteri verisini veya doğrulama token'ı saklamaz. Devre dışı bırakmak ayarı kapatır, müşteri verilerini değiştirmez.
- Canlıya almadan önce kayıt, e-posta gönderimi, doğrulama bağlantısı, giriş ve checkout akışlarını WHMCS ortamınızda test edin.