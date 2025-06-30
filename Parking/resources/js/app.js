function showNewCustomer() {  
    document.getElementById('newCustomerFields').style.display = 'block';  
    document.getElementById('newCustomerPhone').style.display = 'block';  
    document.getElementById('oldCustomerFields').style.display = 'none';  
}  

function showOldCustomer() {  
    document.getElementById('newCustomerFields').style.display = 'none';  
    document.getElementById('newCustomerPhone').style.display = 'none';  
    document.getElementById('oldCustomerFields').style.display = 'block';  
}  

// Initialize with New Customer form visible  
window.onload = showNewCustomer;