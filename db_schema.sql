-- SQL FILE: pwd_employment_system.sql
-- SYSTEM: PWD Employment Information System for Antique

CREATE DATABASE IF NOT EXISTS pwd_employment_antique
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pwd_employment_antique;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS employer_otps;
DROP TABLE IF EXISTS pwd_otps;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS job_applications;
DROP TABLE IF EXISTS job_postings;
DROP TABLE IF EXISTS employers;
DROP TABLE IF EXISTS official_pwd_ids;
DROP TABLE IF EXISTS pwd_profiles;
DROP TABLE IF EXISTS admin_users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admin_users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('SUPER','PWD_ADMIN','EMPLOYER_ADMIN') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    last_login_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pwd_profiles (
    pwd_number VARCHAR(20) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    sex ENUM('Male','Female','Prefer not to say') NOT NULL,
    birthdate DATE NOT NULL,
    province VARCHAR(50) NOT NULL DEFAULT 'Antique',
    municipality VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    contact_number VARCHAR(20),
    disability_category ENUM('Blind','Deaf','Physical Disability') NOT NULL,
    blind_type ENUM('Total blindness','Low vision','Color blindness','Blind with light perception') NULL,
    deaf_type ENUM('Deaf (cannot hear)','Hard of hearing','Deaf with speech difficulty') NULL,
    physical_type ENUM('Wheelchair user','Amputee','Paralyzed','Cerebral palsy','Other physical disability') NULL,
    cause_of_disability ENUM('Congenital','Illness','Accident','Work-related injury','Age-related','Others') NOT NULL,
    educational_level ENUM('No formal education','Elementary','High School','Senior High School','Vocational / TESDA','College Level','College Graduate') NOT NULL,
    employment_status ENUM('Employed - Regular','Employed - Part-time','Self-employed','Unemployed','Student','Unable to work') NOT NULL,
    guardian_name VARCHAR(150),
    guardian_relationship ENUM('Mother','Father','Sister','Brother','Spouse','Relative','Legal Guardian','Others') NULL,
    guardian_contact VARCHAR(20),
    pwd_photo_front VARCHAR(255) NOT NULL,
    pwd_photo_back VARCHAR(255) NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    verification_status ENUM('Valid','Invalid','Not Found','Mismatched','Blocked') DEFAULT 'Not Found',
    verification_message VARCHAR(255),
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE official_pwd_ids (
    pwd_number VARCHAR(20) PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    birthdate DATE NOT NULL,
    status ENUM('Active','Expired','Blocked') DEFAULT 'Active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employers (
    business_permit_no VARCHAR(20) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    date_issued DATE NOT NULL,
    valid_until DATE NOT NULL,
    business_name VARCHAR(200) NOT NULL,
    registered_owner VARCHAR(150) NOT NULL,
    business_address TEXT NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    city_municipality VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    zip_code VARCHAR(10),
    type_of_business VARCHAR(150) NOT NULL,
    line_of_business VARCHAR(150) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE job_postings (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    business_permit_no VARCHAR(20) NOT NULL,
    disability_target ENUM('Blind','Deaf','Physical Disability') NOT NULL,
    job_title VARCHAR(150) NOT NULL,
    job_description TEXT,
    job_location VARCHAR(200),
    salary_range VARCHAR(100),
    max_hires INT NOT NULL DEFAULT 1,
    hired_count INT NOT NULL DEFAULT 0,
    application_deadline DATE NULL,
    date_posted DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Open','Closed','Expired') NOT NULL DEFAULT 'Open',
    FOREIGN KEY (business_permit_no) REFERENCES employers(business_permit_no) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE job_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    pwd_number VARCHAR(20) NOT NULL,
    date_applied DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
    status_updated_at DATETIME NULL,
    UNIQUE KEY uniq_job_pwd (job_id, pwd_number),
    FOREIGN KEY (job_id) REFERENCES job_postings(job_id) ON DELETE CASCADE,
    FOREIGN KEY (pwd_number) REFERENCES pwd_profiles(pwd_number) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('PWD','EMPLOYER','ADMIN') NOT NULL,
    pwd_number VARCHAR(20) NULL,
    business_permit_no VARCHAR(20) NULL,
    admin_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME NULL,
    FOREIGN KEY (pwd_number) REFERENCES pwd_profiles(pwd_number) ON DELETE CASCADE,
    FOREIGN KEY (business_permit_no) REFERENCES employers(business_permit_no) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admin_users(admin_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pwd_otps (
    otp_id INT AUTO_INCREMENT PRIMARY KEY,
    pwd_number VARCHAR(20) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    used_at DATETIME NULL,
    FOREIGN KEY (pwd_number) REFERENCES pwd_profiles(pwd_number) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employer_otps (
    otp_id INT AUTO_INCREMENT PRIMARY KEY,
    business_permit_no VARCHAR(20) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    used_at DATETIME NULL,
    FOREIGN KEY (business_permit_no) REFERENCES employers(business_permit_no) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(admin_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
