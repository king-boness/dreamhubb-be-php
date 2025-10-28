# 🧩 dreamhubb API – Dokumentácia (v1)

**Verzia:** 1.1.8  
**Dátum:** 2025-10-28

**Base URL (DEV):** `http://127.0.0.1:8000`  
**Prefix:** `/api`  
**Autentifikácia:** `Authorization: Bearer <JWT>`

---

## 📚 Obsah

- [1. AUTH](#1-auth--registrácia-prihlásenie-verifikácia)
- [2. USER](#2-user--profil-zmena-hesla-profilovka)
- [3. UPLOAD](#3-upload--obrázky-príspevkov)
- [4. POSTS](#4-posts--crud-systém)
- [5. SYSTEM](#5-system--zdravie-servera)
- [6. STAVOVÉ KÓDY](#6-stavové-kódy-a-chyby)
- [AUTENTIFIKÁCIA](#autentifikácia)
- [POZNÁMKY PRE-FE](#poznámky-pre-fe-tím)

---

## 🔐 1. AUTH – Registrácia, prihlásenie, verifikácia

| Endpoint                                  | Metóda | Popis                                      | Auth |
|-------------------------------------------|--------|--------------------------------------------|------|
| `/api/register`                           |  POST  | Registrácia používateľa                    |  NO  |
| `/api/login`                              |  POST  | Prihlásenie používateľa (vracia JWT token) |  NO  |
| `/api/refresh`                            |  POST  | Obnovenie JWT tokenu                       |  NO  |
| `/api/logout`                             |  POST  | Odhlásenie používateľa                     |  YES |
| `/api/verify-email/{id}/{hash}`           |  GET   | Overenie e-mailu po registrácii            |  YES |
| `/api/email/verification-notification`    |  POST  | Znova odoslanie verifikačného e-mailu      |  YES |

**Request – login:**
```json
{
  "email": "mato@example.com",
  "password": "secret123"
}

**Response – login:**
```json
{
  "status": "success",
  "authorization": {
    "token": "eyJ0eXAiOiJKV1QiLCJh...",
    "type": "bearer"
  }
}

## 👤 2. USER – Profil, zmena hesla, profilovka

| Endpoint               | Metóda |  Popis                                                                        |  Auth  |
|------------------------|--------|-------------------------------------------------------------------------------|--------|
/api/user	                GET      Získa detaily prihláseného používateľa	                                          YES
/api/user/update	        PUT	     Aktualizácia profilu (username, dátum narodenia, pohlavie, lokácia, atď.)	      YES
/api/user/change-password	POST	 Zmena hesla používateľa	                                                      YES
/api/user/profile-picture	POST     Upload profilovej fotky (file)	                                                  YES
/api/user/profile-picture	DELETE   Vymazanie profilovej fotky	                                                      YES

**Príklad response – GET /api/user:**
```json
{
  "status": "success",
  "user": {
    "id": 6,
    "username": "mato",
    "email": "mato@example.com",
    "date_birth": "1996-08-21",
    "gender": "male",
    "location_country_id": 1,
    "tokens": 100,
    "profile_picture": null,
    "email_verified_at": "2025-10-27T21:52:24.000000Z"
  }
}

## 🖼️ 3. UPLOAD – Obrázky príspevkov

| Endpoint               | Metóda |  Popis                            | Auth |           ontent-Type          |
|------------------------|--------|-----------------------------------|------|--------------------------------|
/api/upload                 POST	 Upload obrázka (napr. k postu)	    YES	          multipart/form-data

**Form-data parametre:**

| Názov          |  Typ  |  Povinné     | Popis                              |
|----------------|-------|--------------|------------------------------------|
file	          File	      YES	      Obrázok na upload
post_id	          Integer	  NO	      (voliteľne) priradenie k príspevku

**Príklad response:**
```json
{
  "status": "success",
  "message": "Image uploaded successfully.",
  "image": {
    "url": "https://res.cloudinary.com/dy2omstwu/image/upload/v1761667910/uploads/ny94llqmxojsnlrizo5s.png",
    "public_id": "uploads/ny94llqmxojsnlrizo5s"
  }
}

## 🧾 4. POSTS – CRUD systém

| Endpoint               |  Metóda  |  Popis                                         |  Auth  |
|------------------------|----------|------------------------------------------------|--------|
/api/posts	                 GET	   Zoznam všetkých príspevkov	                     NO
/api/posts/{id}	             GET	   Detail konkrétneho príspevku	                     NO
/api/my-posts	             GET	   Zoznam príspevkov prihláseného používateľa	     YES
/api/post-create	         POST	   Vytvorenie nového príspevku	                     YES
/api/post-update/{id}	     PUT	   Aktualizácia príspevku	                         YES
/api/post-delete/{id}	     DELETE	   Zmazanie príspevku (vrátane obrázkov)             YES

**Request – post-create:**
```json
{
  "title": "Testovací post",
  "description": "Toto je testovací obsah",
  "category_id": 1
}

**Response – post-create:**
```json
{
  "status": "success",
  "post": {
    "post_id": 1,
    "title": "Testovací post",
    "description": "Toto je testovací obsah",
    "user_id": 6,
    "created_at": "2025-10-27T12:00:00Z"
  }
}

**Response – getAllPosts:**
```json
{
  "status": "success",
  "data": [
    {
      "post_id": 1,
      "title": "Testovací post",
      "description": "Toto je testovací obsah",
      "user_id": 6,
      "created_at": "2025-10-27T12:00:00Z"
    }
  ]
}

## ⚙️ 5. SYSTEM – Zdravie servera

| Endpoint    |  Metóda  |  Popis                                         |  Auth  |
|-------------|----------|------------------------------------------------|--------|
/api/health	      GET	    Testovacie „ping“ pre monitoring / status	      NO

**Response:**
```json
{ "status": "ok", "message": "API is running." }

## 🧭 6. STAVOVÉ KÓDY A CHYBY

| Kód |                      Popis                         |
|-----|----------------------------------------------------|
  200	    OK – úspešná operácia
  201	    Vytvorené
  400	    Neplatný vstup
  401	    Neautorizovaný (chybný / expirovaný token)
  403	    Zakázané
  404	    Nenájdené
  422	    Chyba validácie
  500	    Interná chyba servera

**Príklad chyby 422:**
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}

## 🔑 Autentifikácia

**Všetky chránené endpointy vyžadujú hlavičku:**

```makefile
Authorization: Bearer <tvoj_JWT_token>

**Ak token expiroval → použite endpoint:**

```bash
POST /api/refresh
- ktorý vráti nový JWT. -

## 📦 Obsahové typy

JSON (application/json)
Upload: multipart/form-data
Všetky časy vo formáte ISO 8601 (UTC)

## 🧭 Poznámky pre FE tím

1.) Najprv zavolajte /api/login → uložíte JWT do hlavičky.
2.) Potom testujte ďalšie endpointy podľa tabuľky.
3.) Pre uploady použite form-data (s kľúčom file).
4.) /api/my-posts vracia príspevky len pre prihláseného používateľa.
5.) /api/health možno použiť na rýchle overenie spojenia.
6.) /api/email/verification-notification slúži na opätovné odoslanie e-mailu s potvrdením.

© dreamhubb – API dokumentácia (verzia 1.1.8)
