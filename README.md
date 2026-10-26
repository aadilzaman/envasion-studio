# EnvisionStudio — Agency Project Management Dashboard

A modern, full-stack Project Management Dashboard built for **EnvisionStudio** as part of the Web Developer Practical Assessment.

The application allows agency project managers to oversee client initiatives, track milestones and task deliverables, filter projects by operational status, and perform full CRUD operations on both projects and tasks with immediate database persistence and data integrity.

---

## 🚀 Live Demo & Repository

- **Live URL**: `[Insert your deployed URL here, e.g., https://envisionstudio-dashboard.onrender.com]`
- **GitHub Repository**: `[Insert your repository URL here, e.g., https://github.com/your-username/envisionstudio-dashboard]`

---

## 🛠️ Technology Stack & Architectural Decisions

### **Backend: Laravel 12**
- **Why Laravel?** Laravel offers an elegant, production-grade architecture for RESTful APIs. It provides:
  - **Eloquent ORM** for clean data modeling and expressive relational queries (`Project hasMany Task`, `Task belongsTo Project`).
  - **Form Requests (`StoreProjectRequest`, `UpdateProjectRequest`, etc.)** ensuring strict data validation and isolation of business rules from controller logic.
  - **Database Migrations & Seeders** to automatically bootstrap the environment and import the official 10 mock projects and 20 tasks on initial boot.
  - **Automated Feature Testing** with PHPUnit validating all endpoints (`GET`, `POST`, `PUT`, `DELETE`, status filtering, cascade deletions, and summary aggregates).

### **Frontend: Vue 3 (Composition API + Vite)**
- **Why Vue 3?** 
  - **Composition API (`<script setup>`)** enables modular, maintainable, and reactive code structure.
  - **Single Page Application (SPA)** with **Vue Router** for seamless, instantaneous page transitions between the Dashboard overview, Projects directory, and Project Detail views without full page reloads.
  - **Centralized API Service** utilizing Axios with error interceptors and reactive feedback.
  - **Component-driven Design**: Modular, reusable components (`Sidebar`, `StatusBadge`, `ProjectModal`, `TaskModal`, `ConfirmModal`, `ToastContainer`).

### **Styling & UI: Tailwind CSS (Clean Light Theme) & FontAwesome 6**
- Designed with an intentional, crisp **modern light theme** (`slate-50` background, clean white card surfaces, soft borders, and refined typography via Inter).
- **FontAwesome 6 Icons** implemented across all actions, buttons, tabs, and status badges.
- **Modern Sidebar Navigation**: Fixed left-hand navigation on desktop and collapsible drawer on mobile, replacing the traditional top navbar for maximum dashboard usability.
- Fully responsive across desktop, tablet, and mobile screens.

### **Database: SQLite (Zero-Config) / MySQL Supported**
- Configured by default with **SQLite** for instant portability without external database setup. Compatible out-of-the-box with **MySQL** by updating `.env`.

---

## ✨ Implemented Feature Set

| Feature | Description |
| :--- | :--- |
| **Dashboard Summary** | Real-time aggregate metric cards showing **Total Projects**, **Active Projects**, **Completed Tasks**, and **Pending Tasks**, along with progress percentage bars, recent initiatives, and upcoming deadlines. |
| **Project Directory** | View projects in either **Card/Grid layout** or **Tabular layout** with live progress bars, task counts, and deadlines. |
| **Status Filter** | Filter projects by status: **All**, **Active**, **Completed**, or **On Hold** with count badges. Also includes live keyword search and sorting. |
| **Add New Project** | Modal form with fields: `Title`, `Description`, `Status`, and `Deadline` with validation error handling. |
| **Edit Project** | Inline edit modal allowing instant updates to project details and statuses. |
| **Delete Project** | Removal workflow with a confirmation prompt warning about cascading task removal. |
| **Project Detail View** | Dedicated page displaying complete project details, milestone statistics, and comprehensive task management. |
| **Task Management** | Add new tasks, edit existing tasks, delete tasks with confirmation, filter tasks by status (`Pending`, `In Progress`, `Completed`), and quick-toggle completion status directly via checkboxes. |

---

## 📋 API Documentation

All endpoints return standardized JSON structures: `{ "success": true, "data": ... }`.

### **Dashboard**
- `GET /api/dashboard/summary` — Returns total projects, active projects, completed tasks, pending tasks, completion rates, recent projects, and upcoming deadlines.

### **Projects**
- `GET /api/projects` — List all projects. Supports query parameters:
  - `?status=Active` (Filter by `Active`, `Completed`, `On Hold`, or `All`)
  - `?search=keyword` (Search across titles and descriptions)
  - `?sort_by=deadline` & `?sort_order=asc` (Sorting options)
- `POST /api/projects` — Create a project.
  - **Body**: `{ "title": "...", "description": "...", "status": "Active|Completed|On Hold", "deadline": "YYYY-MM-DD" }`
- `GET /api/projects/{id}` — Retrieve project details with eager-loaded tasks and counts.
- `PUT/PATCH /api/projects/{id}` — Update an existing project.
- `DELETE /api/projects/{id}` — Delete project and its associated tasks.

