-- ============================================================
--  HTextile — Step 3: Supabase Sessions Table
--  Required for running PHP on Vercel
--  Run this in Supabase SQL Editor
-- ============================================================

CREATE TABLE IF NOT EXISTS public.txt_sessions (
    id VARCHAR(128) NOT NULL,
    data TEXT NOT NULL,
    timestamp INTEGER NOT NULL,
    PRIMARY KEY (id)
);

CREATE INDEX IF NOT EXISTS idx_sessions_timestamp ON public.txt_sessions (timestamp);

-- Optional: Function to clean up old sessions (e.g. older than 24 hours)
-- Vercel doesn't have cron out of the box, so we can trigger this periodically or let an external cron do it.
CREATE OR REPLACE FUNCTION clean_expired_sessions() RETURNS void AS $$
BEGIN
  DELETE FROM public.txt_sessions WHERE timestamp < extract(epoch FROM now()) - 86400;
END;
$$ LANGUAGE plpgsql;
