-- Migration: add credit_used to reschedule_requests
-- Run once. Safe to re-run (IF NOT EXISTS guard on column).

ALTER TABLE `reschedule_requests`
    ADD COLUMN IF NOT EXISTS `credit_used` tinyint(1) NOT NULL DEFAULT 0
        COMMENT '1 = free rebook credit has been consumed';
