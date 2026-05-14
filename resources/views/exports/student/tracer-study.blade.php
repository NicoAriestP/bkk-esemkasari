<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>title</title>
</head>

<body>
    <table>
        <tr>
            <td colspan="12" align="center" style="font-weight: bold;font-size: 14px;">BKK Edusurya - SMKN Purwosari
                Bojonegoro</td>
        </tr>
        <tr>
            <td colspan="12" align="center" style="font-weight: bold;font-size: 14px;">Export Tracer Study</td>
        </tr>
        <tr>
            <td colspan="6">Tanggal : {{ \Carbon\Carbon::now()->format('l, d-m-Y H:i') }}</td>
            <td colspan="6" align="right">Jumlah Siswa: {{ $students->count() }}</td>
        </tr>
        <tr>
            <td colspan="6">Tahun Angkatan: {{ $studentClass->year->year ?? '-' }}</td>
            <td colspan="6" align="right">Kelas: {{ $studentClass->name ?? '-' }}</td>
        </tr>
    </table>
    <table style="border-collapse: collapse; width: 100%">
        <thead>
            <tr>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">No</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Nama</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">NISN</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">No. Telepon</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Email</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Jenis Kelamin</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Tanggal Lahir</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Umur</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Tinggi Badan</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Berat Badan</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Provinsi</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Kabupaten/Kota</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Alamat</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Status Lulus</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Status Menikah</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Aktivitas Utama</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center" colspan="6">Aktivitas
                    Utama : Bekerja</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center" colspan="5">Aktivitas
                    Utama : Kuliah</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center" colspan="4">Aktivitas
                    Utama : Wirausaha</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center" colspan="7">Umpan Balik
                    Pendidikan</th>
            </tr>
            <tr>
                <th style="border: 1px solid black; font-weight: bold;">Jenis Pekerjaan</th>
                <th style="border: 1px solid black; font-weight: bold;">Jam Kerja (Per Minggu)</th>
                <th style="border: 1px solid black; font-weight: bold;">Range Gaji</th>
                <th style="border: 1px solid black; font-weight: bold;">Frekuensi Ganti Pekerjaan</th>
                <th style="border: 1px solid black; font-weight: bold;">Keselarasan Pekerjaan</th>
                <th style="border: 1px solid black; font-weight: bold;">Cara Dapat Pekerjaan Pertama</th>
                <th style="border: 1px solid black; font-weight: bold;">Perguruan Tinggi</th>
                <th style="border: 1px solid black; font-weight: bold;">Program Studi</th>
                <th style="border: 1px solid black; font-weight: bold;">Jenjang Pendidikan</th>
                <th style="border: 1px solid black; font-weight: bold;">Sumber Pembiayaan</th>
                <th style="border: 1px solid black; font-weight: bold;">Kesesuaian Jurusan</th>
                <th style="border: 1px solid black; font-weight: bold;">Nama Usaha</th>
                <th style="border: 1px solid black; font-weight: bold;">Bidang Usaha</th>
                <th style="border: 1px solid black; font-weight: bold;">Skala Usaha</th>
                <th style="border: 1px solid black; font-weight: bold;">Pendapatan Usaha</th>
                <th style="border: 1px solid black; font-weight: bold;">Alasan Memilih SMK</th>
                <th style="border: 1px solid black; font-weight: bold;">Durasinya PKL</th>
                <th style="border: 1px solid black; font-weight: bold;">Kualitas Tempat PKL</th>
                <th style="border: 1px solid black; font-weight: bold;">Kesesuaian Tugas PKL</th>
                <th style="border: 1px solid black; font-weight: bold;">Bimbingan Selama PKL</th>
                <th style="border: 1px solid black; font-weight: bold;">Monitoring Guru</th>
                <th style="border: 1px solid black; font-weight: bold;">Sertifikat Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                <tr>
                    <td style="border: 1px solid black;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid black;">{{ $student->name }}</td>
                    <td style="border: 1px solid black;">{{ $student->nisn ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->phone ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->email ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        @if ($student->gender == 'laki-laki')
                            Laki-laki
                        @elseif ($student->gender == 'perempuan')
                            Perempuan
                        @else
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->born_date ? \Carbon\Carbon::parse($student->born_date)->format('d-m-Y') : '-' }}
                    </td>
                    <td style="border: 1px solid black;">{{ $student->age . ' tahun' }}</td>
                    <td style="border: 1px solid black;">{{ $student->height ? $student->height . ' cm' : '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->weight ? $student->weight . ' kg' : '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->province ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->city ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->address ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $student->is_graduated ? 'Lulus' : 'Belum Lulus' }}</td>
                    <td style="border: 1px solid black;">{{ $student->is_married ? 'Menikah' : 'Belum Menikah' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->detailActivityAnswer ? \App\Enum\TracerStudy\DetailActivityMainOption::tryFrom(json_decode($student->detailActivityAnswer->answers, true)['mainActivity'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentActivityAnswer ? \App\Enum\TracerStudy\StudentWorkTypeOption::tryFrom(json_decode($student->studentActivityAnswer->answers, true)['workType'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentWorkingAnswer ? (json_decode($student->studentWorkingAnswer->answers, true)['workingHours'] ?? 0) . ' jam' : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentWorkingAnswer ? \App\Enum\TracerStudy\StudentWorkingSalaryOption::tryFrom(json_decode($student->studentWorkingAnswer->answers, true)['salaryRange'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentWorkingAnswer ? \App\Enum\TracerStudy\StudentWorkingJobChangeOption::tryFrom(json_decode($student->studentWorkingAnswer->answers, true)['jobChangeFrequency'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentWorkingAnswer ? \App\Enum\TracerStudy\StudentWorkingJobRelevanceOption::tryFrom(json_decode($student->studentWorkingAnswer->answers, true)['jobRelevance'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        @php
                            $studentWorkingAnswers = $student->studentWorkingAnswer
                                ? json_decode($student->studentWorkingAnswer->answers, true)
                                : [];
                            $howFoundFirstJob = $studentWorkingAnswers['howFoundFirstJob'] ?? [];
                            $howFoundFirstJobLabels = collect(is_array($howFoundFirstJob) ? $howFoundFirstJob : [])
                                ->filter(fn($item) => $item !== 'hfj-other')
                                ->map(
                                    fn($item) => \App\Enum\TracerStudy\StudentWorkingHowFoundJobOption::tryFrom(
                                        $item,
                                    )?->label(),
                                )
                                ->filter()
                                ->values();

                            if (
                                ($studentWorkingAnswers['otherJobSourceText'] ?? null) &&
                                in_array('hfj-other', is_array($howFoundFirstJob) ? $howFoundFirstJob : [], true)
                            ) {
                                $howFoundFirstJobLabels->push(
                                    'Lainnya: ' . $studentWorkingAnswers['otherJobSourceText'],
                                );
                            }
                        @endphp
                        {{ $howFoundFirstJobLabels->isNotEmpty() ? $howFoundFirstJobLabels->implode(', ') : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentUniversityAnswer?->answers['universityName'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentUniversityAnswer?->answers['studyProgram'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentUniversityAnswer?->answers['educationLevel'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentUniversityAnswer ? \App\Enum\TracerStudy\StudentUniversityFundingSourceOption::tryFrom(json_decode($student->studentUniversityAnswer->answers, true)['fundingSource'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentUniversityAnswer ? \App\Enum\TracerStudy\StudentUniversityMajorRelevanceOption::tryFrom(json_decode($student->studentUniversityAnswer->answers, true)['majorRelevance'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentEntrepreneurAnswer?->answers['businessName'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentEntrepreneurAnswer?->answers['businessField'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentEntrepreneurAnswer ? \App\Enum\TracerStudy\StudentEntrepreneurBusinessScaleOption::tryFrom(json_decode($student->studentEntrepreneurAnswer->answers, true)['businessScale'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->studentEntrepreneurAnswer ? \App\Enum\TracerStudy\StudentEntrepreneurBusinessIncomeOption::tryFrom(json_decode($student->studentEntrepreneurAnswer->answers, true)['businessIncome'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        @php
                            $studentSmkReasons = $student->feedbackAnswer
                                ? json_decode($student->feedbackAnswer->answers, true)
                                : [];
                            $smkReasons = $studentSmkReasons['smkReasons'] ?? [];
                            $smkReasonsLabels = collect(is_array($smkReasons) ? $smkReasons : [])
                                ->filter(fn($item) => $item !== 'smk-other')
                                ->map(
                                    fn($item) => \App\Enum\TracerStudy\StudentFeedbackSmkReasonOption::tryFrom(
                                        $item,
                                    )?->label(),
                                )
                                ->filter()
                                ->values();

                            if (
                                ($studentSmkReasons['otherSmkReasonText'] ?? null) &&
                                in_array('smk-other', is_array($smkReasons) ? $smkReasons : [], true)
                            ) {
                                $smkReasonsLabels->push('Lainnya: ' . $studentSmkReasons['otherSmkReasonText']);
                            }
                        @endphp
                        {{ $smkReasonsLabels->isNotEmpty() ? $smkReasonsLabels->implode(', ') : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->feedbackAnswer ? \App\Enum\TracerStudy\StudentFeedbackPklDurationOption::tryFrom(json_decode($student->feedbackAnswer->answers, true)['pklDuration'])?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->feedbackAnswer ? \App\Enum\TracerStudy\StudentFeedbackPklQualityOption::tryFrom(json_decode($student->feedbackAnswer->answers, true)['pklQuality']['location'] ?? null)?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->feedbackAnswer ? \App\Enum\TracerStudy\StudentFeedbackPklTaskRelevanceOption::tryFrom(json_decode($student->feedbackAnswer->answers, true)['pklQuality']['taskRelevance'] ?? null)?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->feedbackAnswer ? \App\Enum\TracerStudy\StudentFeedbackPklGuidanceOption::tryFrom(json_decode($student->feedbackAnswer->answers, true)['pklQuality']['guidance'] ?? null)?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        {{ $student->feedbackAnswer ? \App\Enum\TracerStudy\StudentFeedbackPklMonitoringOption::tryFrom(json_decode($student->feedbackAnswer->answers, true)['pklQuality']['monitoring'] ?? null)?->label() : '-' }}
                    </td>
                    <td style="border: 1px solid black;">
                        @php
                            $studentFeedbackAnswers = $student->feedbackAnswer
                                ? json_decode($student->feedbackAnswer->answers, true)
                                : [];
                            $certificates = $studentFeedbackAnswers['certificates'] ?? [];
                            $certificateNames = collect(is_array($certificates) ? $certificates : [])
                                ->pluck('name')
                                ->filter()
                                ->values();
                        @endphp
                        {{ $certificateNames->isNotEmpty() ? $certificateNames->implode(', ') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
