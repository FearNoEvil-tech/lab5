# Lab 5 Notes: Build and Query the Peer Tutoring Hub Database

Cody Osmond, IS 6465

All SQL for this lab is in `tutoring.sql`, labeled by part. I ran the whole script from the project folder with `sqlite3 tutoring.db < tutoring.sql`. The script turns on `.headers on` and `.mode table` so the results print as tables, and it drops and recreates both tables first, so it can be rerun and the request IDs stay the same. The output below is pasted from that run.

The script also turns on `PRAGMA foreign_keys = ON;`. SQLite does not enforce foreign keys unless this setting is on for the connection, so without it the database would accept a request for a student who does not exist.

## Part 1: Create the database tables

```sql
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
```

**Output of `.tables` and `.schema`:**

```text
sqlite> .tables
students           tutoring_requests
sqlite> .schema
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
```

**Which column is the primary key in each table?**
In `students`, the primary key is `student_id`. In `tutoring_requests`, the primary key is `request_id`. Each is declared `INTEGER PRIMARY KEY`, so SQLite assigns the next number automatically when a row is inserted without one.

**Which column is the foreign key, and what relationship does it represent?**
The foreign key is `student_id` in `tutoring_requests`, which references `students(student_id)`. It represents a one-to-many relationship: one student can submit many tutoring requests, but each request belongs to exactly one student. Because the column is also `NOT NULL`, a request cannot be saved without a student.

**Why is student_email marked UNIQUE?**
An email address identifies one person, so two student rows should never share it. The `UNIQUE` constraint makes the database reject a second row with the same email, which prevents duplicate student records. If duplicates were allowed, a student's requests could be split across two rows. The constraint check at the end of this file shows the error SQLite returns.

## Part 2: Insert students and tutoring requests

```sql
-- Each student is inserted once (Jordan gets student_id 1, Avery gets 2)
INSERT INTO students (student_name, student_email)
VALUES ('Jordan Lee', 'jordan.lee@example.edu');

INSERT INTO students (student_name, student_email)
VALUES ('Avery Patel', 'avery.patel@example.edu');

-- status and created_at are left out so their defaults are used
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'Forms', '2026-11-16', '14:00');          -- Jordan, request 1

INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (1, 'IS 6465', 'SQL SELECT', '2026-11-18', '10:00');     -- Jordan, request 2

INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (2, 'IS 6465', 'SQLite CRUD', '2026-11-17', '16:00');    -- Avery, request 3
```

**Rows in both tables after the inserts:**

```text
sqlite> SELECT * FROM students;
+------------+--------------+-------------------------+
| student_id | student_name |      student_email      |
+------------+--------------+-------------------------+
| 1          | Jordan Lee   | jordan.lee@example.edu  |
| 2          | Avery Patel  | avery.patel@example.edu |
+------------+--------------+-------------------------+
sqlite> SELECT * FROM tutoring_requests;
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+
| request_id | student_id | course  |    topic    | preferred_date | preferred_time | status |     created_at      |
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+
| 1          | 1          | IS 6465 | Forms       | 2026-11-16     | 14:00          | Open   | 2026-11-08 00:18:34 |
| 2          | 1          | IS 6465 | SQL SELECT  | 2026-11-18     | 10:00          | Open   | 2026-11-08 00:18:34 |
| 3          | 2          | IS 6465 | SQLite CRUD | 2026-11-17     | 16:00          | Open   | 2026-11-08 00:18:34 |
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+
```

