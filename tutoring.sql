-- Lab 5: Build and Query the Peer Tutoring Hub Database
-- Cody Osmond, IS 6465
--
-- Run the whole script from the project folder:
--     sqlite3 tutoring.db < tutoring.sql
-- or from inside the SQLite shell:
--     sqlite3 tutoring.db
--     sqlite> .read tutoring.sql
--
-- The script starts from empty tables each time, so it can be rerun safely
-- and the request_id values are always the same (1, 2, 3, and 4 for the test).

-- Show column names and draw results as tables in the terminal
.headers on
.mode table

-- SQLite does not check foreign keys unless this is turned on for the connection
PRAGMA foreign_keys = ON;

-- Start clean: drop the child table first, then the parent table
DROP TABLE IF EXISTS tutoring_requests;
DROP TABLE IF EXISTS students;


-- =====================================================================
-- Part 1: Create tables
-- =====================================================================

CREATE TABLE students (
  student_id    INTEGER PRIMARY KEY,
  student_name  TEXT NOT NULL,
  student_email TEXT NOT NULL UNIQUE
);

CREATE TABLE tutoring_requests (
  request_id     INTEGER PRIMARY KEY,
  student_id     INTEGER NOT NULL,
  course         TEXT NOT NULL,
  topic          TEXT NOT NULL,
  preferred_date TEXT NOT NULL,              -- YYYY-MM-DD
  preferred_time TEXT NOT NULL,              -- HH:MM, 24-hour clock
  status         TEXT NOT NULL DEFAULT 'Open',
  created_at     TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

.print '--- Part 1: .tables'
.tables
.print '--- Part 1: .schema'
.schema


-- =====================================================================
-- Part 2: Insert students and tutoring requests
-- =====================================================================

-- Each student is inserted once (Jordan gets student_id 1, Avery gets 2)
INSERT INTO students (student_name, student_email)
VALUES ('Jordan Lee', 'jordan.lee@example.edu');

INSERT INTO students (student_name, student_email)
VALUES ('Avery Patel', 'avery.patel@example.edu');

-- Requests point to their student through student_id.
-- status and created_at are left out so their defaults ('Open' and the current timestamp) are used.
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'Forms', '2026-11-16', '14:00');          -- Jordan, request 1

INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'SQL SELECT', '2026-11-18', '10:00');     -- Jordan, request 2

INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (2, 'IS 6465', 'SQLite CRUD', '2026-11-17', '16:00');    -- Avery, request 3


-- =====================================================================
-- Part 3: Read the stored data
-- =====================================================================

.print '--- Part 3: All students'
SELECT * FROM students;

.print '--- Part 3: All tutoring requests'
SELECT * FROM tutoring_requests;

.print '--- Part 3: Open requests only'
SELECT * FROM tutoring_requests
WHERE status = 'Open';

.print '--- Part 3: One request by request_id'
SELECT * FROM tutoring_requests
WHERE request_id = 2;

.print '--- Part 3: Request list (JOIN), ordered by preferred_date'
SELECT
  r.request_id,
  s.student_name,
  s.student_email,
  r.course,
  r.topic,
  r.preferred_date,
  r.preferred_time,
  r.status
FROM tutoring_requests AS r
JOIN students AS s ON r.student_id = s.student_id
ORDER BY r.preferred_date;


-- =====================================================================
-- Part 4: Update one request
-- =====================================================================

.print '--- Part 4: Jordan request 1 BEFORE the update'
SELECT request_id, student_id, topic, preferred_date, preferred_time, status
FROM tutoring_requests
WHERE request_id = 1;

-- Revise one of Jordan's open requests: new topic, date, and time
UPDATE tutoring_requests
SET topic = 'Forms and validation',
    preferred_date = '2026-11-19',
    preferred_time = '15:30'
WHERE request_id = 1;

.print '--- Part 4: Jordan request 1 AFTER the update'
SELECT request_id, student_id, topic, preferred_date, preferred_time, status
FROM tutoring_requests
WHERE request_id = 1;

-- Cancel Avery's request instead of deleting it
UPDATE tutoring_requests
SET status = 'Cancelled'
WHERE request_id = 3;

.print '--- Part 4: Avery request after cancelling'
SELECT r.request_id, s.student_name, r.topic, r.status
FROM tutoring_requests AS r
JOIN students AS s ON r.student_id = s.student_id
WHERE r.request_id = 3;


-- =====================================================================
-- Part 5: Delete one test request
-- =====================================================================

-- A clearly labeled test record (it becomes request_id 4)
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (2, 'IS 6465', 'Delete Practice', '2026-11-20', '09:00');

.print '--- Part 5: Requests BEFORE the delete'
SELECT request_id, student_id, topic, status
FROM tutoring_requests
ORDER BY request_id;

-- Remove only the test record, by its primary key
DELETE FROM tutoring_requests
WHERE request_id = 4;

.print '--- Part 5: Requests AFTER the delete (test record gone, others remain)'
SELECT request_id, student_id, topic, status
FROM tutoring_requests
ORDER BY request_id;


-- =====================================================================
-- Constraint checks (each statement below is expected to FAIL)
-- =====================================================================

-- UNIQUE: a second student row with Jordan's email is rejected
.print '--- Check: duplicate email (expect a UNIQUE constraint error)'
INSERT INTO students (student_name, student_email)
VALUES ('Jordan Lee Duplicate', 'jordan.lee@example.edu');

-- FOREIGN KEY: a request for a student who does not exist is rejected
.print '--- Check: request for student 99 (expect a FOREIGN KEY constraint error)'
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (99, 'IS 6465', 'No such student', '2026-11-21', '11:00');

.print '--- Check: row counts after the failed inserts'
SELECT (SELECT COUNT(*) FROM students) AS students,
       (SELECT COUNT(*) FROM tutoring_requests) AS requests;
