<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Year;
use App\Http\Requests\Student\CreateStudentFormRequest;
use App\Http\Requests\Student\EditStudentFormRequest;
use App\Http\Requests\ImportStudentRequest;
use App\Actions\Student\StudentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Year $year, StudentClass $studentClass, Request $request)
    {
        $search = $request->input('search', '');

        $students = Student::query()
            ->where('student_class_id', $studentClass->id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                ->orWhere('nisn', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('student/List', [
            'year' => $year,
            'studentClass' => $studentClass,
            'models' => $students,
        ]);
    }

    public function store(Year $year, StudentClass $studentClass, CreateStudentFormRequest $request, StudentAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function update(Year $year, StudentClass $studentClass, EditStudentFormRequest $request, Student $model, StudentAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function destroy(Year $year, StudentClass $studentClass, Student $model)
    {
        $model->deleteCvFile();
        $model->delete();

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function dashboard(Request $request)
    {
        $dashboardData = Student::getDashboardData();

        return Inertia::render('dashboard/DashboardStudent', [
            'dashboardData' => $dashboardData
        ]);
    }

    public function import(Year $year, StudentClass $studentClass, ImportStudentRequest $request)
    {
        DB::beginTransaction();
        // Variabel untuk menyimpan nomor baris saat ini untuk logging error jika terjadi Exception
        $currentRowNumberForLogging = 'N/A (sebelum loop data)';

        try {
            $file = $request->file('file');
            $authUserId = Auth::user()->id;

            $sheets = Excel::toArray(new \stdClass(), $file);

            $sheetData = $sheets[0]; // Asumsi hanya satu sheet dan itu yang pertama

            $fileHeaders = array_map('trim', $sheetData[0]);

            $headerMapping = [
                'Nama' => 'name',
                'NISN' => 'nisn',
                'No. Telp' => 'phone',
                'Email' => 'email',
                'Password' => 'password',
                'Jenis Kelamin' => 'gender',
                'Tanggal Lahir' => 'born_date', // Asumsi format YYYY-MM-DD dari Excel
                'TB' => 'height',
                'BB' => 'weight',
                'Provinsi' => 'province',
                'Kota' => 'city',
                'Alamat' => 'address',
                'Status Lulus' => 'is_graduated', // Asumsi 1 atau 0 dari Excel
                'Status Menikah' => 'is_married', // Asumsi 1 atau 0 dari Excel
            ];

            dd($headerMapping);

            $actualHeaderMap = [];
            foreach ($headerMapping as $indonesianHeader => $modelAttribute) {
                $columnIndex = array_search($indonesianHeader, $fileHeaders);
                if ($columnIndex === false) {
                    throw new \Exception("Header kolom '{$indonesianHeader}' tidak ditemukan. Pastikan template file Excel sesuai.");
                }
                $actualHeaderMap[$modelAttribute] = $columnIndex;
            }

            $importedCount = 0;

            // Mulai dari baris kedua (index 1), karena baris pertama adalah header
            for ($rowIndex = 1; $rowIndex < count($sheetData); $rowIndex++) {
                $row = $sheetData[$rowIndex];
                $currentRowNumberForLogging = $rowIndex + 1; // Untuk pesan error jika ada
                $studentData = [];

                foreach ($headerMapping as $indonesianHeader => $modelAttribute) {
                    $columnIndex = $actualHeaderMap[$modelAttribute];
                    $rawValue = isset($row[$columnIndex]) ? (is_string($row[$columnIndex]) ? trim($row[$columnIndex]) : $row[$columnIndex]) : null;

                    switch ($modelAttribute) {
                        case 'password':
                            // Langsung hash, asumsi tidak kosong berdasarkan SOP
                            $studentData[$modelAttribute] = Hash::make($rawValue);
                            break;
                        case 'height':
                        case 'weight':
                        case 'is_graduated':
                        case 'is_married':
                        default:
                            $studentData[$modelAttribute] = $rawValue;
                            break;
                    }
                }

                $studentData['student_class_id'] = $studentClass->id;
                $studentData['created_by'] = $authUserId;
                $studentData['updated_by'] = $authUserId;

                Student::create($studentData);
                $importedCount++;
            }

            DB::commit();
            return back()->with('success', "Impor berhasil! {$importedCount} siswa telah ditambahkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import siswa gagal: Terjadi error pada atau sekitar baris '{$currentRowNumberForLogging}'. Pesan: " . $e->getMessage() . '. Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Proses impor gagal: ' . $e->getMessage());
        }

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }
}
