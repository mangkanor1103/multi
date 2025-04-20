<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mangyan Virtual Keyboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
    <!-- Header Section -->
    <header class="bg-green-500 text-white py-6 shadow-lg">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl font-bold">Mangyan Virtual Keyboard</h1>
            <p class="text-lg mt-2">Click a key to see the selected letter or word</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto mt-12 text-center">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="index.php" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                &larr; Back to Home
            </a>
        </div>
        <!-- Display Selected Letter -->
        <div id="display" class="bg-white p-8 rounded-lg shadow-md text-2xl font-semibold text-gray-800 flex items-center justify-center space-x-4">
            <img id="displayImage" src="" alt="Selected Letter" class="w-16 h-16 hidden">
            <span id="displayText">Click a key to display the letter or word here.</span>
        </div>

        <!-- Virtual Keyboard -->
        <section class="mt-12">
            <h2 class="text-2xl font-semibold mb-6">Mangyan Keyboard</h2>
            <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                <!-- Main Keyboard Grid -->
                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                    <!-- First Row -->
                    <button onclick="showLetter('KA', 'surat/ka.png')" class="keyboard-key"><img src="surat/ka.png" class="w-8 h-8" alt="KA"></button>
                    <button onclick="showLetter('GA', 'surat/ga.png')" class="keyboard-key"><img src="surat/ga.png" class="w-8 h-8" alt="GA"></button>
                    <button onclick="showLetter('NGA', 'surat/nga.png')" class="keyboard-key"><img src="surat/nga.png" class="w-8 h-8" alt="NGA"></button>
                    <button onclick="showLetter('TA', 'surat/ta.png')" class="keyboard-key"><img src="surat/ta.png" class="w-8 h-8" alt="TA"></button>
                    <button onclick="showLetter('DA', 'surat/da.png')" class="keyboard-key"><img src="surat/da.png" class="w-8 h-8" alt="DA"></button>
                    <button onclick="showLetter('NA', 'surat/na.png')" class="keyboard-key"><img src="surat/na.png" class="w-8 h-8" alt="NA"></button>
                    <button onclick="showLetter('PA', 'surat/pa.png')" class="keyboard-key"><img src="surat/pa.png" class="w-8 h-8" alt="PA"></button>
                    <button onclick="showLetter('BA', 'surat/ba.png')" class="keyboard-key"><img src="surat/ba.png" class="w-8 h-8" alt="BA"></button>
                    <button onclick="showLetter('MA', 'surat/ma.png')" class="keyboard-key"><img src="surat/ma.png" class="w-8 h-8" alt="MA"></button>
                    <button onclick="showLetter('YA', 'surat/ya.png')" class="keyboard-key"><img src="surat/ya.png" class="w-8 h-8" alt="YA"></button>

                    <!-- Second Row -->
                    <button onclick="showLetter('KI', 'surat/ki.png')" class="keyboard-key"><img src="surat/ki.png" class="w-8 h-8" alt="KI"></button>
                    <button onclick="showLetter('GI', 'surat/gi.png')" class="keyboard-key"><img src="surat/gi.png" class="w-8 h-8" alt="GI"></button>
                    <button onclick="showLetter('NGI', 'surat/ngi.png')" class="keyboard-key"><img src="surat/ngi.png" class="w-8 h-8" alt="NGI"></button>
                    <button onclick="showLetter('TI', 'surat/ti.png')" class="keyboard-key"><img src="surat/ti.png" class="w-8 h-8" alt="TI"></button>
                    <button onclick="showLetter('DI', 'surat/di.png')" class="keyboard-key"><img src="surat/di.png" class="w-8 h-8" alt="DI"></button>
                    <button onclick="showLetter('NI', 'surat/ni.png')" class="keyboard-key"><img src="surat/ni.png" class="w-8 h-8" alt="NI"></button>
                    <button onclick="showLetter('PI', 'surat/pi.png')" class="keyboard-key"><img src="surat/pi.png" class="w-8 h-8" alt="PI"></button>
                    <button onclick="showLetter('BI', 'surat/bi.png')" class="keyboard-key"><img src="surat/bi.png" class="w-8 h-8" alt="BI"></button>
                    <button onclick="showLetter('MI', 'surat/mi.png')" class="keyboard-key"><img src="surat/mi.png" class="w-8 h-8" alt="MI"></button>
                    <button onclick="showLetter('YI', 'surat/yi.png')" class="keyboard-key"><img src="surat/yi.png" class="w-8 h-8" alt="YI"></button>

                    <!-- Third Row -->
                    <button onclick="showLetter('KU', 'surat/ku.png')" class="keyboard-key"><img src="surat/ku.png" class="w-8 h-8" alt="KU"></button>
                    <button onclick="showLetter('GU', 'surat/gu.png')" class="keyboard-key"><img src="surat/gu.png" class="w-8 h-8" alt="GU"></button>
                    <button onclick="showLetter('NGU', 'surat/ngu.png')" class="keyboard-key"><img src="surat/ngu.png" class="w-8 h-8" alt="NGU"></button>
                    <button onclick="showLetter('TU', 'surat/tu.png')" class="keyboard-key"><img src="surat/tu.png" class="w-8 h-8" alt="TU"></button>
                    <button onclick="showLetter('DU', 'surat/du.png')" class="keyboard-key"><img src="surat/du.png" class="w-8 h-8" alt="DU"></button>
                    <button onclick="showLetter('NU', 'surat/nu.png')" class="keyboard-key"><img src="surat/nu.png" class="w-8 h-8" alt="NU"></button>
                    <button onclick="showLetter('PU', 'surat/pu.png')" class="keyboard-key"><img src="surat/pu.png" class="w-8 h-8" alt="PU"></button>
                    <button onclick="showLetter('BU', 'surat/bu.png')" class="keyboard-key"><img src="surat/bu.png" class="w-8 h-8" alt="BU"></button>
                    <button onclick="showLetter('MU', 'surat/mu.png')" class="keyboard-key"><img src="surat/mu.png" class="w-8 h-8" alt="MU"></button>
                    <button onclick="showLetter('YU', 'surat/yu.png')" class="keyboard-key"><img src="surat/yu.png" class="w-8 h-8" alt="YU"></button>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-sm">&copy; 2025 Mangyan Translator. All rights reserved.</p>
            <p class="text-sm">Made with ❤️ for cultural preservation.</p>
        </div>
    </footer>

    <script>
        function showLetter(letter, imageSrc) {
            const displayText = document.getElementById('displayText');
            const displayImage = document.getElementById('displayImage');

            displayText.textContent = letter; // Update the text
            displayImage.src = imageSrc; // Update the image source
            displayImage.classList.remove('hidden'); // Show the image
        }
    </script>
</body>
</html>