<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nota')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .nota {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            font-weight: bold;
        }
        .sub-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th,
        table td {
            padding: .625em;
            text-align: center;
        }
        .tabel tbody th, .tabel tbody td {
            border: 1px solid #000;
            text-align: left;
            padding: 5px;
        }
        .ttd td, .ttd th {
            padding-top: 2em;
            padding-bottom: 4em;
        }
        .footer {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer div {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer .note {
            border: 1px solid #000;
            padding: 5px;
            width: 100%;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
