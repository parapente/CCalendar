# CCalendar

## Προαπαιτούμενα

-   PHP v8.1+
-   composer
-   npm

## Εγκατάσταση

Στον web server θα πρέπει να είναι ορατός μόνο ο public φάκελος. Αντιγράφουμε το
αρχείο .env.example στο αρχείο .env και κάνουμε τις απαραίτητες ρυθμίσεις για
τη βάση δεδομένων και το CAS.

Έπειτα μέσα στον φάκελο του κώδικα τρέχουμε:

```
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --seed
php artisan config:cache
npm ci
npm run build
```

## Ενημέρωση

Προσοχή να μην σβήσουμε το .env αρχείο και τον φάκελο storage που κρατάει τα
ανεβασμένα αρχεία!

Αφού αντικαταστήσουμε τα υπόλοιπα αρχεία τρέχουμε:

```
composer install --optimize-autoloader --no-dev
php artisan migrate
php artisan optimize:clear
php artisan config:cache
npm ci
npm run build
```

## Σημειώσεις για τα test

-   Δημιουργούμε ένα .env.testing για να τρέξουμε τα τεστ μέσω του pest. Καλό
    είναι να χρησιμοποιήσουμε άλλη βάση (πχ testing) αντί της κανονικής και πάντα
    όχι σε production περιβάλλον αλλά τοπικά στον υπολογιστή μας.

-   Δημιουργούμε ένα .env.cypress (ή αντιγράφουμε το .env.testing) ώστε να
    ρυθμίσουμε ότι θέλουμε για τα τεστ του Cypress.

-   Για να δουλέψει σωστά το CAS με το Cypress θα πρέπει να ρυθμίσουμε σωστά τη
    μεταβλητή CAS_CLIENT_SERVICE ώστε να έχει τιμή ίδια με το όνομα της εφαρμογής
    στο docker (http://laravel.test).
