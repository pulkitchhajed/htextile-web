-- ============================================================
--  HTextile — Step 2: Supabase SQL Migration Script
--  Copy ENTIRE contents → Paste into Supabase SQL Editor → Run
--  This is SAFE to run: adds indexes and fixes only.
--  No data is deleted or modified.
-- ============================================================

-- ── PART A: Performance Indexes ───────────────────────────────
-- These make your reports and searches significantly faster.

-- Bill Entry indexes
CREATE INDEX IF NOT EXISTS idx_bill_entry_supplier
    ON txt_bill_entry(supplier_account_code);

CREATE INDEX IF NOT EXISTS idx_bill_entry_buyer
    ON txt_bill_entry(buyer_account_code);

CREATE INDEX IF NOT EXISTS idx_bill_entry_bill_date
    ON txt_bill_entry(bill_date);

CREATE INDEX IF NOT EXISTS idx_bill_entry_voucher_date
    ON txt_bill_entry(voucher_date);

CREATE INDEX IF NOT EXISTS idx_bill_entry_delete_tag
    ON txt_bill_entry(delete_tag);

-- Payment Entry Main indexes
CREATE INDEX IF NOT EXISTS idx_payment_entry_buyer
    ON txt_payment_entry_main(buyer_account_code);

CREATE INDEX IF NOT EXISTS idx_payment_entry_supplier
    ON txt_payment_entry_main(supplier_account_code);

CREATE INDEX IF NOT EXISTS idx_payment_entry_vou_date
    ON txt_payment_entry_main(voucher_date);

CREATE INDEX IF NOT EXISTS idx_payment_entry_delete_tag
    ON txt_payment_entry_main(delete_tag);

-- Payment Bill Entry indexes
CREATE INDEX IF NOT EXISTS idx_pbe_bill_entry_id
    ON txt_payment_bill_entry(bill_entry_id);

CREATE INDEX IF NOT EXISTS idx_pbe_payment_entry_id
    ON txt_payment_bill_entry(payment_entry_id);

CREATE INDEX IF NOT EXISTS idx_pbe_delete_tag
    ON txt_payment_bill_entry(delete_tag);

-- Company indexes
CREATE INDEX IF NOT EXISTS idx_company_group_id
    ON txt_company(group_id);

CREATE INDEX IF NOT EXISTS idx_company_firm_type
    ON txt_company(firm_type);

CREATE INDEX IF NOT EXISTS idx_company_delete_tag
    ON txt_company(delete_tag);

-- Notes indexes
CREATE INDEX IF NOT EXISTS idx_notes_detail_main_id
    ON notes_detail(notes_main_id);

CREATE INDEX IF NOT EXISTS idx_notes_detail_delete_tag
    ON notes_detail(delete_tag);

CREATE INDEX IF NOT EXISTS idx_notes_main_delete_tag
    ON notes_main(delete_tag);

-- Commission Bill Entry indexes
CREATE INDEX IF NOT EXISTS idx_comm_bill_supplier
    ON txt_commission_bill_entry(supplier_account_code);

CREATE INDEX IF NOT EXISTS idx_comm_bill_agent
    ON txt_commission_bill_entry(agent_account_code);

CREATE INDEX IF NOT EXISTS idx_comm_bill_date
    ON txt_commission_bill_entry(commission_bill_date);

-- Group Master index
CREATE INDEX IF NOT EXISTS idx_group_master_type
    ON txt_group_master(group_type);

-- Login index (speeds up login lookup)
CREATE INDEX IF NOT EXISTS idx_login_name
    ON txt_login(login_name);

-- ── PART B: Fix remarks default value ────────────────────────
-- Change remarks default from '0' to '' (empty string)
ALTER TABLE txt_bill_entry
    ALTER COLUMN remarks SET DEFAULT '';

-- ── PART C: Fix active column default in txt_login ───────────
-- Ensure new users are inactive by default until enabled
ALTER TABLE txt_login
    ALTER COLUMN active SET DEFAULT 0;

-- ── PART D: Verify indexes were created ──────────────────────
-- Run this separately to confirm all indexes exist:
-- SELECT indexname, tablename FROM pg_indexes
-- WHERE schemaname = 'public'
-- ORDER BY tablename, indexname;

-- ============================================================
--  END OF SCRIPT
--  ✅ Safe to run — no data was deleted or modified
-- ============================================================