Every request received a status of `Open` and a `created_at` timestamp from the column defaults. `CURRENT_TIMESTAMP` records the time in UTC, so the timestamps are several hours ahead of my local Pacific time (late in the afternoon they already show the next day's date).

**Why does Jordan have one student row but two request rows?**
The `students` table stores facts about the person, such as the name and email, and those facts are the same no matter how many requests Jordan submits. The `tutoring_requests` table stores one row for each request. Both of Jordan's requests use `student_id` 1 to point back to the same student row, so the name and email are stored only once. If I repeated Jordan's student row, the `UNIQUE` email constraint would reject it, and a change to Jordan's email would otherwise have to be made in more than one place.

## Part 3: Read the stored data

```sql
-- All rows in students
SELECT * FROM students;

-- All rows in tutoring_requests
SELECT * FROM tutoring_requests;

-- Only Open requests
SELECT * FROM tutoring_requests
WHERE status = 'Open';

-- One request by its request_id
SELECT * FROM tutoring_requests
WHERE request_id = 2;
```

```text
Open requests only:
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+
| request_id | student_id | course  |    topic    | preferred_date | preferred_time | status |     created_at      |
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+
| 1          | 1          | IS 6465 | Forms       | 2026-11-16     | 14:00          | Open   | 2026-11-08 00:18:34 |
| 2          | 1          | IS 6465 | SQL SELECT  | 2026-11-18     | 10:00          | Open   | 2026-11-08 00:18:34 |
| 3          | 2          | IS 6465 | SQLite CRUD | 2026-11-17     | 16:00          | Open   | 2026-11-08 00:18:34 |
+------------+------------+---------+-------------+----------------+----------------+--------+---------------------+

One request by request_id:
+------------+------------+---------+------------+----------------+----------------+--------+---------------------+
| request_id | student_id | course  |   topic    | preferred_date | preferred_time | status |     created_at      |
+------------+------------+---------+------------+----------------+----------------+--------+---------------------+
| 2          | 1          | IS 6465 | SQL SELECT | 2026-11-18     | 10:00          | Open   | 2026-11-08 00:18:34 |
+------------+------------+---------+------------+----------------+----------------+--------+---------------------+
```

All three requests appear in the Open query because none had been updated yet.

**JOIN query (request list ordered by preferred_date):**

```sql
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
```

```text
+------------+--------------+-------------------------+---------+-------------+----------------+----------------+--------+
| request_id | student_name |      student_email      | course  |    topic    | preferred_date | preferred_time | status |
+------------+--------------+-------------------------+---------+-------------+----------------+----------------+--------+
| 1          | Jordan Lee   | jordan.lee@example.edu  | IS 6465 | Forms       | 2026-11-16     | 14:00          | Open   |
| 3          | Avery Patel  | avery.patel@example.edu | IS 6465 | SQLite CRUD | 2026-11-17     | 16:00          | Open   |
| 2          | Jordan Lee   | jordan.lee@example.edu  | IS 6465 | SQL SELECT  | 2026-11-18     | 10:00          | Open   |
+------------+--------------+-------------------------+---------+-------------+----------------+----------------+--------+
```

**What does the JOIN condition connect?**
The condition `r.student_id = s.student_id` matches each tutoring request to the student row with the same `student_id`. That lets one result row show the request details from `tutoring_requests` together with the student's name and email from `students`.

**What is the difference between the purpose of JOIN and WHERE?**
A `JOIN` combines columns from two tables by matching related rows, so it decides how the tables fit together. A `WHERE` clause filters rows, so it decides which rows are kept in the result. In this lab, the JOIN adds the student's name and email to each request, and a WHERE clause such as `WHERE status = 'Open'` limits the results to the requests that meet a condition.

**Why should the application retrieve one request by request_id instead of by topic alone?**
`request_id` is the primary key, so it is guaranteed to identify exactly one request. A topic is not unique: two students could both request help with "Forms," and one student could ask about the same topic twice. Searching by topic could return several rows or the wrong one, and the topic can change, as it did in Part 4. The ID never changes, which is why the details, edit, and delete pages pass `request_id` in the URL.

## Part 4: Update one request

```sql
-- Revise one of Jordan's open requests: new topic, date, and time
UPDATE tutoring_requests
SET topic = 'Forms and validation',
    preferred_date = '2026-11-19',
    preferred_time = '15:30'
WHERE request_id = 1;

-- Cancel Avery's request instead of deleting it
UPDATE tutoring_requests
SET status = 'Cancelled'
WHERE request_id = 3;
```

**Jordan's request 1, before and after the update:**

```text
Before:
+------------+------------+-------+----------------+----------------+--------+
| request_id | student_id | topic | preferred_date | preferred_time | status |
+------------+------------+-------+----------------+----------------+--------+
| 1          | 1          | Forms | 2026-11-16     | 14:00          | Open   |
+------------+------------+-------+----------------+----------------+--------+

After:
+------------+------------+----------------------+----------------+----------------+--------+
| request_id | student_id |        topic         | preferred_date | preferred_time | status |
+------------+------------+----------------------+----------------+----------------+--------+
| 1          | 1          | Forms and validation | 2026-11-19     | 15:30          | Open   |
+------------+------------+----------------------+----------------+----------------+--------+
```

**Avery's request after the second update:**

```text
+------------+--------------+-------------+-----------+
| request_id | student_name |    topic    |  status   |
+------------+--------------+-------------+-----------+
| 3          | Avery Patel  | SQLite CRUD | Cancelled |
+------------+--------------+-------------+-----------+
```

**Why is a specific WHERE clause essential in an UPDATE statement?**
Without a `WHERE` clause, an `UPDATE` changes every row in the table. My first update would have given all of the requests the same topic, date, and time, and my second would have cancelled every request instead of only Avery's. Using `WHERE request_id = ...` limits the change to exactly one row, because the primary key matches only one request.

**Why does the Peer Tutoring Hub use a Cancelled status instead of immediately deleting a real request?**
Changing the status keeps the record. The hub keeps a history of what was requested, when, and by whom, which is useful for reports, for scheduling tutors, and for answering a student's questions later. A cancelled request can also be reopened if it was cancelled by mistake, while a deleted row is gone. In the application, the "Cancel request" button leads to a confirmation page, and the status change is the action that runs after the student confirms.

## Part 5: Delete one test request

```sql
-- A clearly labeled test record (it becomes request_id 4)
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (2, 'IS 6465', 'Delete Practice', '2026-11-20', '09:00');

-- Remove only the test record, by its primary key
DELETE FROM tutoring_requests
WHERE request_id = 4;
```

```text
Before the delete:
+------------+------------+----------------------+-----------+
| request_id | student_id |        topic         |  status   |
+------------+------------+----------------------+-----------+
| 1          | 1          | Forms and validation | Open      |
| 2          | 1          | SQL SELECT           | Open      |
| 3          | 2          | SQLite CRUD          | Cancelled |
| 4          | 2          | Delete Practice      | Open      |
+------------+------------+----------------------+-----------+

After the delete:
+------------+------------+----------------------+-----------+
| request_id | student_id |        topic         |  status   |
+------------+------------+----------------------+-----------+
| 1          | 1          | Forms and validation | Open      |
| 2          | 1          | SQL SELECT           | Open      |
| 3          | 2          | SQLite CRUD          | Cancelled |
+------------+------------+----------------------+-----------+
```

The test record (request 4, "Delete Practice") is gone, and requests 1, 2, and 3 remain unchanged.

**Why is a WHERE clause essential in a DELETE statement?**
`DELETE FROM tutoring_requests;` with no `WHERE` clause removes every row in the table, and SQLite does not ask for confirmation. The `WHERE request_id = 4` clause limits the delete to the one test record. Before running a delete, it is good practice to run a `SELECT` with the same `WHERE` clause to confirm exactly which rows will be removed, which is what the "before" query above does.

## Part 6: Reflect on the CRUD workflow

| CRUD action | SQL statement used in this lab | Peer Tutoring Hub example |
|---|---|---|
| Create | `INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time) VALUES (1, 'IS 6465', 'Forms', '2026-11-16', '14:00');` | A student submits the request form, and the request is saved with a status of Open. |
| Read | `SELECT ... FROM tutoring_requests AS r JOIN students AS s ON r.student_id = s.student_id ORDER BY r.preferred_date;` | The requests page lists every request with the student's name and email, and the details page shows one request by `request_id`. |
| Update | `UPDATE tutoring_requests SET topic = 'Forms and validation', preferred_date = '2026-11-19', preferred_time = '15:30' WHERE request_id = 1;` | A student edits a request, or cancels it with `SET status = 'Cancelled'`. |
| Delete | `DELETE FROM tutoring_requests WHERE request_id = 4;` | An administrator removes a test or duplicate record that is not a real request. |

**How does the database preserve the connection between a student and that student's requests?**
Each row in `tutoring_requests` stores the `student_id` of the student who submitted it, and the `FOREIGN KEY` constraint requires that value to match an existing `student_id` in `students`. With foreign keys turned on, SQLite rejects a request for a student who does not exist, as the check below shows. It also refuses to delete a student who still has requests, so a request is never left pointing to a missing student.

**Which database constraint prevents two student rows from using the same email address?**
The `UNIQUE` constraint on `student_email` prevents it. When I tried to insert a second student with Jordan's email, SQLite rejected the row with the error shown below.

**What could happen if an UPDATE or DELETE statement did not include a WHERE clause?**
The statement would affect every row in the table. An `UPDATE` without `WHERE` would overwrite every request with the same values, for example cancelling every request at once, and a `DELETE` without `WHERE` would erase all of the requests. SQLite runs these statements without a warning, and there is no undo unless a backup exists.

**When the application later receives values from an HTML form, why should PHP send those values as parameters instead of placing them directly into SQL text?**
If PHP builds the SQL by joining form values into the text, a user can type SQL into a field and change the meaning of the statement. This is called SQL injection. For example, a topic such as `x'; DELETE FROM tutoring_requests; --` could close the quote and run a statement the application never intended. With a prepared statement, the SQL is written with placeholders such as `?` or `:topic`, and the values are sent separately with `bindValue()` or `execute()`. The database always treats them as data, never as SQL. Parameters also handle quotes in ordinary input correctly, such as the name O'Brien.

### Constraint checks

The end of `tutoring.sql` runs two inserts that are designed to fail, to show that the constraints work:

```sql
-- UNIQUE: a second student row with Jordan's email
INSERT INTO students (student_name, student_email)
VALUES ('Jordan Lee Duplicate', 'jordan.lee@example.edu');

-- FOREIGN KEY: a request for a student who does not exist
INSERT INTO tutoring_requests (student_id, course, topic, preferred_date, preferred_time)
VALUES (99, 'IS 6465', 'No such student', '2026-11-21', '11:00');
```

```text
Runtime error near line 171: UNIQUE constraint failed: students.student_email (19)
Runtime error near line 176: FOREIGN KEY constraint failed (19)

Row counts after the failed inserts:
+----------+----------+
| students | requests |
+----------+----------+
| 2        | 3        |
+----------+----------+
```

Both inserts were rejected, so the tables still have 2 students and 3 requests. Because these two statements fail on purpose, `sqlite3` reports errors when the script runs; every other statement completes normally.

## AI use

I used Claude (Anthropic) to help with this lab. Claude wrote and ran `tutoring.sql`, captured the output pasted above, and drafted these answers from the results. I reviewed the SQL and the answers against the Week 6 material and reran the script in my Codespace. I did not change any of the provided PHP application files.
