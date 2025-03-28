document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-to-cart').addEventListener('click', function() {
        const size = document.getElementById('size').value;
        const quantity = document.getElementById('quantity').value;
        
        // Use absolute path from your domain root
        fetch('/CLONR-main/products/includes/cart_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'add_to_cart': '1',
                'size': size,
                'quantity': quantity
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to connect to server. Please try again.');
        });
    });
});