function clear(event) {
    event.preventDefault()
    console.log("called")
    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = data.message;
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
            document.getElementById('cart_container').innerHTML = '';
        })
        .catch(error => console.error('Error:', error));
}
