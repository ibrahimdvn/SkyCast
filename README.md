# ☁️ SkyCast — Weather Intelligence Dashboard

A premium, glassmorphism-styled real-time weather application built with **Laravel**. SkyCast delivers hyper-accurate meteorological data through a breathtaking, distraction-free interface.

---

## 🖼️ Screenshots

| Welcome Page | Dashboard | Login |
|---|---|---|
| Glassmorphism landing with app intro | Live weather with hourly & extended forecast | Auth form with blurred background |

---

## ✨ Features

### 🔐 Authentication System
- **Register** — Create an account with name, email, and password
- **Login** — Session-based authentication with "Remember Me" support
- **Logout** — Secure session invalidation
- **Validation & Error Handling** — User-friendly inline error messages
- **Auth Flow** — Register → redirect to Login → redirect to Dashboard
- **Route Protection** — `/dashboard` is only accessible to authenticated users
- **Guest Guard** — Logged-in users cannot access `/login` or `/register`

### 🌤️ Weather Dashboard (`/dashboard`)
- Real-time weather data powered by **Open-Meteo API** (no API key required)
- Displays current conditions: temperature, description, high/low
- **Feels Like** and **Humidity** metric cards
- **24-Hour Hourly Forecast** with precipitation probability
- **Wind, Visibility, Pressure, Cloud Cover** secondary metrics
- **Sunrise / Sunset** arc visual
- **5-Day Extended Forecast** grid
- **City Search** — Search any global location from the header
- **Favorite Cities** — Star any city to pin it; appears in the favorites bar
- **Recent Searches** — Session-based recent search history
- **Personalized Greeting** — Good Morning/Afternoon/Evening + user's first name
- **Dynamic Background** — Background gradient changes based on weather condition and time of day (clear, clouds, rain, snow, night)
- **Live Clock** — Real-time clock updates every second in the header

### 🏠 Welcome Page (`/`)
- Accessible without login — no weather data fetched for guests
- Presents SkyCast with premium glassmorphism cards
- Feature highlights: Real-Time Data, 24h Forecast, 5-Day Outlook, Saved Cities
- "Get Started" → Register, "Sign In" link → Login
- Consistent design language with the main dashboard

### 📱 Responsive Design
- Fully mobile-responsive across all pages
- Header search bar and logout/auth buttons always on the same row
- Font sizes, padding, and card layouts scale gracefully from mobile to desktop
- Weather icon sizes adapt to screen width
- Login & Register forms scroll properly on small screens

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 10 |
| **Frontend** | Blade Templates + TailwindCSS (CDN) |
| **Icons** | Font Awesome 6 |
| **Fonts** | Inter (Google Fonts) |
| **Weather API** | [Open-Meteo](https://open-meteo.com/) (free, no key needed) |
| **Geocoding** | Open-Meteo Geocoding API |
| **Auth** | Laravel Session Authentication (custom — no Breeze/Jetstream) |
| **Caching** | Laravel Cache (300s TTL per city) |
| **Database** | MySQL / SQLite (users + favorite_cities tables) |

---

## 📁 Project Structure

```
SkyCast/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Register, Login, Logout
│   │   │   ├── WeatherController.php     # Dashboard & Welcome logic
│   │   │   └── FavoriteCityController.php # Toggle favorite cities
│   │   └── Middleware/
│   │       └── Authenticate.php          # Redirects unauthenticated users to /login
│   ├── Models/
│   │   ├── User.php
│   │   └── FavoriteCity.php
│   └── Services/
│       └── WeatherService.php            # Open-Meteo API integration + WMO code mapping
│
├── resources/views/
│   ├── layout/
│   │   └── app.blade.php                 # Base layout (Tailwind, FA, custom CSS, gradients)
│   ├── auth/
│   │   ├── login.blade.php               # Glassmorphism login form
│   │   └── register.blade.php            # Glassmorphism register form
│   ├── weather/
│   │   └── index.blade.php               # Main weather dashboard
│   └── welcome.blade.php                 # Public landing/intro page
│
├── routes/
│   └── web.php                           # All application routes
│
└── database/migrations/
    ├── create_users_table.php
    └── create_favorite_cities_table.php
```

---

## 🗺️ Routes

| Method | URI | Controller | Middleware | Description |
|---|---|---|---|---|
| GET | `/` | `WeatherController@welcome` | — | Welcome / landing page |
| GET | `/dashboard` | `WeatherController@index` | `auth` | Main weather dashboard |
| GET | `/login` | `AuthController@showLogin` | `guest` | Login form |
| POST | `/login` | `AuthController@login` | `guest` | Process login |
| GET | `/register` | `AuthController@showRegister` | `guest` | Register form |
| POST | `/register` | `AuthController@register` | `guest` | Process registration |
| POST | `/logout` | `AuthController@logout` | `auth` | Logout + redirect to `/` |
| POST | `/favorites/toggle` | `FavoriteCityController@toggle` | `auth` | Add/remove favorite city |

---

## 🎨 Design System

### Glassmorphism Components (defined in `layout/app.blade.php`)

```css
.glass-panel   /* Frosted glass card with backdrop-filter blur */
.glass-pill    /* Rounded pill button/badge with glass effect */
```

### Background Gradients

| Class | Condition | Colors |
|---|---|---|
| `bg-gradient-clear` | Clear sky | `#4facfe → #00f2fe` |
| `bg-gradient-clouds` | Cloudy / Overcast | `#8ba3ba → #4b5d67` |
| `bg-gradient-rain` | Rain / Drizzle / Thunder | `#2b5876 → #4e4376` |
| `bg-gradient-snow` | Snow | `#e0c3fc → #8ec5fc` |
| `bg-gradient-night` | Night time | `#0f2027 → #203a43 → #2c5364` |
| `bg-gradient-default` | Default / Unknown | `#1e3c72 → #2a5298` |

---

## 🌐 Weather API Integration

The app uses **Open-Meteo** (a free, open-source weather API — no API key required).

### Flow
1. **Geocoding** — City name → lat/lon via `geocoding-api.open-meteo.com`
2. **Weather Fetch** — lat/lon → current conditions + hourly + daily forecast
3. **WMO Code Mapping** — Open-Meteo returns WMO weather codes; these are mapped to OpenWeatherMap-compatible `main`, `description`, and `icon` fields for the views
4. **Caching** — Each city's data is cached for 5 minutes to avoid redundant API calls

---

## 🚀 Installation & Setup

```bash
# 1. Clone the repository
git clone <repo-url>
cd SkyCast

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_DATABASE=skycast
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Start the development server
php artisan serve
```

Open your browser at: **http://127.0.0.1:8000**

---

## 📌 Notes

- No external weather API key is required — Open-Meteo is completely free
- Weather data is cached per city for 5 minutes
- The Welcome page (`/`) is public and shows app introduction only
- The Dashboard (`/dashboard`) requires authentication
- Favorite cities are stored per user in the database
- The background gradient updates dynamically based on the searched city's weather and local time (day/night)

---

## 📄 License

MIT License — Built with ❤️ using Laravel & Open-Meteo.
