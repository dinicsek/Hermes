# Backend
Ez a program logikája, illetve itt található az admin felület kódja is.

## Telepítés
A legegyszerűbb Dockerben elindítani a konténereket, így most ezt részletezzük.
Előfeltétel, hogy a Docker (és a docker-compose) és a Node (meg az npm) telepítve legyen a számítógépen.

Lépések:
1. Klónozzuk a projektet (ha még nem tettük volna meg) és lépjünk be (ebbe) a *backend* mappába!
2. Töltsük le a függőségeket egy Docker konténer elindításával: `docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs`!
3. Készítsünk egy másolatot az *.env.example* fájlról és nevezzük el *.env*-ként! Módosítsuk igényeink szerint a paramétereket.
4. Ezután mehet a `./vendor/bin/sail up`. (Ha a háttérben akarjuk futtani, akkor adjuk hozzá a `-d` opciót.) Ez elindítja a Docker-compose-ban leírt konténereket.
5. Alakítsuk ki az adatbázis struktúráját! Ehhez futtassuk (ha nem a háttérben indítottuk el az előző parancsot, akkor most nyissunk egy új terminál ablakot/lapot) a `./vendor/bin/sail artisan migrate` parancsot.
6. Töltsük is fel adatokkal a táblákat a `./vendor/bin/sail artisan db:seed` parancs kiadásával!
7. Ezután a szerverünk fut is. Ahhoz, hogy elérjük az admin felületet, futtassuk az `npm install` parancsot, mely telepíti a szükséges függőségeket!
8. Majd ha még nincs telepítve a pnpm, akkor `npx pnpm dev`, ha igen, akkor `pnpm dev` és el is indult a VITE.
9. Az admin felületet a *localhost/common* URL-t megnyitva érhetjük el.
   Alapból két felhasználó van:
   - admin,
   - menedzser.

   Előbbi email címe *admin@test.test*, míg utóbbié *manager@test.test*. Mindkettőhöz a *password* jelszó tartozik.
10. Jó fejlesztést!
