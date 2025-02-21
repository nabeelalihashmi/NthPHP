<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 600px;
        }
        h1 {
            font-size: 72px;
            margin: 0;
            color: #343a40;
        }
        p {
            font-size: 18px;
            margin: 10px 0;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-size: 18px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 2px;
        }
        a:hover {
            color: #0056b3;
            border-bottom-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Oops! The page you are looking for does not exist.</p>
        <p><a href="{{baseurl('/')}}">Return to Home</a></p>
    </div>
</body>
</html>
