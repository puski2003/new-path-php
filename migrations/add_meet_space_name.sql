-- Migration: add meet_space_name to sessions
-- Run once against your database.

ALTER TABLE sessions
    ADD COLUMN meet_space_name VARCHAR(255) NULL
        AFTER meeting_link;
