<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div x-data="{ count: 0 }">
    <button @click="count++">Click</button>
    <span x-text="count"></span>
</div>

</body>
</html>
