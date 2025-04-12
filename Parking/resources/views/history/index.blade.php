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
            <h2>Parking Session History</h2>

            <!-- Filtering Form -->
            <form method="GET" action="{{ route('history.index') }}" class="filter-form">
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="customer_name" class="inp-text" 
                               placeholder="Customer Name" 
                               value="{{ request('customer_name') }}">
                        <label>Customer Name</label>
                    </div>

                    <div class="input-form">
                        <select name="vic_typ" class="inp-text">
                            <option value="">All Vehicle Types</option>
                            <option value="car" {{ request('vic_typ') == 'car' ? 'selected' : '' }}>Car</option>
                            <option value="moto" {{ request('vic_typ') == 'moto' ? 'selected' : '' }}>Motorcycle</option>
                        </select>
                        <label>Vehicle Type</label>
                    </div>

                    <div class="input-form">
                        <input type="date" name="start_date" class="inp-text" 
                               value="{{ request('start_date') }}">
                        <label>Start Date</label>
                    </div>

                    <div class="input-form">
                        <input type="date" name="end_date" class="inp-text" 
                               value="{{ request('end_date') }}">
                        <label>End Date</label>
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
                        <th>Customer</th>
                        <th>Vehicle Type</th>
                        <th>Vehicle Plate</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Duration (mins)</th>
                        <th>Total Price</th>
                        <th>Services</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr>
                            <td>{{ $history->customer_name }}</td>
                            <td>{{ $history->vic_typ }}</td>
                            <td>{{ $history->vic_plate }}</td>
                            <td>{{ $history->time_in ? \Carbon\Carbon::parse($history->time_in)->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>{{ $history->time_out ? \Carbon\Carbon::parse($history->time_out)->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>{{ number_format($history->duration ?? 'N/A',2) }}</td>
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
                                    No additional services
                                @endif
                            </td>
                            <td>{{$history->notes}} </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No history records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $histories->appends(request()->input())->links() }}
            </div>
        </div>
    </section>
</body>
</html>