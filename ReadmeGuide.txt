WELLMEADOWS HOSPITAL - HOSPITAL MANAGEMENT SYSTEM
Installation & Setup Guide

STEP 1: Install PHP Dependencies
composer install

STEP 2: Install Node Dependencies
npm install

STEP 3: Create Environment File
copy .env.example .env

STEP 4: Configure .env File
Copy and paste the .env content provided below:

APP_NAME=WellmeadowsHospital
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-south-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.cgczzowbxhjpncngmhrb
DB_PASSWORD=5225155geraldin

SUPABASE_URL=https://cgczzowbxhjpncngmhrb.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImNnY3p6b3dieGhqcG5jbmdtaHJiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzgxNjA5NDYsImV4cCI6MjA5MzczNjk0Nn0.BznhRvBPFi0yVqy1_ztQibRXVT2go8YX6YUMZ1HlJSQ

STEP 5: Run NPM (Keep this terminal running)
npm run dev

STEP 6: Open NEW Terminal and Start Server
php artisan serve

ACCESS THE APPLICATION
URL: http://127.0.0.1:8000
