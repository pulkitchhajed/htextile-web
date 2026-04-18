## 1. Database Schema (MySQL)

```sql
-- SQL to create or update offer_letters and offer_letter_details tables

CREATE TABLE IF NOT EXISTS offer_letters (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier VARCHAR(255) NOT NULL,
    buyer VARCHAR(255) NOT NULL,
    buyer_gst VARCHAR(50),
    transport VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS offer_letter_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    offer_id INT NOT NULL,
    quality VARCHAR(255) NOT NULL,
    meter VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (offer_id) REFERENCES offer_letters(offer_id) ON DELETE CASCADE
);

-- If the offer_letters table exists but missing columns, alter table to add them
ALTER TABLE offer_letters
    ADD COLUMN IF NOT EXISTS supplier VARCHAR(255) NOT NULL,
    ADD COLUMN IF NOT EXISTS buyer VARCHAR(255) NOT NULL,
    ADD COLUMN IF NOT EXISTS buyer_gst VARCHAR(50),
    ADD COLUMN IF NOT EXISTS transport VARCHAR(255),
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

---

## 2. Database Alter Statements

```sql
-- Alter price column in offer_letter_details to VARCHAR(50)
ALTER TABLE offer_letter_details MODIFY COLUMN price VARCHAR(50) NOT NULL;

-- Add pdf_path column to offer_letters table
ALTER TABLE offer_letters
ADD COLUMN pdf_path VARCHAR(255) NULL AFTER created_at;

-- Add UNIQUE constraint to offer_number column in offer_letters table
ALTER TABLE offer_letters
ADD UNIQUE (offer_number);
```

---

## 3. Save Offer Letter (save_offer_letter.php)

## 4. Edit Offer Letter (edit_offer_letter.php)

- Handles both displaying the edit form and processing updates.
- Updates offer_letters and offer_letter_details tables.
- Layout and form fields consistent with Add Offer form.

---

## 5. Search Offer Letters (search_offer_letter.php)

- Handles search by supplier, buyer, or offer number.
- Combines supplier and buyer criteria with OR.
- Displays results in a table with edit, view, and delete options.

---

This notebook serves as a reference for the offer letter backend implementation and database schema changes.
