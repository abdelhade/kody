<!DOCTYPE html>
<html>
<head>
    <title>Test Payment</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <button onclick="testPayment()">Test Payment</button>
    
    <script>
    function testPayment() {
        $.ajax({
            url: 'ajax/process_payment.php',
            method: 'POST',
            data: { 
                table_id: 1,
                amount: 150.00
            },
            success: function(response) {
                console.log('Raw response:', response);
                try {
                    var data = JSON.parse(response);
                    console.log('Parsed data:', data);
                    alert('Success: ' + JSON.stringify(data));
                } catch(e) {
                    console.log('Parse error:', e);
                    alert('Parse error: ' + response);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                alert('AJAX Error: ' + error);
            }
        });
    }
    </script>
</body>
</html>