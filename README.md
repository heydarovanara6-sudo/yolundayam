# 🛣️ Yolundayam - Supervised Driving Experience Dashboard

**Yolundayam** is a premium, full-stack web application designed to help student drivers track, analyze, and optimize their supervised driving journey. Built with a focus on **Security, Ergonomics, and Data Visualization**, it provides a seamless experience from the first kilometer to the final exam.

---

## 🚀 Key Features

### 🛡️ Core Security
- **Session-Based ID Anonymization**: All internal Primary and Foreign Keys are masked in URLs using a custom `SessionAnonymizer`. High-security mapping prevents ID enumeration or direct database exposure.
- **Prepared SQL Statements**: 100% of database interactions use PDO with secured parameter markers to prevent SQL Injection.
- **CSRF Protection & Sanitization**: Robust input handling for all driving telemetry data.

### 📊 Advanced Analytics
- **Dynamic Dashboard**: Interactive table managed via **DataTables JS**, featuring advanced multi-dimensional filtering and sorting.
- **Visual Insights**: 
  - **Success Evolution**: Line chart tracking cumulative mileage progress.
  - **Contextual Breakdown**: Doughnut charts displaying weather and road condition distributions.
- **Automatic Statistics**: Real-time calculation of total distance and experience metrics.

### 📱 Premium UX/UI
- **Glassmorphism Design**: A modern, translucent UI with vibrant backgrounds and smooth transitions.
- **Mobile-First Responsiveness**: 
  - **Smart Header**: Responsive navigation that optimizes for vertical space on mobile.
  - **Table Card-View**: Data tables automatically transform into readable interactive cards on small screens.
- **Seamless Cinematic Background**: High-performance YouTube background video utilizing the **IFrame API** for a "play-when-ready" effect, hiding all initial player initialization artifacts.

---

## 🏗️ Technical Architecture

The project follows a clean **MVC-inspired** structure:

- **Entities**: `DrivingExperience.php` - Object-oriented representation of a driving session.
- **Managers**: `class.inc.php` - Business logic layer handling data retrieval and processing.
- **Database**: `DB.inc.php` - Dedicated Singleton for PDO management, optimized for **AlwaysData** environments.
- **Configuration**: Global localization set to `Asia/Baku` to ensure accurate local time tracking.

---

## 🛠️ Setup & Installation

1. **Database Setup**:
   - Import the provided `schema.sql` into your MySQL environment.
   - Configure your credentials in `inc/DB.inc.php`.

2. **Server Requirements**:
   - PHP 7.4+
   - MySQL 5.7+
   - `pdo_mysql` extension enabled.

3. **Deployment**:
   - Upload the files to your public directory (e.g., `www/` on AlwaysData).
   - Ensure the `inc/` directory is protected or outside the public web root in production.

---

## 📜 Credits

Built with precision for the **Supervised Driving Excellence** peer review.
- **Typography**: Outfit, Inter.
- **Libraries**: Chart.js, jQuery, DataTables.
- **Design Inspiration**: Modern Glassmorphism & Cyberpunk aesthetics.

---
© 2025 Yolundayam. Leveling up your driving journey.
