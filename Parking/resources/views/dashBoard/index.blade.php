<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard</title>
</head>

<body>
    <section class="board">
        <h1>hello</h1>
    </section>
    <section class="side">

    
    <section class="chick-in">
        <div class="oon">  
            <button type="button" class="button1" onclick="showNewCustomer()">New</button>  
            <button type="button" class="button1" onclick="showOldCustomer()">Old</button>  
        </div>  
       <form action="submit" id="form1" class="new-form">  
         <h2>Enter a Customer</h2>  
         <div class="form">  
        <div class="input-form" >  
            <input type="text" class="inp-text" placeholder="name...." id="nameInput">  
            <label for="nameInput">: الاسم الكامل</label>  
        </div>  
        <div class="input-form" id="newCustomerPhone">  
            <input type="text" class="inp-text" placeholder="phone...." id="phoneInput">  
            <label for="phoneInput">: رقم الهاتف</label>  
        </div>  
       
        
        <div class="input-form">  
            <input type="text" class="inp-text" placeholder="vehicle...." id="vehicleInput">  
            <label for="vehicleInput">: نوع المركبة</label>  
        </div>  
        <div class="input-form">  
            <input type="text" class="inp-text" placeholder="plate...." id="plateInput">  
            <label for="plateInput">: رقم اللوحة</label>  
        </div>  
    </div>  
</form>  
<form action="supmit" id="form2" class="old-form">
      <h2>Select a Customer</h2> 
      <div class="form">
        <div class="input-form " >  
            <select class="inp-text" id="customerSelect">  
                <option value="">Select an Old Customer</option>  
              
                <option value="customer1">Customer 1</option>  
                <option value="customer2">Customer 2</option>  
                <option value="customer3">Customer 3</option>  
            </select>  
            <label for="customerSelect">: اختيار عميل قديم</label>  
        </div>  
        <div class="input-form">
            <input type="text" class="inp-text" placeholder="vehicle...." id="vehicleInput">
            <label for="vehicleInput">: نوع المركبة</label>
        </div>
        <div class="input-form">
            <input type="text" class="inp-text" placeholder="plate...." id="plateInput">
            <label for="plateInput">: رقم اللوحة</label>
        </div>
        </div>
</form>
</section>
<section class="service">
    <h1>hello</h1>
</section>
</section>
</body>
    <script>
                function showNewCustomer() {
                    document.getElementById('form1').style.display = 'block';
                    document.getElementById('form2').style.display = 'none';
            
    }

                function showOldCustomer() {
                    document.getElementById('form2').style.display = 'block';
                    document.getElementById('form1').style.display = 'none';
                
    }

                // Initialize with New Customer form visible  
                window.onload = showNewCustomer;  
    </script>

</html>