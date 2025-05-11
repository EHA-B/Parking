<!-- resources/views/history/report.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
</head>
<body>
    <h1>Report</h1>
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
                    <td colspan="8" class="text-center">لا يوجد سجلات بهذا البحث</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <p>price sum : {{$price_sum}}</p>
    <p>price with service : {{$price_with_services}}</p>
    <p>service price : {{$price_with_services - $price_sum}}</p>
    <button onclick="pprint()">Print</button>
</body>

<script>
    function pprint()
    {
        window.print();
    }
</script>
</html>