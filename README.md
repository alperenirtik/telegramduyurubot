# Telegram Duyuru Botu

Bu proje, bir Telegram botu aracılığıyla gruba belirli zaman aralıklarıyla otomatik duyuru mesajları göndermeye yarayan bir PHP uygulamasıdır. Uygulama, cron iş görevleriyle çalışır ve eklenen duyuruları zamanında gönderir. Ayrıca, anlık mesaj gönderme, duyuruları biçimlendirme ve düzenleme gibi özellikler sunar.

## Özellikler

- **Kullanıcı Giriş Sistemi**: Sadece yöneticiler giriş yaparak duyuru gönderme yetkisine sahip olur. Varsayılan giriş bilgileri `admin` kullanıcı adı ve `admin` şifresidir.
- **Otomatik Duyuru Gönderimi**: `crone.php` dosyası cron job olarak eklenerek belirli zaman aralıklarında otomatik olarak duyuru mesajları gönderilebilir. Gönderilen duyurunun ilk başlama zamanı, tekrar etme sıklığı saniye cinsinden ayarlaanbilir.
- **Duyuru Formatlama**: Kullanıcılar duyuru mesajlarını kalın, italik, altı çizili, üstü çizili, alıntı, link, kod, preformat, ve spoiler olarak formatlayabilir.
- **Duyuru İşlemleri**: Kullanıcılar duyurularını listeleyebilir, düzenleyebilir, zaman ayarını değiştirebilir ve silebilirler.
- **Anlık Mesaj Gönderme**: Mesajlar, Telegram botu aracılığıyla belirli bir gruba anlık olarak gönderilebilir.
- **Admin Panel Ayarları**: BotFather ile oluşturulan bot API anahtarı ve grup ID'si admin panelinden ayarlanabilir.

## Kurulum

### Gereksinimler

- PHP 7.x veya üzeri
- MySQL
- Cpanel destekli herhangi bir hosting kullanılabilir
- Localhost üzerinde sorunsuz çalışmaktadır.

### Adımlar

1. **Depoyu Klonlayın**
    ```bash
    git clone https://github.com/kullaniciadi/telegramduyurubot.git
    cd telegramduyurubot
    ```

2. **Veritabanını Ayarlayın**
    - MySQL'de bir veritabanı oluşturun ve `config.php` dosyasındaki veritabanı bağlantı ayarlarını güncelleyin.
    - Admin tablosuna varsayılan giriş bilgilerini ekleyin:

3. **Admin Paneli Ayarları**
    - BotFather ile yeni bir bot oluşturun ve API anahtarını alın.
    - Grubunuza Rose ekleyerek `/id` komutunu kullanarak grup ID'sini öğrenin.
    - Bu bilgileri admin panelinin ayarlar kısmından güncelleyin.

4. **Cron Job Ekleyin**
    - `crone.php` dosyasını cron job olarak ekleyerek otomatik duyuru gönderimini yapılandırın

6. **Uygulamayı Tarayıcıda Açın**
    - Tarayıcınızda site.com/admin adresine gidin ve uygulamayı kullanmaya başlayın.


## Lisans

Bu proje MIT lisansı ile lisanslanmıştır, herkes tarafından kullanılabilir ve geliştirilebilir. Daha fazla bilgi için [LICENSE](LICENSE) dosyasına bakabilirsiniz.

## Katkıda Bulunanlar

- **Alperen İRTİK** - Geliştirici

