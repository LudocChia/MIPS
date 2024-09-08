<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toggle Button Colors</title>
    <style>
        /* Style for buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn.active {
            background-color: #4CAF50; /* Highlighted color */
            color: white;
        }

        .btn.inactive {
            background-color: transparent; /* Transparent */
            color: black;
            border: 1px solid #4CAF50; /* Optional border */
        }
    </style>
</head>
<body>
    <div>
        <button class="btn active" id="btn1" onclick="toggleActive('btn1')">Button 1</button>
        <button class="btn inactive" id="btn2" onclick="toggleActive('btn2')">Button 2</button>
    </div>

    <script>
        function toggleActive(clickedBtnId) {
            // Get references to both buttons
            var btn1 = document.getElementById('btn1');
            var btn2 = document.getElementById('btn2');
            
            // Check which button is clicked and toggle classes accordingly
            if (clickedBtnId === 'btn1') {
                btn1.classList.add('active');
                btn1.classList.remove('inactive');
                
                btn2.classList.add('inactive');
                btn2.classList.remove('active');
            } else if (clickedBtnId === 'btn2') {
                btn2.classList.add('active');
                btn2.classList.remove('inactive');
                
                btn1.classList.add('inactive');
                btn1.classList.remove('active');
            }
        }
    </script>
</body>
</html>
