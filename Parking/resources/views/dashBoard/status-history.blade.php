<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status History</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <style>
        .history-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        .history-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .status-in {
            color: #28a745;
        }

        .status-out {
            color: #dc3545;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="history-container">
        <a href="{{ route('dashboard.index') }}" class="back-button">العودة للوحة التحكم</a>

        <h2>سجل حالة الوقوف</h2>

        <table class="history-table">
            <thead>
                <tr>
                    <th>التاريخ والوقت</th>
                    <th>رقم اللوحة</th>
                    <th>نوع المركبة</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $record)
                    <tr>
                        <td>{{ $record->changed_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $record->parkingSlot->vics->plate }}</td>
                        <td>{{ $record->parkingSlot->vics->brand }}</td>
                        <td class="status-{{ $record->status }}">
                            {{ $record->status === 'in' ? 'داخل' : 'خارج' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>