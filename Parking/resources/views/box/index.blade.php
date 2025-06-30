<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الصندوق</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <div class="all">
        <a href="{{ route('dashboard.index') }}" class="button2" style="width:100px; margin:10px;">العودة للرئيسية</a>

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
                        <select name="customer_id" class="inp-text" id="customer-select">
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
            <div
                style="background:#e8f5e8; border:1px solid #28a745; border-radius:8px; padding:20px 40px; min-width:220px; text-align:center; font-size:1.5em; font-weight:bold; color:#28a745;">
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
    </div>
    <script>
        $(document).ready(function () {
            $('#customer-select').select2({
                placeholder: "ابحث عن عميل...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>

</html>