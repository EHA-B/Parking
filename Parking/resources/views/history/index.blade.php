<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking History</title>
</head>

<body>
    <section class="board">
        <div class="container">

            <h2>سجل الموقف</h2>
            <div class="boton">
                <a href="<?php echo route('dashboard.index'); ?>" class="serv-btn back">العودة للصفحة الرئيسية</a>
            </div>
            <!-- Filtering Form -->
            <form method="GET" action="{{ route('history.index') }}" class="filter-form">
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="customer_name" class="inp-text" placeholder="Customer Name"
                            value="{{ request('customer_name') }}">
                        <label>اسم الزبون</label>
                    </div>

                    <div class="input-form">
                        <select name="vic_typ" class="inp-text">
                            <option value="">جميع الفئات</option>
                            <option value="مركبة كبيرة" {{ request('vic_typ') == 'مركبة كبيرة' ? 'selected' : '' }}>مركبة كبيرة
                            </option>
                            <option value="مركبة صغيرة" {{ request('vic_typ') == 'مركبة صغيرة' ? 'selected' : '' }}>مركبة صغيرة
                            </option>
                        </select>
                        <label>فئة المركبة</label>
                    </div>

                    <div class="input-form">
                        <select name="parking_type" class="inp-text">
                            <option value="">جميع أنواع المواقف</option>
                            <option value="hourly" {{ request('parking_type') == 'hourly' ? 'selected' : '' }}>ساعي</option>
                            <option value="daily" {{ request('parking_type') == 'daily' ? 'selected' : '' }}>يومي</option>
                            <option value="monthly" {{ request('parking_type') == 'monthly' ? 'selected' : '' }}>شهري</option>

                        </select>
                        <label>نوع الموقف</label>
                    </div>

                    <div class="input-form">
                        <input type="date" name="start_date" class="inp-text" value="{{ request('start_date') }}">
                        <label>من</label>
                    </div>

                    <div class="input-form">
                        <input type="date" name="end_date" class="inp-text" value="{{ request('end_date') }}">
                        <label>الى</label>
                    </div>

                    <button type="submit" class="button2">
                        <span class="button-content">Filter</span>
                    </button>
                </div>
            </form>

            <!-- History Table -->
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

            <!-- Pagination -->
            <div class="pagination">
                <style>
                    .pagination {
                        display: flex;
                        justify-content: center;
                        margin: 2rem 0;
                    }

                    .pagination-controls {
                        display: flex;
                        gap: 0.5rem;
                        align-items: center;
                    }

                    .pagination-controls a,
                    .pagination-controls span {
                        padding: 0.5rem 1rem;
                        border: 1px solid #e2e8f0;
                        border-radius: 0.375rem;
                        color: #4a5568;
                        text-decoration: none;
                        transition: all 0.2s ease;
                    }

                    .pagination-controls a:hover {
                        background-color: #f7fafc;
                        border-color: #cbd5e0;
                    }

                    .pagination-controls .current {
                        background-color: #4299e1;
                        color: white;
                        border-color: #4299e1;
                    }

                    .pagination-controls .disabled {
                        color: #a0aec0;
                        cursor: not-allowed;
                    }
                </style>

                @if ($filteredHistories->hasPages())
                    <div class="pagination-controls">
                        {{-- Previous Page Link --}}
                        @if ($histories->onFirstPage())
                            <span class="disabled">Previous</span>
                        @else
                            <a href="{{ $histories->previousPageUrl() }}" rel="prev">Previous</a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($histories->getUrlRange(max($histories->currentPage() - 2, 1), min($histories->currentPage() + 2, $histories->lastPage())) as $page => $url)
                            @if ($page == $histories->currentPage())
                                <span class="current">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($histories->hasMorePages())
                            <a href="{{ $histories->nextPageUrl() }}" rel="next">Next</a>
                        @else
                            <span class="disabled">Next</span>
                        @endif
                    </div>
                @endif
            </div>

            <a href="{{ route('history.report', request()->query()) }}">Generate Report</a>
        </div>
    </section>
</body>

</html>