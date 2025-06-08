# Laravel Vue 3 Template

A modern full-stack boilerplate combining **Laravel 12**, **Vue 3**, **Inertia.js**, and **Vuetify 3**.  
Built for rapid development with clean architecture, SOLID principles, and the Repository Design Pattern.

---

## 🚀 Features

-   Laravel 12 (PHP 8.2)
-   Vue 3 + Inertia.js (SPA architecture)
-   Vuetify 3 for beautiful Material Design components
-   Vite for fast frontend builds
-   Repository pattern for clean separation of concerns
-   SOLID principles for maintainable code
-   Modular and scalable project structure

---

## ⚙️ Requirements

Make sure your environment meets these requirements:

-   **PHP**: ^8.2
-   **Composer**
-   **Node.js**: >= 18.15
-   **MariaDB** or **MySQL**
-   PHP Extensions:
    -   `bcmath`
    -   `intl`
    -   `gd`
    -   `xml`
    -   `zip`
    -   `mbstring`
    -   `pdo`
    -   `mysql`
    -   `curl`

---

## 🛠️ Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/kirzan101/laravel-vue.git
cd laravel-vue3-template
```

### 2. Set up environment

```bash
cp .env.example .env
```

### 3. Install PHP dependencies

```bash
composer install
php artisan key:generate

#Optional: Run database migrations and seeders (if applicable):
php artisan migrate:fresh --seed
```

### 4. Install JavaScript dependencies

```bash
npm install
```

### 5. Start development servers
Open **two terminal windows or tabs** (one for the backend and one for the frontend):

```bash
# Terminal 1: Start Laravel backend
php artisan serve

# Terminal 2: Start Vite frontend (Vue + Inertia.js)
npm run dev
```
> 💡 VS Code Tip: Use `Ctrl + Shift + ``` to open a new terminal tab inside VS Code.

---

## 📁 Project Structure Highlights

```plaintext 
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Interfaces/
│   │   └── FetchInterface/         # Fetch interfaces
│   └── Services/                   # Business logic layer
│       └── FetchServices/         # Fetch services (index & show)
│
├── resources/
│   └── js/                         # Vue 3 + Inertia frontend
│       ├── Components/
│       │   ├── Customs/            # Customized Vuetify components
│       │   ├── Errors/             # Error display components
│       │   ├── Pages/              # Vue module pages
│       │   └── Utilities/          # Reusable utility components
│       ├── Layouts/                # Application layout (e.g., blank, main)
│       └── Pages/                  # Laravel + Inertia page components
│
├── routes/
│   └── web.php                     # Inertia-based web routes
│
├── database/
│   ├── migrations/                 # DB schema definitions
│   └── seeders/                    # DB seed data
```

---

## 🧩 Technology Stack

### Backend
* Laravel ^12.0
* Inertia.js Laravel Adapter ^2.0
* PHP ^8.2

### Frontend
* Vue ^3.5.13
* Vuetify ^3.8.3
* @inertiajs/vue3 ^2.0.8
* Vite
* @mdi/font ^7.4.47

## 📦 Production Build
```bash
npm run build
```

---

## 📝 License
This project is open-source under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙌 Contributing
Contributions are welcome! Feel free to open issues, fork the repo, or submit pull requests.

---

## 📫 Contact
For questions, feedback, or support, contact:
ckimescamilla@gmail.com or open an issue in the repository.

