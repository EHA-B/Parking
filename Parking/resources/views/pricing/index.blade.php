<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management</title>
</head>
<body>
    <section class="board">
        <div class="container">
            <h2>Parking Pricing Management</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pricing.update') }}" method="POST">
                @csrf
                <div class="form">
                    <div class="input-form">
                        <input type="number" step="0.01" name="car_price" class="inp-text" 
                               value="{{ old('car_price', $pricing->car_price ?? 0) }}" required>
                        <label>Price per Minute for Cars</label>
                    </div>
                    
                    <div class="input-form">
                        <input type="number" step="0.01" name="moto_price" class="inp-text" 
                               value="{{ old('moto_price', $pricing->moto_price ?? 0) }}" required>
                        <label>Price per Minute for Motorcycles</label>
                    </div>

                    <button type="submit" class="button2">
                        <span class="button-content">Update Pricing</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</body>
</html>