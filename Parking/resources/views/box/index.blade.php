<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الصندوق</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <style>
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        .input-form {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .income-outcome-container {
            display: flex;
            gap: 40px;
            margin-bottom: 30px;
        }
        .income-box, .outcome-box {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table1 {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table1 th, .table1 td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table1 th {
            background: #e9ecef;
        }
        .title {
            text-align: center;
            margin: 30px 0 20px 0;
        }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:flex-start; margin: 20px 0 0 20px;">
        <a href="{{ route('dashboard.index') }}" class="button2" style="background:#007bff; color:#fff; padding:8px 20px; border-radius:6px; text-decoration:none; font-size:1em;">العودة للرئيسية</a>
    </div>
    <h1 class="title">إدارة الصندوق</h1>
    <div class="income-outcome-container">
        <div class="income-box">
            <h2>إضافة دخل</h2>
            <form action="{{ route('box.income.store') }}" method="POST" class="form">
                @csrf
                <div class="input-form">
                    <label>المبلغ</label>
                    <input type="number" name="amount" class="inp-text" required>
                </div>
                <div class="input-form">
                    <label>العميل (اختياري)</label>
                    <select name="customer_id" class="inp-text">
                        <option value="">بدون عميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-form">
                    <label>ملاحظات</label>
                    <input type="text" name="notes" class="inp-text">
                </div>
                <button type="submit" class="button2">إضافة دخل</button>
            </form>
        </div>
        <div class="outcome-box">
            <h2>إضافة مصروف</h2>
            <form action="{{ route('box.outcome.store') }}" method="POST" class="form">
                @csrf
                <div class="input-form">
                    <label>المبلغ</label>
                    <input type="number" name="amount" class="inp-text" required>
                </div>
                <div class="input-form">
                    <label>ملاحظات</label>
                    <input type="text" name="notes" class="inp-text">
                </div>
                <button type="submit" class="button2" style="background:#dc3545;">إضافة مصروف</button>
            </form>
        </div>
    </div>
    <div style="display:flex; justify-content:center; margin-bottom:30px;">
        <div style="background:#e8f5e8; border:1px solid #28a745; border-radius:8px; padding:20px 40px; min-width:220px; text-align:center; font-size:1.5em; font-weight:bold; color:#28a745;">
            إجمالي الصندوق: {{ number_format($totalBox, 2) }}
        </div>
    </div>
    <h2 style="text-align:center;">سجل الحركات</h2>
    <table class="table1">
        <thead>
            <tr>
                <th>النوع</th>
                <th>المبلغ</th>
                <th>العميل</th>
                <th>ملاحظات</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->type == 'income' ? 'دخل' : 'مصروف' }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ $transaction->customer ? $transaction->customer->name : '-' }}</td>
                    <td>{{ $transaction->notes }}</td>
                    <td>{{ $transaction->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
