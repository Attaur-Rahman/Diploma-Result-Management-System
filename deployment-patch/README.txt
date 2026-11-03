BTE Results - Deployment Patch Files
====================================

CONTENTS
--------
1. modified-existing/  -> The 9 files that were CHANGED for deployment readiness.
   Replace the corresponding files in your local repository with these:

   src/Components/HomePage.jsx              (LAN IP -> relative URLs /bte/Results, /bte/generate_pdf)
   src/Components/Navbar.jsx                (LAN IP -> /bte/AdminLoginPage)
   bte/Results.php                          (hardcoded DB creds -> require db_config.php)
   bte/adminlogin.php                       (hardcoded DB creds -> require db_config.php)
   bte/generate_pdf.php                     (hardcoded DB creds -> require db_config.php)
   bte/AdminDashboard/Backend/connectdb.php (hardcoded DB creds -> require ../../db_config.php)
   bte/AdminLoginPage.php                   (localhost:3000 back-link -> /)
   bte/AdminDashboard/Dashboard.php         (localhost:3000 back-link -> /)
   .gitignore                               (added /build, .DS_Store, .env* patterns)

2. new/db_config.php  -> NEW file. Place it at:  bte/db_config.php
   It reads env vars DB_HOST / DB_USER / DB_PASS / DB_NAME and falls back to
   XAMPP defaults (localhost / root / admin / bte). For shared hosting
   (e.g. InfinityFree), edit the fallback values to match your MySQL details.

3. deleted-backup/*.orig -> Original copies of all changed files (from git main)
   so you can diff or revert if needed.

HOW TO APPLY
------------
From your repo root:
    cp -r path/to/deployment-patch/modified-existing/* .
    cp path/to/deployment-patch/new/db_config.php bte/db_config.php
Then rebuild the frontend:
    npm install
    npm run build
(No .env file is required; db_config.php already has safe defaults.)

Local test: copy bte/ into C:\xampp\htdocs\, serve the build/ folder as htdocs root,
import bte.sql via phpMyAdmin, open http://localhost/, search Reg No 323CS21016 Sem 1.
