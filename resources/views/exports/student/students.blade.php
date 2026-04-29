<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>title</title>
</head>

<body>
    <table>
        <tr>
            <td colspan="15" align="center" style="font-weight: bold;font-size: 14px;">BKK Edusurya - SMKN Purwosari
                Bojonegoro</td>
        </tr>
        <tr>
            <td colspan="15" align="center" style="font-weight: bold;font-size: 14px;">Export Data Siswa</td>
        </tr>
        <tr>
            <td colspan="8">Tanggal : {{ \Carbon\Carbon::now()->format('l, d-m-Y H:i') }}</td>
            <td colspan="7" align="right">Jumlah Siswa: {{ $students->count() }}</td>
        </tr>
        <tr>
            <td colspan="8">Tahun Angkatan: {{ $studentClass->year->year ?? '-' }}</td>
            <td colspan="7" align="right">Kelas: {{ $studentClass->name ?? '-' }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr style="font-weight: bold;">
                <th>No</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>No. Telepon</th>
                <th>Email</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Umur</th>
                <th>Tinggi Badan</th>
                <th>Berat Badan</th>
                <th>Provinsi</th>
                <th>Kabupaten/Kota</th>
                <th>Alamat</th>
                <th>Status Lulus</th>
                <th>Status Menikah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->nisn ?? '-' }}</td>
                    <td>{{ $student->phone ?? '-' }}</td>
                    <td>{{ $student->email ?? '-' }}</td>
                    <td>
                        @if ($student->gender == 'laki-laki')
                            Laki-laki
                        @elseif ($student->gender == 'perempuan')
                            Perempuan
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $student->born_date ? \Carbon\Carbon::parse($student->born_date)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $student->age . ' tahun' }}</td>
                    <td>{{ $student->height ? $student->height . ' cm' : '-' }}</td>
                    <td>{{ $student->weight ? $student->weight . ' kg' : '-' }}</td>
                    <td>{{ $student->province ?? '-' }}</td>
                    <td>{{ $student->city ?? '-' }}</td>
                    <td>{{ $student->address ?? '-' }}</td>
                    <td>{{ $student->is_graduated ? 'Lulus' : 'Belum Lulus' }}</td>
                    <td>{{ $student->is_married ? 'Menikah' : 'Belum Menikah' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
