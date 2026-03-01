<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $defaultStudentClassId;

    public function __construct($defaultStudentClassId = null)
    {
        $this->defaultStudentClassId = $defaultStudentClassId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        return new Student([
            'student_class_id' => $this->defaultStudentClassId,
            'name'             => $row['nama'] ?? $row['name'],
            'nisn'             => (string) ($row['nisn'] ?? ''),
            'phone'            => $this->cleanPhoneNumber($row['no_telp'] ?? $row['phone']),
            'email'            => $row['email'],
            'password'         => Hash::make((string) ($row['password'] ?? '')),
            'gender'           => $this->normalizeGender($row['jenis_kelamin'] ?? $row['gender']),
            'born_date'        => $this->parseDate($row['tanggal_lahir'] ?? $row['born_date']),
            'height'           => $row['tb'] ?? $row['height'],
            'weight'           => $row['bb'] ?? $row['weight'],
            'province'         => $row['provinsi'] ?? $row['province'],
            'city'             => $row['kota'] ?? $row['city'],
            'address'          => $row['alamat'] ?? $row['address'],
            'is_graduated'     => $this->parseBoolean($row['status_lulus'] ?? $row['is_graduated']),
            'is_married'       => $this->parseBoolean($row['status_menikah'] ?? $row['is_married']),
        ]);
    }

    /**
     * Clean and format phone number
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove brackets, spaces, and hyphens
        $phone = preg_replace('/[\(\)\s\-]/', '', $phone);

        // Convert (+62) to 62
        $phone = preg_replace('/^\(\+62\)/', '62', $phone);
        $phone = preg_replace('/^\+62/', '62', $phone);

        // Convert 0 to 62
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Normalize gender values
     */
    private function normalizeGender($gender)
    {
        if (empty($gender)) {
            return null;
        }

        $gender = strtolower(trim($gender));

        if (in_array($gender, ['laki-laki', 'l', 'male', 'pria'])) {
            return 'laki-laki';
        }

        if (in_array($gender, ['perempuan', 'p', 'female', 'wanita'])) {
            return 'perempuan';
        }

        return $gender;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Excel serial date (numeric)
            if (is_numeric($date)) {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $date);
                return $dt->format('Y-m-d');
            }

            $date = trim((string) $date);

            // Try strict formats first (handles zero-padded variants)
            $formats = [
                'Y-m-d',   // 2005-01-15
                'd/m/Y',   // 15/01/2005
                'm/d/Y',   // 01/15/2005
                'd-m-Y',   // 15-01-2005
                'm-d-Y',   // 01-15-2005
                'Y/m/d',   // 2005/01/15
                'd/m/y',   // 15/01/05
            ];

            foreach ($formats as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, $date);
                    if ($parsed !== false) {
                        return $parsed->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Fallback: Carbon flexible parsing (handles non-zero-padded like 15/1/2005)
            return Carbon::parse($date)->format('Y-m-d');

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse boolean values
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'ya', 'yes', 'lulus', 'menikah']);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama'          => 'required|string|max:255',
            'nisn'          => 'required|max:20|unique:students,nisn',
            'email'         => 'required|email|unique:students,email|max:255',
            'password'      => 'required|max:255',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'tb'            => 'nullable|numeric|min:0|max:300',
            'bb'            => 'nullable|numeric|min:0|max:500',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama siswa wajib diisi.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar dalam sistem.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tb.numeric' => 'Tinggi badan harus berupa angka.',
            'bb.numeric' => 'Berat badan harus berupa angka.',
        ];
    }
}
