<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoice')</title>
    <style>
        body {
            font-family: Calibri;
            font-size: 8pt;
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
        .tabel {
            padding-top: 1em;
            padding-bottom: 1em;
        }
        .tabel-header {
            text-align: left;
        }
        .tabel th {
            background-color: #D7D3BF;
        }
        .tabel th,
        .tabel td {
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
        .signature-section {
            margin-top: 40px;
            margin-left: 400px;
            text-align: center;
            position: relative;
        }
        .signature-section img.stamp {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%) rotate(-30deg);
            max-width: 200px;
            opacity: 0.8;
        }
        .signature-section img.signature {
            max-width: 120px;
            display: block;
            margin: 0 auto;
        }
        .signature-section p {
            margin: 0px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
