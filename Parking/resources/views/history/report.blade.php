<!-- resources/views/history/report.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .report-container {
            width: 100%;
            max-width: 100%;
        }

        .table1 {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .table1 th,
        .table1 td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        .table1 th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .report-summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .report-summary p {
            margin: 5px 0;
            font-size: 14px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Print-specific styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .table1 {
                font-size: 10px;
                page-break-inside: auto;
            }

            .table1 th,
            .table1 td {
                padding: 4px;
            }

            .table1 tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .report-summary {
                page-break-inside: avoid;
                margin-top: 10px;
                padding: 5px;
            }

            .report-summary p {
                font-size: 12px;
                margin: 3px 0;
            }

            button {
                display: none;
            }

            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <table class="table1">
            <thead>
                <tr>
                    <th>الزبون</th>
                    <th>نوع الوقوف</th>
                    <th>فئة المركبة</th>
                    <th>رقم اللوحة</th>
                    <th>وقت الدخول</th>
                    <th>وقت الخروج</th>
                    <th>المدة بالدقيقة</th>
                    <th>التكلفة الكلية</th>
                    <th>الخدمات</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteredHistories as $history)
                    <tr>
                        <td>{{ $history->customer_name }}</td>
                        <td>{{$history->parking_type}} </td>
                        <td>{{ $history->vic_typ }}</td>
                        <td>{{ $history->vic_plate }}</td>
                        <td>{{ $history->time_in ? \Carbon\Carbon::parse($history->time_in)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td>{{ $history->time_out ? \Carbon\Carbon::parse($history->time_out)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td>{{ number_format($history->duration ?? 'N/A', 2) }}</td>
                        <td>{{ number_format($history->price, 2) }}</td>
                        <td>
                            @if($history->services)
                                @php
                                    $services = json_decode($history->services, true);
                                @endphp
                                @if(is_array($services))
                                    @foreach($services as $service)
                                        {{ $service['name'] }} ({{ $service['price'] }})
                                        @if(!$loop->last), @endif
                                    @endforeach
                                @endif
                            @else
                                لا يوجد خدمات
                            @endif
                        </td>
                        <td>{{$history->notes}} </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">لا يوجد سجلات بهذا البحث</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="report-summary">
            <p>تكلفة الوقوف : {{number_format($price_sum, 2)}}</p>
            <p>التكلفة مع الخدمات : {{number_format($price_with_services, 2)}}</p>
            <p>تكلفة الخدمات : {{number_format($price_with_services - $price_sum, 2)}}</p>
        </div>
    </div>

    <button onclick="pprint()">اطبع التقرير</button>
</body>

<script>
    function pprint() {
        window.print();
    }
</script>

</html>