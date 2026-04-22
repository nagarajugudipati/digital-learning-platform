# Digital Learning Platform (LMS)

##  Overview

This is a full-stack Learning Management System (LMS) built using Laravel.
It allows teachers to create courses, students to enroll and learn, and admins to manage and approve content.

---

##  Features

###  Admin

* Approve/reject teachers
* Approve courses and quizzes
* View all student reports
* Dashboard with analytics

###  Teacher

* Create and manage courses
* Upload lessons (video, PDF, image, text)
* Create quizzes (MCQ, True/False, Text)
* View student progress

###  Student

* Browse and purchase courses
* Learn lessons (video/PDF/image/text)
* Attempt quizzes
* Track progress and daily streak
* Add to cart and checkout

---

##  Payment System

* UPI & Card (simulated)
* QR-based payment UI
* Auto enrollment after payment

---

##  AI Chatbot

* Rule-based chatbot (offline)
* Uses keyword matching + database search
* Provides answers from lessons and predefined data

---

##  Reports & Analytics

* Student progress tracking
* Quiz scores & performance
* Admin and teacher dashboards

---

##  Additional Features

* Daily login streak system
* Role-based access control
* Pagination & search
* Toast notifications & loading states

---

## Tech Stack

* **Backend:** Laravel (PHP)
* **Frontend:** Blade, Tailwind CSS
* **JS Framework:** Alpine.js
* **Database:** SQLite
* **Version Control:** Git & GitHub

---

##  Project Architecture

* MVC (Model-View-Controller)
* RESTful routing
* Modular structure

---

##  Setup Instructions

```bash
git clone https://github.com/nagarajugudipati/digital-learning-platform.git
cd digital-learning-platform

composer install
npm install

copy .env.example .env
php artisan key:generate

php artisan migrate
php artisan storage:link

npm run dev
php artisan serve
```

---

##  Run Project

Open in browser:
http://127.0.0.1:8000

---

##  Future Improvements

* Real payment gateway integration
* AI/ML-based chatbot
* Mobile app version
* Cloud deployment

---


