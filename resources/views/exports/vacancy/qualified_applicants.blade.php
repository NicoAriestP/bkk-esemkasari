<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>title</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="9" align="center" style="font-weight: bold;font-size: 14px;">BKK Edusurya - SMKN Purwosari Bojonegoro</td>
        </tr>
        <tr>
            <td colspan="9" align="center" style="font-weight: bold;font-size: 14px;">Seleksi Pelamar - {{ $vacancy->title }}</td>
        </tr>
        <tr>
            <td colspan="5">Tanggal : {{ \Carbon\Carbon::now()->format('l, d-m-Y H:i') }}</td>
            <td colspan="4" align="right">Jumlah Pelamar: {{ $vacancy->applicants_count }} pelamar</td>
        </tr>
        <tr>
            <td colspan="5">Mitra DU/DI : {{ $vacancy->createdBy->name }}</td>
            <td colspan="4" align="right">Jumlah Terseleksi: {{ $vacancy->qualified_applicants_count }} pelamar</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tahun Angkatan</th>
                <th>Kelas</th>
                <th>NISN</th>
                <th>No. Telepon</th>
                <th>Jenis Kelamin</th>
                <th>Umur</th>
                <th>Tanggal Melamar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($qualifiedApplicants as $index => $applicant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $applicant->student->name }}</td>
                    <td>{{ $applicant->student->studentClass->year->year ?? '-' }}</td>
                    <td>{{ $applicant->student->studentClass->name ?? '-' }}</td>
                    <td>{{ $applicant->student->nisn ?? '-' }}</td>
                    <td>{{ $applicant->student->phone ?? '-' }}</td>
                    <td>@if ($applicant->student->gender == 'laki-laki')
                        Laki-laki
                    @elseif ($applicant->student->gender == 'perempuan')
                        Perempuan
                    @else
                        -
                    @endif</td>
                    <td>{{ $applicant->student->age . ' tahun' }}</td>
                    <td>{{ $applicant->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
