
# Project Scope

## 1. Platforms Included

### Web Portal

Accessible by:

* Super Admin
* Sub Admins (Event Management)
* School Admins
* Judges

### Mobile Applications

* School Admin Mobile App
* Judge Portal Mobile App
* Sub Admin Mobile Access (limited operational use)

*Mobile UI design is included as part of the project scope.*

---

## 2. User Roles & Access Overview

### Super Admin

* Full system control
* Event, competition, criteria, and role management
* School approvals & payment verification
* Reporting & exports

### Sub Admin (Event Management)

* Mobile + Web access
* View schedules, rosters, and draws
* Mark attendance
* Verify students on-ground
* Assign referees/umpires and manage match rosters

### School Admin

* Web + Mobile access
* Manage students (manual entry only)
* Register participants
* View invoices & registration status
* View rosters and event schedules

### Judges

* Web + Mobile access
* Academic events only
* View assigned competitions
* Enter scores based on predefined rubrics

---

## 3. Competition Structure

The competition framework will follow a standardized hierarchy:

* Junior Category
* Senior Category

Each category will be divided into:

* Individual
* Team

Each competition will have:

* Defined minimum and maximum age limits
* Assigned event type (Academic or Sports)

Per-school quota will be managed under **School Allowance** rather than within competition definitions.

The system will also support creating and managing categories within events and competitions based on subject, competition type, skill level, or other organizer-defined criteria.

---

## 4. Registration & Student Management Rules

* Students will be added manually by the School Admin (bulk upload not available).
* Schools will upload official verification documents (e.g., Cambridge affiliation certificate or Matriculation board certificate) during registration.
* Upon approval, the system will automatically generate a unique **School ID**.
* The School ID will be used for:

  * Student registrations
  * Payments
  * Reporting
  * Internal system references

---

## 5. Payments & Confirmation Flow

* The application will generate a **PSID (Payment Slip ID)** for each registration.
* The system will integrate with the client's existing **DNS (Donation Management System)**.
* Payment records will be synchronized between:

  * Olympiad Application
  * Client's DNS
* Payment status and transaction history will be available for reconciliation and reporting.

---

## 6. Event-Day Operations

### Attendance

* Marked by Sub Admins through Web or Mobile.
* Attendance will be based on **Student ID**.

### Rosters

* Generated automatically by the system.
* Managed using **Student ID**.
* Shirt numbers will not be used for roster generation.

---

## 7. Academic vs Sports Event Handling

### Academic Events

* Judges assigned per competition.
* Marking available through Web and Mobile.
* Referees can mark only through the Mobile App.
* Paper-based submissions can be uploaded as scanned images/PDFs and linked to the respective student, competition, and event.

### Sports Events

* Referees/Umpires assigned instead of judges.
* Rosters will display:

  * Student Name
  * School Name
  * Shirt Number
* Attendance will be marked using **Student ID** by authorized personnel.

---

## 8. Reporting & Outputs

The system will provide reports including:

* School-wise registrations
* Competition-wise participant lists
* Attendance summaries
* Academic results
* Sports rosters & match sheets
* PSID-based transaction reports
* Payment summaries

Reports can be exported in **PDF** and **CSV** formats with role-based access control.

---

## 9. Security & Governance

* Role-Based Access Control (RBAC)
* School-level data isolation
* Audit logs for:

  * Approvals
  * Attendance
  * Judging
  * Payments
* Blacklisting of schools and students by authorized administrators.
* Blacklisted entities will be restricted from registration, participation, and system access.





