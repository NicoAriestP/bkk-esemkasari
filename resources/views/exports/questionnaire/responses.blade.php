<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td colspan="12" align="center" style="font-weight: bold;font-size: 14px;">BKK Edusurya - SMKN Purwosari
                Bojonegoro</td>
        </tr>
        <tr>
            <td colspan="12" align="center" style="font-weight: bold;font-size: 14px;">Export Respons Kuesioner</td>
        </tr>
        <tr>
            <td colspan="6">Angkatan : {{ $year->year }}</td>
        <tr>
            <td colspan="6">Kelas: {{ $studentClass->name }}</td>
        </tr>
        <tr>
            <td colspan="6">Judul Kuesioner: {{ $questionnaire->title }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">No</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Siswa</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">NISN</th>
                <th style="border: 1px solid black; font-weight: bold;" rowspan="2">Waktu Submit</th>
                <th align="center" colspan="{{ $questionnaire->questions->count() }}" style="border: 1px solid black; font-weight: bold;">Pertanyaan</th>
            </tr>
            <tr>
                @foreach ($questionnaire->questions as $question)
                    <th style="border: 1px solid black; font-weight: bold;">{{ $question->question_title }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($responses as $index => $response)
                <tr>
                    <td style="border: 1px solid black;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid black;">{{ $response['student'] }}</td>
                    <td style="border: 1px solid black;">{{ $response['nisn'] ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $response['submitted_at'] }}</td>
                    @foreach ($questionnaire->questions as $question)
                        <td style="border: 1px solid black;">{{ data_get($response['answers'], $question->id, '-') }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + $questionnaire->questions->count() }}" style="border: 1px solid black;">Belum ada respons yang masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
