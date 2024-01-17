<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby</title>
    <style>
        body {
            background-color: #000;
            color: #FFF;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        button {
            background-color: #CAD704;
            color: #000;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <button onclick="startGame()">Start Game</button>
    <button onclick="changePlayer()">Change Player</button>

    <script>
        const startGame = async () =>  {
            window.location.href = '/game'
        }

        const changePlayer = async () => {
            window.location.href = '/logout'
        }
    </script>
</body>
</html>
