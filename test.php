
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mangyan Alphabet | MultiLingual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            background-image: 
                radial-gradient(at 70% 20%, rgba(129, 140, 248, 0.1) 0px, transparent 50%),
                radial-gradient(at 30% 70%, rgba(168, 85, 247, 0.1) 0px, transparent 50%);
        }
        
        .keyboard-key {
            @apply bg-white hover:bg-indigo-50 text-gray-800 font-semibold py-2 px-3 rounded-lg 
            shadow-sm transition duration-150 ease-in-out flex items-center justify-center;
        }
        
        .keyboard-key:hover img {
            transform: scale(1.15);
            transition: transform 0.2s ease;
        }
        
        .character-display {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .category-tab {
            @apply px-3 py-1 font-medium rounded-t-lg transition-colors duration-200;
        }
        
        .category-tab.active {
            @apply bg-white text-indigo-700 font-semibold;
        }
        
        .category-tab:not(.active) {
            @apply bg-gray-100 text-gray-600 hover:bg-gray-200;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-3 px-6 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-language text-2xl"></i>
                <h1 class="text-xl font-bold">MultiLingual</h1>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-blue-100 font-medium">Home</a>
                <a href="test.php" class="text-white font-medium border-b-2 border-white">Mangyan Alphabet</a>
                <a href="about.php" class="hover:text-blue-100 font-medium">About</a>
            </div>
            <div class="md:hidden">
                <button id="mobileMenu" class="focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu (hidden by default) -->
    <div id="mobileMenuItems" class="hidden bg-blue-600 text-white py-4 px-6 md:hidden">
        <a href="index.php" class="block py-2 hover:bg-blue-700 px-3 rounded">Home</a>
        <a href="test.php" class="block py-2 bg-blue-700 px-3 rounded">Mangyan Alphabet</a>
        <a href="#about" class="block py-2 hover:bg-blue-700 px-3 rounded">About</a>
    </div>

    <!-- Combined Header and Display Section -->
    <div class="bg-gradient-to-b from-indigo-600 to-blue-500 text-white py-4 px-4 md:px-6">
        <div class="max-w-5xl mx-auto">
            <!-- Two Column Layout -->
            <div class="flex flex-col md:flex-row gap-4 md:gap-8 items-center">
                <!-- Header Text -->
                <div class="md:w-1/2 text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Explore the Mangyan Alphabet</h1>
                    <p class="text-sm md:text-base opacity-90 max-w-md">
                        Discover the beautiful and unique characters of the indigenous Mangyan writing system
                    </p>
                </div>
                
                <!-- Display Selected Letter - Incorporated into header -->
                <div class="md:w-1/2">
                    <div id="letterDisplay" class="character-display p-4 rounded-xl bg-white/90 text-gray-800 flex items-center justify-center gap-4">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-100 rounded-full blur-lg opacity-50"></div>
                            <div class="relative">
                                <img id="displayImage" src="" alt="Selected Letter" class="w-20 h-20 object-contain hidden">
                            </div>
                        </div>
                        <div class="text-center md:text-left">
                            <p class="text-gray-500 text-xs">Currently Selected:</p>
                            <h2 id="displayText" class="text-2xl font-bold text-gray-800">Click a character</h2>
                            <p id="pronounciation" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-5xl mx-auto px-4 md:px-6 py-4">
        <!-- Category Tabs -->
        <div class="flex space-x-2 mb-3 overflow-x-auto pb-1">
            <button class="category-tab active" data-category="all">All</button>
            <button class="category-tab" data-category="consonants-a">+ A</button>
            <button class="category-tab" data-category="consonants-i">+ I</button>
            <button class="category-tab" data-category="consonants-u">+ U</button>
        </div>

        <!-- Virtual Keyboard -->
        <section class="bg-white p-4 rounded-xl shadow-sm mb-6">
            <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                <!-- First Row - Consonants + A -->
                <button onclick="showLetter('KA', 'surat/ka.png', 'Pronounced as KA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ka.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="KA">
                </button>
                <button onclick="showLetter('GA', 'surat/ga.png', 'Pronounced as GA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ga.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="GA">
                </button>
                <button onclick="showLetter('NGA', 'surat/nga.png', 'Pronounced as NGA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/nga.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NGA">
                </button>
                <button onclick="showLetter('TA', 'surat/ta.png', 'Pronounced as TA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ta.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="TA">
                </button>
                <button onclick="showLetter('DA', 'surat/da.png', 'Pronounced as DA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/da.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="DA">
                </button>
                <button onclick="showLetter('NA', 'surat/na.png', 'Pronounced as NA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/na.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NA">
                </button>
                <button onclick="showLetter('PA', 'surat/pa.png', 'Pronounced as PA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/pa.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="PA">
                </button>
                <button onclick="showLetter('BA', 'surat/ba.png', 'Pronounced as BA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ba.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="BA">
                </button>
                <button onclick="showLetter('MA', 'surat/ma.png', 'Pronounced as MA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ma.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="MA">
                </button>
                <button onclick="showLetter('YA', 'surat/ya.png', 'Pronounced as YA')" class="keyboard-key" data-category="consonants-a">
                    <img src="surat/ya.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="YA">
                </button>

                <!-- Second Row - Consonants + I -->
                <button onclick="showLetter('KI', 'surat/ki.png', 'Pronounced as KI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/ki.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="KI">
                </button>
                <button onclick="showLetter('GI', 'surat/gi.png', 'Pronounced as GI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/gi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="GI">
                </button>
                <button onclick="showLetter('NGI', 'surat/ngi.png', 'Pronounced as NGI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/ngi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NGI">
                </button>
                <button onclick="showLetter('TI', 'surat/ti.png', 'Pronounced as TI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/ti.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="TI">
                </button>
                <button onclick="showLetter('DI', 'surat/di.png', 'Pronounced as DI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/di.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="DI">
                </button>
                <button onclick="showLetter('NI', 'surat/ni.png', 'Pronounced as NI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/ni.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NI">
                </button>
                <button onclick="showLetter('PI', 'surat/pi.png', 'Pronounced as PI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/pi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="PI">
                </button>
                <button onclick="showLetter('BI', 'surat/bi.png', 'Pronounced as BI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/bi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="BI">
                </button>
                <button onclick="showLetter('MI', 'surat/mi.png', 'Pronounced as MI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/mi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="MI">
                </button>
                <button onclick="showLetter('YI', 'surat/yi.png', 'Pronounced as YI')" class="keyboard-key" data-category="consonants-i">
                    <img src="surat/yi.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="YI">
                </button>

                <!-- Third Row - Consonants + U -->
                <button onclick="showLetter('KU', 'surat/ku.png', 'Pronounced as KU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/ku.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="KU">
                </button>
                <button onclick="showLetter('GU', 'surat/gu.png', 'Pronounced as GU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/gu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="GU">
                </button>
                <button onclick="showLetter('NGU', 'surat/ngu.png', 'Pronounced as NGU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/ngu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NGU">
                </button>
                <button onclick="showLetter('TU', 'surat/tu.png', 'Pronounced as TU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/tu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="TU">
                </button>
                <button onclick="showLetter('DU', 'surat/du.png', 'Pronounced as DU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/du.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="DU">
                </button>
                <button onclick="showLetter('NU', 'surat/nu.png', 'Pronounced as NU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/nu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="NU">
                </button>
                <button onclick="showLetter('PU', 'surat/pu.png', 'Pronounced as PU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/pu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="PU">
                </button>
                <button onclick="showLetter('BU', 'surat/bu.png', 'Pronounced as BU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/bu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="BU">
                </button>
                <button onclick="showLetter('MU', 'surat/mu.png', 'Pronounced as MU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/mu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="MU">
                </button>
                <button onclick="showLetter('YU', 'surat/yu.png', 'Pronounced as YU')" class="keyboard-key" data-category="consonants-u">
                    <img src="surat/yu.png" class="w-8 h-8 md:w-10 md:h-10 transition-transform" alt="YU">
                </button>
            </div>
        </section>

        <!-- Information Accordion -->
        <section class="mb-8">
            <div class="border rounded-xl overflow-hidden">
                <div class="bg-white">
                    <button id="toggleAboutInfo" class="w-full text-left p-4 font-medium text-indigo-700 flex items-center justify-between">
                        <span class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            About Mangyan Writing
                        </span>
                        <i class="fas fa-chevron-down transition-transform" id="aboutInfoIcon"></i>
                    </button>
                    <div id="aboutInfoContent" class="hidden px-4 pb-4">
                        <p class="text-gray-700 mb-3 text-sm">
                            The Mangyan script (Surat Mangyan) is an indigenous writing system used by the 
                            Mangyan people of Mindoro, Philippines. It's one of the few pre-Spanish writing 
                            systems that is still in use today.
                        </p>
                        <p class="text-gray-700 text-sm">
                            The script is written from bottom to top and from left to right, making it unique 
                            among Philippine scripts. Its preservation is crucial for maintaining the cultural 
                            heritage of the Mangyan people.
                        </p>
                    </div>
                </div>

                <div class="bg-white border-t">
                    <button id="toggleHowToUse" class="w-full text-left p-4 font-medium text-indigo-700 flex items-center justify-between">
                        <span class="flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>
                            How to Use This Tool
                        </span>
                        <i class="fas fa-chevron-down transition-transform" id="howToUseIcon"></i>
                    </button>
                    <div id="howToUseContent" class="hidden px-4 pb-4">
                        <ul class="text-gray-700 space-y-2 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Click on any character to see it enlarged</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Use the category tabs to filter characters</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Learn the pronunciation of each character</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Return to the translator to practice what you've learned</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section - More Compact -->
    <footer class="bg-gray-800 text-white py-6 px-4 mt-auto">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start space-x-2">
                        <i class="fas fa-language text-xl"></i>
                        <h2 class="text-lg font-bold text-white">MultiLingual</h2>
                    </div>
                    <p class="text-xs mt-1">Preserving indigenous languages since 2025</p>
                </div>
                
                <div class="flex mb-4 md:mb-0 text-sm">
                    <div>
                        <ul class="flex space-x-4">
                            <li><a href="index.php" class="hover:text-white transition">Home</a></li>
                            <li><a href="test.php" class="hover:text-white transition">Mangyan Alphabet</a></li>
                            <li><a href="#about" class="hover:text-white transition">About</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-4 pt-4 flex flex-col md:flex-row justify-between items-center text-xs">
                <p>&copy; 2025 MultiLingual</p>
                <p>Made with ❤️ for cultural preservation</p>
            </div>
        </div>
    </footer>

    <script>
        // Show letter function
        function showLetter(letter, imageSrc, pronunciation) {
            const displayText = document.getElementById('displayText');
            const displayImage = document.getElementById('displayImage');
            const pronounciationText = document.getElementById('pronounciation');

            displayText.textContent = letter;
            pronounciationText.textContent = pronunciation;
            displayImage.src = imageSrc;
            displayImage.classList.remove('hidden');
            
            // Add a subtle animation
            displayText.classList.add('animate-pulse');
            setTimeout(() => {
                displayText.classList.remove('animate-pulse');
            }, 500);
        }

        // Category tabs functionality
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                document.querySelectorAll('.category-tab').forEach(t => {
                    t.classList.remove('active');
                });
                tab.classList.add('active');

                // Filter keys
                const category = tab.dataset.category;
                const keys = document.querySelectorAll('.keyboard-key');
                
                if (category === 'all') {
                    keys.forEach(key => key.classList.remove('hidden'));
                } else {
                    keys.forEach(key => {
                        if (key.dataset.category === category) {
                            key.classList.remove('hidden');
                        } else {
                            key.classList.add('hidden');
                        }
                    });
                }
            });
        });

        // Mobile menu toggle
        document.getElementById('mobileMenu')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenuItems');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        });

        // Toggle accordion sections
        document.getElementById('toggleAboutInfo').addEventListener('click', function() {
            const content = document.getElementById('aboutInfoContent');
            const icon = document.getElementById('aboutInfoIcon');
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });

        document.getElementById('toggleHowToUse').addEventListener('click', function() {
            const content = document.getElementById('howToUseContent');
            const icon = document.getElementById('howToUseIcon');
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    </script>
</body>
</html>