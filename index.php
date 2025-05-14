<!-- filepath: c:\xampp\htdocs\multi\index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanuno Translation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        /* Moving gradient background */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #84fab0, #8fd3f4);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Enhanced button styles */
        a {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        a:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        /* Card shadow for sections */
        .card {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="text-gray-800">
    <!-- Header Section -->
    <header class="bg-green-500 text-white py-8 shadow-lg">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-bold">Welcome to Hanuno Translation</h1>
            <p class="text-lg mt-2">Bridging the gap between Tagalog and Mangyan languages</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto mt-12 text-center">
        <!-- Intro Section -->
        <section class="mb-12">
            <img src="https://via.placeholder.com/600x300" alt="Translation Illustration" class="mx-auto rounded-lg shadow-lg mb-6">
            <p class="text-lg mb-4">Hanuno Translation is a powerful tool designed to help you translate between Tagalog and Mangyan languages with ease.</p>
            <p class="text-lg">Whether you're learning a new language or connecting with others, we've got you covered.</p>
        </section>

        <!-- Buttons Section -->
        <section class="mb-12">
            <h2 class="text-3xl font-semibold mb-6">Get Started</h2>
            <div class="flex justify-center space-x-6">
                <a href="home.php" class="bg-green-500 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-green-600 transition shadow-lg">
                    Mangyan Translation
                </a>
                <a href="test.php" class="bg-blue-500 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-blue-600 transition shadow-lg">
                    Mangyan Keyboard
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-white p-8 rounded-lg card">
            <h2 class="text-3xl font-semibold mb-6">Why Choose Hanuno Translation?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">Accurate Translations</h3>
                    <p class="text-sm">Our tool ensures precise translations to help you communicate effectively.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">User-Friendly</h3>
                    <p class="text-sm">Designed with simplicity in mind, making it easy for everyone to use.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">Free to Use</h3>
                    <p class="text-sm">Enjoy all the features of Hanuno Translation at no cost.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-sm">&copy; 2025 Hanuno Translation. All rights reserved.</p>
            <p class="text-sm">Made with ❤️ for language enthusiasts.</p>
        </div>
    </footer>
</body>
</html>