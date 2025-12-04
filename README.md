# Leírás
Ezt a jegykezelő webalkalmazást az ELTE IK Programtervező Informatikus BSc szakán, az 5. félévem szerveroldali webprogramozás beadandójaként (2025/26 ősz) fejlesztettem.

A rendszer központi elemét a Laravel (PHP) keretrendszer adja. Ez a technológia felel a teljes backend logika kezeléséért, ideértve a felhasználói hitelesítést, valamint a modellek és az adatbázis közötti kommunikációt.

# Főbb funkciók
### Felhasználói szerepkörök
Különálló jogkörök (Adminisztrátor és Felhasználó).
### Adatmodell
- **User:** Tárolja az alapvető felhasználói adatokat és az admin jogosultságot.
- **Event:** Kezeli az események adatait.
- **Seat:** Rögzíti a lefoglalható ülőhelyeket és alapárakat.
- **Ticket:** Kapcsolja a felhasználót, az eseményt és az ülőhelyet a vásárlás során (egyedi barcode-dal).
### Adminisztráció
Az adminisztrátor képes eseményeket létrehozni, szerkeszteni, törölni, valamint kezelni az ülőhely-kapacitásokat.
### Vásárlási logika
A rendszer nyomon követi az események telítettségét, és kezeli a jegyvásárlási folyamatot.
### Fejlesztéshez adatfeltöltés
A Seeder gondoskodik a teszteléshez szükséges alapvető adatok (pl. felhasználók, események) betöltéséről.
#### Teszteléshez bejelentkezési adatok:
| Szerepkör | E-mail cím | Jelszó |
| --- | --- | --- |
| Felhasználó | user@email.com | user |
| Adminisztrátor | admin@email.com | admin |

# Localhost futtatás
### Függőségek telepítése
```
composer install
npm install
```

### Konfigurációs & struktúra
Windows:
```
copy .env.example .env
php artisan key:generate
php artisan storage:link
``` 
Linux/Mac:
```
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

### Adatbázis
Windows:
```
type nul > database\database.sqlite
php artisan migrate:fresh --seed
```
Linux/Mac:
```
touch database\database.sqlite
php artisan migrate:fresh --seed
```

### Indítás (két külön terminálban)
```
npm run dev
php artisan serve
```

### Elérés
Böngészőben a http://127.0.0.1:8000 címen.
