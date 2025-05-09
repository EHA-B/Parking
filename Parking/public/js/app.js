// Function to generate barcode
function generateBarcode(parcode) {
    const barcodeElement = document.getElementById('barcode');
    JsBarcode(barcodeElement, parcode, {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 100,
        displayValue: true
    });
}

// Function to print barcode
function printBarcode() {
    const printWindow = window.open('', '_blank');
    const barcodeElement = document.getElementById('barcode').cloneNode(true);
    
    printWindow.document.write(`
        <html>
            <head>
                <title>طباعة الباركود</title>
                <style>
                    body { 
                        display: flex; 
                        justify-content: center; 
                        align-items: center; 
                        height: 100vh; 
                        margin: 0; 
                    }
                    svg { 
                        max-width: 100%; 
                        height: auto; 
                    }
                </style>
            </head>
            <body>
                ${barcodeElement.outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Function to close checkout modal
function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

// Function to show checkout modal with customer details
function showCheckoutModal(customerData) {
    document.getElementById('customerName').textContent = customerData.name;
    document.getElementById('customerPhone').textContent = customerData.phone;
    document.getElementById('vehicleType').textContent = customerData.vehicleType;
    document.getElementById('timeIn').textContent = customerData.timeIn;
    document.getElementById('duration').textContent = customerData.duration;
    document.getElementById('price').textContent = customerData.price;
    document.getElementById('checkoutParcode').value = customerData.parcode;
    
    generateBarcode(customerData.parcode);
    document.getElementById('checkoutModal').style.display = 'block';
} 