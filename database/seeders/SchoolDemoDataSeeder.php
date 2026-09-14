<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\SchoolStatusEnum;
use App\Enums\StudentStatusEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentRegistration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::query()
            ->where('status', SchoolStatusEnum::Approved)
            ->orderBy('id')
            ->get();

        if ($schools->isEmpty()) {
            $this->command?->warn('No approved school found. Approve a school first.');

            return;
        }

        foreach ($schools as $school) {
            $this->seedForSchool($school);
        }
    }

    private function seedForSchool(School $school): void
    {
        $prefix = 'STU-S'.$school->id.'-';

        $students = [
            ['suffix' => '001', 'name' => 'Anas', 'father' => 'Rashid Khan', 'class' => '10', 'section' => 'A', 'gender' => GenderEnum::Male, 'dob' => '2010-03-15'],
            ['suffix' => '002', 'name' => 'Ahmed Khan', 'father' => 'Khalid Khan', 'class' => '8', 'section' => 'A', 'gender' => GenderEnum::Male, 'dob' => '2012-06-20'],
            ['suffix' => '003', 'name' => 'Sara Ali', 'father' => 'Ali Raza', 'class' => '9', 'section' => 'B', 'gender' => GenderEnum::Female, 'dob' => '2011-01-08'],
            ['suffix' => '004', 'name' => 'Zaid Hassan', 'father' => 'Hassan Mehmood', 'class' => '7', 'section' => 'C', 'gender' => GenderEnum::Male, 'dob' => '2013-09-12'],
            ['suffix' => '005', 'name' => 'Fatima Noor', 'father' => 'Noor Ahmed', 'class' => '10', 'section' => 'A', 'gender' => GenderEnum::Female, 'dob' => '2010-11-25'],
            ['suffix' => '006', 'name' => 'Hassan Raza', 'father' => 'Razaullah', 'class' => '6', 'section' => 'A', 'gender' => GenderEnum::Male, 'dob' => '2014-04-03'],
            ['suffix' => '007', 'name' => 'Ayesha Malik', 'father' => 'Malik Saeed', 'class' => '8', 'section' => 'B', 'gender' => GenderEnum::Female, 'dob' => '2012-08-17'],
            ['suffix' => '008', 'name' => 'Omar Farooq', 'father' => 'Farooq Shah', 'class' => '9', 'section' => 'A', 'gender' => GenderEnum::Male, 'dob' => '2011-12-01'],
            ['suffix' => '009', 'name' => 'Mariam Shah', 'father' => 'Shahid Hussain', 'class' => '7', 'section' => 'A', 'gender' => GenderEnum::Female, 'dob' => '2013-02-14'],
            ['suffix' => '010', 'name' => 'Bilal Ahmed', 'father' => 'Ahmed Nadeem', 'class' => '11', 'section' => 'B', 'gender' => GenderEnum::Male, 'dob' => '2009-07-22'],
            ['suffix' => '011', 'name' => 'Hira Khan', 'father' => 'Imran Khan', 'class' => '6', 'section' => 'B', 'gender' => GenderEnum::Female, 'dob' => '2014-10-30'],
            ['suffix' => '012', 'name' => 'Usman Ali', 'father' => 'Ali Akbar', 'class' => '10', 'section' => 'B', 'gender' => GenderEnum::Male, 'dob' => '2010-05-09'],
        ];

        $createdStudents = [];

        foreach ($students as $data) {
            $code = $prefix.$data['suffix'];
            $createdStudents[$code] = Student::query()->updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_code' => $code,
                ],
                [
                    'name' => $data['name'],
                    'father_name' => $data['father'],
                    'date_of_birth' => $data['dob'],
                    'gender' => $data['gender'],
                    'class' => $data['class'],
                    'section' => $data['section'],
                    'status' => StudentStatusEnum::Active,
                ],
            );
        }

        $registrations = [
            [
                'student_code' => $prefix.'001',
                'competition' => 'Math Olympiad 2025',
                'category' => 'Junior Boys (U-14)',
                'status' => RegistrationStatusEnum::Approved,
                'shirt' => '12',
            ],
            [
                'student_code' => $prefix.'002',
                'competition' => 'Basketball Championship',
                'category' => 'Junior Boys',
                'status' => RegistrationStatusEnum::Approved,
                'shirt' => '7',
            ],
            [
                'student_code' => $prefix.'003',
                'competition' => 'Science Olympiad',
                'category' => 'Junior Girls',
                'status' => RegistrationStatusEnum::Submitted,
                'shirt' => '21',
            ],
            [
                'student_code' => $prefix.'004',
                'competition' => 'Football Tournament',
                'category' => 'Junior Boys',
                'status' => RegistrationStatusEnum::Pending,
                'shirt' => '9',
            ],
            [
                'student_code' => $prefix.'005',
                'competition' => 'Art Competition',
                'category' => 'Solo Event - Girls',
                'status' => RegistrationStatusEnum::Approved,
                'shirt' => '3',
            ],
            [
                'student_code' => $prefix.'006',
                'competition' => 'Math Olympiad 2025',
                'category' => 'Senior Boys',
                'status' => RegistrationStatusEnum::Submitted,
                'shirt' => '15',
            ],
        ];

        $seq = 1;

        foreach ($registrations as $item) {
            $student = $createdStudents[$item['student_code']] ?? null;
            if (! $student) {
                continue;
            }

            $competition = Competition::query()
                ->where('name', $item['competition'])
                ->first();

            if (! $competition) {
                continue;
            }

            $category = CompetitionCategory::query()
                ->where('competition_id', $competition->id)
                ->where('name', $item['category'])
                ->first();

            if (! $category) {
                continue;
            }

            $code = 'REG-S'.$school->id.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;

            StudentRegistration::query()->updateOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $student->id,
                    'competition_category_id' => $category->id,
                ],
                [
                    'competition_id' => $competition->id,
                    'registration_code' => $code,
                    'shirt_number' => $item['shirt'],
                    'remarks' => 'Demo registration',
                    'status' => $item['status'],
                    'psid' => 'PSID-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)),
                    'payment_status' => $item['status'] === RegistrationStatusEnum::Approved
                        ? 'Paid'
                        : 'Pending Payment',
                    'amount' => 'PKR 5,000',
                ],
            );
        }

        $this->command?->info("Seeded 12 students and 6 registrations for school: {$school->name} (ID {$school->id})");
    }
}