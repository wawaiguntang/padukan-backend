# Profile Module Database Schema Design

## Overview
Modul Profile menggunakan sistem polymorphic untuk menangani berbagai tipe profile (Driver, Merchant, Customer) dengan data global di tabel `profiles` dan data spesifik di tabel masing-masing.

## Tables and Fields

### 1. `profiles` (Global Profile)
- `id` (string, UUID, primary key)
- `user_id` (string, foreign key ke users.id)
- `first_name` (string, nullable)
- `last_name` (string, nullable)
- `avatar` (string, path file, nullable)
- `gender` (enum: male/female/other, nullable)
- `language` (string, default 'id' - setting bahasa)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 2. `addresses` (Personal Addresses - Multiple per Profile)
- `id` (string, UUID, primary key)
- `profile_id` (string, foreign key ke profiles.id)
- `type` (enum: home/work/business/other)
- `label` (string)
- `street` (string)
- `city` (string)
- `province` (string)
- `postal_code` (string)
- `latitude` (decimal, 10,8)
- `longitude` (decimal, 11,8)
- `is_primary` (boolean, default false)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 3. `banks` (Master Bank)
- `id` (string, UUID, primary key)
- `name` (string)
- `code` (string, unique)
- `is_active` (boolean, default true)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 4. `driver_profiles` (Driver Profile)
- `id` (string, UUID, primary key)
- `profile_id` (string, foreign key ke profiles.id)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 5. `driver_vehicles` (Driver Vehicles)
- `id` (string, UUID, primary key)
- `driver_profile_id` (string, foreign key ke driver_profiles.id)
- `type` (enum: motorcycle/car)
- `brand` (string)
- `model` (string)
- `year` (integer)
- `color` (string)
- `license_plate` (string)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 6. `driver_documents` (Driver Documents)
- `id` (string, UUID, primary key)
- `driver_profile_id` (string, foreign key ke driver_profiles.id)
- `type` (enum: id_card/sim/stnk/vehicle_photo)
- `file_path` (string)
- `file_name` (string)
- `mime_type` (string)
- `file_size` (integer)
- `meta` (json) - lihat contoh di bawah
- `expiry_date` (date, nullable)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `verified_at` (timestamp, nullable)
- `verified_by` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Contoh Meta untuk driver_documents:**
- `id_card`: `{"number": "1234567890123456", "expiry_date": "2025-12-31"}`
- `sim`: `{"number": "123456789", "expiry_date": "2025-12-31"}`
- `stnk`: `{"number": "B1234ABC", "expiry_date": "2025-12-31"}`
- `vehicle_photo`: `{}` (kosong atau data tambahan jika diperlukan)

### 7. `merchant_profiles` (Merchant Profile)
- `id` (string, UUID, primary key)
- `profile_id` (string, foreign key ke profiles.id)
- `business_name` (string)
- `business_type` (enum: food/mart)
- `business_phone` (string)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 8. `merchant_banks` (Merchant Banks)
- `id` (string, UUID, primary key)
- `merchant_profile_id` (string, foreign key ke merchant_profiles.id)
- `bank_id` (string, foreign key ke banks.id)
- `account_number` (string)
- `is_primary` (boolean, default false)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 9. `merchant_addresses` (Merchant Business Address - One-to-One)
- `id` (string, UUID, primary key)
- `merchant_profile_id` (string, foreign key ke merchant_profiles.id, unique)
- `street` (string)
- `city` (string)
- `province` (string)
- `postal_code` (string)
- `latitude` (decimal, 10,8)
- `longitude` (decimal, 11,8)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 10. `merchant_documents` (Merchant Documents)
- `id` (string, UUID, primary key)
- `merchant_profile_id` (string, foreign key ke merchant_profiles.id)
- `type` (enum: id_card/store)
- `file_path` (string)
- `file_name` (string)
- `mime_type` (string)
- `file_size` (integer)
- `meta` (json) - lihat contoh di bawah
- `expiry_date` (date, nullable)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `verified_at` (timestamp, nullable)
- `verified_by` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Contoh Meta untuk merchant_documents:**
- `id_card`: `{"number": "1234567890123456", "expiry_date": "2025-12-31"}`
- `store`: `{}`

### 11. `customer_profiles` (Customer Profile)
- `id` (string, UUID, primary key)
- `profile_id` (string, foreign key ke profiles.id)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### 12. `customer_documents` (Customer Documents)
- `id` (string, UUID, primary key)
- `customer_profile_id` (string, foreign key ke customer_profiles.id)
- `type` (enum: id_card/other)
- `file_path` (string)
- `file_name` (string)
- `mime_type` (string)
- `file_size` (integer)
- `meta` (json) - lihat contoh di bawah
- `expiry_date` (date, nullable)
- `is_verified` (boolean, default false)
- `verification_status` (enum: pending/approved/rejected)
- `verified_at` (timestamp, nullable)
- `verified_by` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Contoh Meta untuk customer_documents:**
- `id_card`: `{"number": "1234567890123456", "expiry_date": "2025-12-31"}`
- `other`: `{}` (flexible untuk dokumen lain)

## Notes
- Semua tabel menggunakan UUID untuk primary key.
- Field `meta` (json) digunakan untuk menyimpan data tambahan seperti nomor dokumen dan tanggal expiry.
- Relasi: User -> Profile -> Specific Profile (Driver/Merchant/Customer) dengan sub-tabel masing-masing.
- Addresses terpisah untuk personal (multiple) dan business (one-to-one untuk merchant).
- Documents terpisah per tipe profile untuk fleksibilitas dan verifikasi.