# 🧱 Instrukcja uruchomienia środowiska WordPress + WooCommerce lokalnie



##  1. Uruchomienie środowiska


```bash
make up
```

To polecenie uruchamia usługi zdefiniowane w pliku `docker-compose.yml`.

- WordPress będzie dostępny pod adresem: `http://localhost:8080/`
- Baza danych zostanie utworzona jako wolumen Dockera i zainicjalizowana automatycznie.

---

## Dane dostępowe do bazy danych

Ustawienia bazy danych są zdefiniowane w pliku `docker-compose.yml`:


Baza danych MySQL jest zapisywana w nazwanym wolumenie Dockera (`wp_wc_local_db_data`).  
Dane są trwałe i nie znikają między restartami, o ile nie wykonasz `make reset`.

```env
MYSQL_DATABASE=wpdb
MYSQL_USER=wpuser
MYSQL_PASSWORD=wppass
MYSQL_ROOT_PASSWORD=rootpass
```

---

## Instalacja WooCommerce

Po uruchomieniu środowiska zainstaluj wtyczkę WooCommerce w kontenerze WordPressa:

1. Wejdź do kontenera WordPressa:

   ```bash
   docker compose exec wordpress bash
   ```

2. Zainstaluj i aktywuj WooCommerce (wersja zgodna z WP 6.4.x):

   ```bash
   wp plugin install woocommerce --version=8.6.1 --activate --allow-root
   ```

---

## Dostęp do strony


```
http://localhost:8080/
```

Przejdź przez instalację WordPressa i kreator WooCommerce.



