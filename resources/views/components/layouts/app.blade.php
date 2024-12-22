<!-- resources/views/components/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- CSS and JavaScript Libraries -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    @stack('head') <!-- Untuk menambahkan custom script atau CSS lainnya di halaman tertentu -->
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <header>
            <!-- Navbar atau Header jika ada -->
        </header>
        
        <main>
            {{ $slot }} <!-- Ini akan menampilkan konten dari setiap halaman yang menggunakan layout ini -->
        </main>
    </div>

    <!-- Footer atau script lainnya jika ada -->
    
    @stack('scripts') <!-- Custom JavaScript yang akan dipush oleh halaman-halaman tertentu -->
</body>
</html>
