<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script>
        const eventSource = new EventSource('/sse');

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            console.log("Received message:", data.message);
        };

        eventSource.onerror = function(error) {
            console.error("SSE connection error:", error);
        };
    </script>

</head>

<body>
    <h1>Create a New User</h1>
    <form id="user-form">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit">Create User</button>
    </form>
    <div id="message"></div>
</body>

</html>