### **Tasks**
- `GET /api/projects/{id}/tasks` — List all tasks belonging to a specific project (supports `?status=...`).
- `POST /api/projects/{id}/tasks` — Add a new task to a project.
  - **Body**: `{ "title": "...", "description": "...", "status": "Pending|In Progress|Completed" }`
- `GET /api/tasks/{id}` — Retrieve an individual task with project metadata.
- `PUT/PATCH /api/tasks/{id}` — Update a task's title, description, or status.
- `PATCH /api/tasks/{id}/toggle` — Quick-toggle task completion (`Completed` ↔ `Pending`).
- `DELETE /api/tasks/{id}` — Delete a task.

---

## 💻 Local Setup Instructions

### Prerequisites
- **PHP** >= 8.2 with `pdo_sqlite` or `pdo_mysql` enabled
- **Composer** (v2+)
- **Node.js** (v18+) and **npm**

### Step-by-Step Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/envisionstudio-dashboard.git
   cd envisionstudio-dashboard
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Initialization & Seeding:**
   ```bash
   # Creates database/database.sqlite automatically and imports the 10 mock projects and 20 tasks
   php artisan migrate --seed
   ```

6. **Build Frontend Assets:**
   ```bash
   npm run build
   ```
   *(For active development with Hot Module Replacement, run `npm run dev` in a separate terminal).*

7. **Start the Laravel Server:**
   ```bash
   php artisan serve
   ```
   Open your browser and navigate to: **`http://127.0.0.1:8000`**

### Running Automated Tests
```bash
php artisan test
```

---

## 🐳 Docker Setup (Optional)

You can run the entire production-ready application using Docker:

```bash
docker compose up --build -d
```
The application will be accessible at `http://localhost:8000`.

---

## 🧗 Challenges Encountered & Solutions

1. **Reconciling Mock JSON Schema with Eloquent Conventions**:
   - *Challenge*: The provided mock data JSON used `projectId` (camelCase) while Laravel relational database conventions expect `project_id` (snake_case).
   - *Solution*: Designed the migration and seeder mapping `projectId` -> `project_id` during data ingestion and ensured the API controllers gracefully resolve relations whether queried through nested routes (`/api/projects/{id}/tasks`) or direct task endpoints.

2. **Real-time UX vs. Single Source of Truth**:
   - *Challenge*: Toggling tasks or updating statuses required immediate visual feedback on progress bars across both the Project view and the Dashboard.
   - *Solution*: Implemented optimistic updates paired with centralized API service calls and reactive toast notifications, ensuring accurate database synchronization without stale state.

3. **Strict Validation & Cascading Integrity**:
   - *Challenge*: Deleting a project without proper foreign key constraints could leave orphaned task records.
   - *Solution*: Configured `cascadeOnDelete()` at the database schema level and wrapped operations in Laravel transactions to guarantee relational data integrity.

---

## 🤖 Statement on Use of AI Tools & Code Validation

- **AI Tools Used**: Assisted by **Google Antigravity (Gemini)** during initial scaffolding, drafting API routes, and styling layout structures.
- **Application**:
  - Accelerated generation of boilerplate Form Requests and seeders for the 10 projects and 20 tasks.
  - Assisted in designing the dark theme aesthetic using Tailwind CSS color scales (`slate-950`, `indigo-500`, `emerald-400`, `cyan-400`).
- **Validation Process**:
  - **Code Review**: Every generated line was reviewed and verified for idiomatic Laravel 12 and Vue 3 standards.
  - **Automated Testing**: Wrote a dedicated PHPUnit test suite (`ProjectManagementApiTest.php`) verifying 100% of the core API endpoints (28 assertions covering CRUD operations, status filtering, and relations).
  - **End-to-End Manual Verification**: Tested all user journeys locally (creating/editing/deleting projects, adding/editing/toggling tasks, filtering by status, search, and layout switching).

---

## 📹 Video Walkthrough Outline (Optional 3-Minute Guide)

If recording a video walkthrough, consider following this structure:
1. **Introduction (0:00 - 0:30)**: Briefly present yourself, the tech stack (Laravel 12 + Vue 3 + Tailwind CSS), and show the live running application.
2. **Dashboard & Project Directory (0:30 - 1:15)**: Show the summary cards (total projects, active projects, completed/pending tasks), status filter pills, search bar, and card/table layout switch.
3. **Project & Task CRUD (1:15 - 2:15)**:
   - Create a new project and observe it appearing immediately in the directory.
   - Navigate into the project detail view.
   - Add a task, check it off (observe the completion bar advance), edit the task, and delete it.
   - Demonstrate the confirmation modal.
4. **Codebase Architecture & Conclusion (2:15 - 3:00)**: Highlight the clean project structure (`app/Http/Controllers/Api`, `database/seeders`, `resources/js/views`), and run `php artisan test` demonstrating passing tests.

---

## 📄 License
Created for the EnvisionStudio Web Developer Practical Assessment.
