# Project Scope

## 1. Platforms Included

### Web Portal

Accessible by:

- Super Admin
- Sub Admins (Event Management)
- School Admins
- Judges

### Mobile Applications

- School Admin Mobile App
- Judge Portal Mobile App
- Sub Admin Mobile Access (limited operational use)

Mobile UI design is included as part of the project scope.

---

# 2. User Roles & Access Overview

## Super Admin

- Full system control
- Event, competition, criteria, and role management
- School approvals and payment verification
- Reporting and exports

## Sub Admin (Event Management)

- Mobile and Web access
- View schedules, rosters, and draws
- Mark attendance
- Verify students on-ground
- Assign referees/umpires and manage match rosters

## School Admin

- Web and Mobile access
- Manage students through manual entry only
- Register participants
- View invoices and registration status
- View rosters and event schedules

## Judges

- Web and Mobile access
- Academic events only
- View assigned competitions
- Enter scores based on predefined rubrics

---

# 3. Competition Structure

The competition framework will follow a standardized hierarchy:

- Junior Category
- Senior Category

Each category will be divided into:

- Individual
- Team

Each competition will have:

- Defined minimum and maximum age limits
- Assigned event type (Academic or Sports)

Per-school quota will be managed under School Allowance and will not be part of competition definitions.

The system will support creating and managing categories within events and competitions based on:

- Subject
- Competition type
- Skill level
- Other organizer-defined criteria

---

# 4. Registration & Student Management Rules

To ensure data accuracy, transparency, and operational control:

- Students will be added manually, one-by-one by the School Admin.
- Bulk upload of students will not be available.

Schools will upload official verification documents during registration, including:

- Cambridge affiliation certificate
- Matriculation board certificate

These documents will be reviewed as part of the school approval workflow.

Additional rules:

- Schools will not upload payment slips within the system.
- Manual payment handling and WhatsApp-based payment confirmation will not be used.
- Remaining available slots will not be displayed to schools during registration.

Upon successful approval of school registration, the system will automatically generate a unique School ID.

The School ID will be used for:

- Student registrations
- Payments
- Reporting
- Internal system references

---

# 5. Payments & Confirmation Flow

- The application will generate a PSID (Payment Slip ID) for each registration.
- The system will integrate with the client's existing DNS (Donation Management System).
- Payment records will be synchronized between:
    - Olympiad Application
    - Client's DNS

Payment information will include:

- Payment status
- Transaction history
- Reconciliation records
- Reporting data

Manual payment handling and WhatsApp-based confirmation will not be part of the system workflow.

---

# 6. Event-Day Operations

## Attendance

- Attendance will be marked by Sub Admins through Web or Mobile.
- Attendance will be based strictly on Student ID.
- Shirt numbers will not be used for attendance purposes.
- Attendance will only be marked by authorized event management personnel.

## Rosters

- Rosters will be generated automatically by the system.
- Rosters will be managed and referenced using Student ID only.
- Shirt numbers will not be used for roster generation.

---

# 7. Academic vs Sports Event Handling

## Academic Events

- Judges will be assigned per competition.
- Judges can perform marking through Web Portal and Mobile Application.
- Referees will perform marking strictly through Mobile Application only.

This separation ensures controlled and efficient operations.

Paper-based competitions such as:

- Scrabble
- Science Fair
- Essay Writing

will allow students to submit handwritten work.

The system will provide an option to:

- Upload images
- Upload scanned copies of written submissions

Uploaded documents will be linked with:

- Student
- Competition
- Event

for evaluation and record purposes.

---

## Sports Events

- Referees and Umpires will be assigned instead of Judges.

Sports rosters will display:

- Student Name
- School Name
- Shirt Number

Referee numbers will also be visible on sports rosters.

Rules:

- Attendance will be marked strictly using Student ID.
- Shirt numbers will only be used for roster display.
- Attendance will be performed by authorized event management personnel through the system.

---

# 8. Reporting & Outputs

The system will support:

- School-wise registration reports
- Competition-wise participant lists
- Attendance summaries
- Academic results
- Sports rosters and match sheets
- PSID-based transaction reports
- Payment summaries

Reports will be exportable in:

- PDF
- CSV

formats with role-based access control.

---

# 9. Security & Governance

The system will provide:

- Strict Role-Based Access Control (RBAC)
- School-level data isolation
- Complete audit logs for:
    - Approvals
    - Attendance
    - Judging entries
    - Payment records

The system will provide blacklist management for:

- Schools
- Individual students

Blacklisted entities will be restricted from:

- Registration
- Participation
- System access

Blacklisting actions will only be performed by authorized administrators.
