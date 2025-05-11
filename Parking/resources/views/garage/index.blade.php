<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>garage</title>
</head>
<body>
    <section class="board">
        <div class="list-container">
            @if(session('new_parcode'))
                <div class="success-message"
                    style="background-color: var(--secondary-color); color: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;">
                    تم إنشاء رمز وقوف السيارات بنجاح: {{ session('new_parcode') }}
                </div>
            @endif

            @if(session('success'))
                <div class="success-message"
                    style="background-color: var(--secondary-color); color: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="search-container" style="margin-bottom: 15px; display: flex; align-items: center;">
                <input type="text" id="tableSearch" class="inp-text" placeholder="search..."
                    style="flex: 1; padding: 4px; border-radius: 5px; margin-left:5px">
                <label for="tableSearch" style="padding-left: 5px">:ابحث</label>
            </div>

            <table class="table1">
                <tr>
                    <th>ID</th>
                    <th>الزبون</th>
                    <th>فئة المركبة</th>
                    <th>نوع المركبة</th>
                    <th>رقم اللوحة</th>
                    <th>وقت الدخول</th>
                    <th>نوع الوقوف</th>
                    <th>خدمات</th>
                    <th>تحرير</th>
                </tr>
                @foreach($parking_slots as $parking_slot)
                            <tr class="data" data-parking-slot-id="{{ $parking_slot->id }}">
                                <td onclick="showParcodePopup('{{ $parking_slot->parcode }}')" style="cursor:pointer;">
                                    {{$parking_slot->id}}
                                </td>
                                <td>{{$parking_slot->vics->customer->name}}</td>
                                <td>
                                    <img src="{{ $parking_slot->vics->typ == "مركبة صغيرة" ? asset('build/assets/motor.svg') : asset('build/assets/car.svg') }}"
                                        alt={{$parking_slot->vics->typ == "مركبة صغيرة" ? "Motor" : "Car"}} width="40">
                                </td>
                                <td>{{$parking_slot->vics->brand}}</td>
                                <td>{{$parking_slot->vics->plate}}</td>
                                <td>{{ \Carbon\Carbon::parse($parking_slot->time_in)->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    {{
                    $parking_slot->parking_type === 'hourly' ? 'ساعي' :
                    ($parking_slot->parking_type === 'daily' ? 'يومي' :
                        ($parking_slot->parking_type === 'monthly' ? 'شهري' : $parking_slot->parking_type))
                                        }}
                                </td>
                                <td>
                                    @if($parking_slot->vics->services->count() > 0 || $parking_slot->vics->items->count() > 0)
                                        @foreach($parking_slot->vics->services as $service)
                                            @if($service->pivot->parking_slot_id == $parking_slot->id)
                                                <li>
                                                    {{ $service->name }}
                                                    ( التكلفة: {{ $service->cost }})
                                                </li>
                                            @endif
                                        @endforeach

                                        @foreach($parking_slot->vics->items as $item)
                                            <li>
                                                {{$item->item}}
                                                العدد : {{$item->pivot->item_quantity}}
                                            </li>
                                        @endforeach
                                    @else
                                        لا يوجد خدمات!
                                    @endif
                                </td>

                                <td>

                                    <a href="{{ route('dashboard.checkout', ['parcode' => $parking_slot->parcode]) }}"
                                        class="btn checkout-btn">خروج</a>

                                    <a onclick="openServicePopup({{ $parking_slot->vics->id }}, {{ $parking_slot->id }})"
                                        class="serv-btn"> خدمة</a>
                                </td>

                            </tr>
                @endforeach


            </table>
        </div>
    </section>
</body>
</html>