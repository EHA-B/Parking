<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الوقوف</title>
    <style>
         @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .table1 {
                font-size: 10px;
                page-break-inside: auto;
                color: black;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .report-actions {
            margin-top: 20px;
            text-align: right;
        }
    </style>
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
                            <option value="مركبة كبيرة" {{ request('vic_typ') == 'مركبة كبيرة' ? 'selected' : '' }}>مركبة
                                كبيرة
                            </option>
                            <option value="مركبة صغيرة" {{ request('vic_typ') == 'مركبة صغيرة' ? 'selected' : '' }}>مركبة
                                صغيرة
                            </option>
                        </select>
                        <label>فئة المركبة</label>
                    </div>

                    <div class="input-form">
                        <select name="parking_type" class="inp-text">
                            <option value="">جميع أنواع المواقف</option>
                            <option value="hourly" {{ request('parking_type') == 'hourly' ? 'selected' : '' }}>ساعي
                            </option>
                            <option value="daily" {{ request('parking_type') == 'daily' ? 'selected' : '' }}>يومي</option>
                            <option value="monthly" {{ request('parking_type') == 'monthly' ? 'selected' : '' }}>شهري
                            </option>

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
                            <td>
                                @switch($history->parking_type)
                                    @case('hourly')
                                        ساعي
                                        @break
                                    @case('daily')
                                        يومي
                                        @break
                                    @case('monthly')
                                        شهري
                                        @break
                                    @default
                                        {{ $history->parking_type }}
                                @endswitch
                            </td>
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
                        @if ($filteredHistories->onFirstPage())
                            <span class="disabled">Previous</span>
                        @else
                            <a href="{{ $filteredHistories->previousPageUrl() }}" rel="prev">Previous</a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($filteredHistories->getUrlRange(max($filteredHistories->currentPage() - 2, 1), min($filteredHistories->currentPage() + 2, $filteredHistories->lastPage())) as $page => $url)
                            @if ($page == $filteredHistories->currentPage())
                                <span class="current">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($filteredHistories->hasMorePages())
                            <a href="{{ $filteredHistories->nextPageUrl() }}" rel="next">Next</a>
                        @else
                            <span class="disabled">Next</span>
                        @endif
                    </div>
                @endif
            </div>

            <button onclick="openReportModal()" class="button2">التقرير</button>
        </div>
    </section>

    <!-- Report Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReportModal()">&times;</span>
            <div id="reportContent">
                <!-- Report content will be loaded here -->
            </div>
            <div class="report-actions">
                <button onclick="printReport()" class="button2">اطبع</button>
            </div>
        </div>
    </div>

    <script>
        function openReportModal() {
            const modal = document.getElementById('reportModal');
            const reportContent = document.getElementById('reportContent');

            // Show loading state
            reportContent.innerHTML = 'Loading report...';
            modal.style.display = 'block';

            // Fetch report content
            fetch(`{{ route('history.report', request()->query()) }}`)
                .then(response => response.text())
                .then(html => {
                    // Extract the table and summary content from the response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const table = doc.querySelector('.table1');
                    const summary = doc.querySelectorAll('p');

                    // Build the report content
                    let content = '<h1>Report</h1>';
                    content += table.outerHTML;
                    content += '<div class="summary">';
                    summary.forEach(p => {
                        content += p.outerHTML;
                    });
                    content += '</div>';

                    reportContent.innerHTML = content;
                })
                .catch(error => {
                    reportContent.innerHTML = 'Error loading report: ' + error.message;
                });
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
        }

        function printReport() {
            const printContent = document.getElementById('reportContent').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <div class="print-content">
                    ${printContent}
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContent;

            // Reattach event listeners
            document.querySelector('.close').onclick = closeReportModal;
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('reportModal');
            if (event.target == modal) {
                closeReportModal();
            }
        }
    </script>
</body>

</html>