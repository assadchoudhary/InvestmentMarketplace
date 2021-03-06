<?php

namespace Helpers\Locales;

class Tr extends AbstractLanguage {

    public string
        $active            = 'Aktif',
        $addLevel          = 'Seviye Ekle',
        $addPlan           = 'Plan Ekle',
        $addProject        = 'Proje Ekle',
        $after             = 'üzerinden',
        $availableValues   = 'Olası Değerler',
        $badPassword       = 'Geçersiz Parola',
        $chat              = 'Sohbet',
        $check             = 'kontrol',
        $contact           = 'İletişim',
        $dateStart         = 'Başlangıç tarihi',
        $deleted           = 'Silindi',
        $deposit           = 'Depozito',
        $description       = 'Açıklama',
        $enter             = 'Giriş Yap',
        $error             = 'Hata',
        $exit              = 'Çıkış',
        $expectedNumber    = 'Sayı bekleniyor',
        $fileNotFound      = '%s dosyası bulunamadı',
        $fixedLength       = 'Sabit Uzunluk',
        $free              = 'ücretsiz',
        $freeForAddProject = 'Veritabanına tamamen proje ekleniyor',
        $from              = 'from',
        $guest             = 'Misafir',
        $headKeywords      = 'heyecan izleme 2020, yüksek karlı projeler, İnternette kazançlar, yatırım projeleri, piramitler',
        $headDescription   = '2020 Yüksek Verimli Yatırım Projeleri',
        $headTitle         = 'Yatırım Pazarı',
        $invalidDateFormat = 'Geçersiz tarih biçimi',
        $languages         = 'Site dilleri',
        $level             = 'seviye',
        $login             = 'Giriş Yap',
        $loginIsBusy       = 'Bu giriş zaten kayıtlı. Lütfen başka bir tane girin ',
        $maxLength         = 'Maksimum karakter sayısı:',
        $maxValue          = 'Maksimum değer:',
        $message           = 'Mesaj',
        $messageIsSent     = 'Mesaj gönderildi',
        $minDeposit        = 'Minimum Depozito',
        $minLength         = 'Minimum karakter sayısı:',
        $minValue          = 'Minimum değer:',
        $menu              = 'Menü',
        $name              = 'İsim',
        $needAuthorization = 'Yetkilendirmeniz gerekiyor',
        $no                = 'Hayır',
        $noAccess          = 'Erişim Yok',
        $noLanguage        = 'Dil bulunamadı',
        $noUser            = 'Kullanıcı bulunamadı',
        $noPage            = 'Sayfa bulunamadı',
        $noProject         = 'Proje bulunamadı',
        $notPublished      = 'Yayınlanmadı',
        $options           = 'Seçenekler',
        $password          = 'Şifre',
        $paymentSystem     = 'Ödeme Sistemleri',
        $paywait           = 'Ödeme Bekleniyor',
        $period            = 'Dönem',
        $placeBanner       = 'Haftada $%d|bir afiş yerleştirin',
        $plans             = 'Tarife Planları',
        $profit            = 'Kar',
        $projectName       = 'Proje Adı',
        $projectIsAdded    = 'Proje Eklendi',
        $projectUrl        = 'Projeye (veya yönlendirme bağlantısına) bağlantı',
        $prohibitedChars   = 'Geçersiz karakterler girildi',
        $rating            = 'Puan',
        $refProgram        = 'Tavsiye Programı',
        $registration      = 'Kayıt',
        $remember          = 'Hatırla',
        $remove            = 'Kaldır',
        $repeatPassword    = 'Şifreyi Tekrarla',
        $required          = 'Gereklidir',
        $scam              = 'Aldatmaca',
//        $selectFile      = 'Dosya seçin',
        $sendForm          = 'Formu Gönder',
        $showAllLangs      = 'Tüm Dilleri Göster',
        $siteExists        = 'Site zaten veritabanında',
        $siteIsFree        = 'Site veritabanında değil',
        $startDate         = 'Proje başlangıç tarihi',
        $success           = 'Başarılı',
        $userRegistered    = 'Kullanıcı kayıtlı',
        $userRegistration  = 'Kullanıcı Kaydı',
        $writeMessage      = 'Bir mesaj yazın',
        $wrongUrl          = 'Yanlış site url\'si',
        $wrongValue        = 'Geçersiz değer',
        $yes               = 'Evet',
        $youAreAuthorized  = 'Yetkiniz var';

    public array
        $paymentType       = ['Ödeme türü', 'Manuel', 'Anında', 'Otomatik'],
        $periodName        = ['', 'dakika', 'saat', 'gün', 'hafta', 'ay', 'yıl'],
        $currency          = ['dolar', 'euro', 'bitcoin', 'ruble', 'pound', 'yen', 'kazandı', 'rupi'];

    public function getPeriodName(int $i, int $k): string {
        return ['dakika', 'saat', 'gün', 'hafta', 'ay', 'yıl'][$i-1];
    }
}
