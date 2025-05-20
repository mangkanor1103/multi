<?php
require 'config.php'; // Database connection

$translationResult = '';
$errorMessage = '';

// Function to get translations word by word
function translateSentence($conn, $sentence, $sourceColumn, $targetColumn) {
    $words = explode(" ", trim($sentence)); // Split sentence into words
    $translatedWords = [];
    $missingWordCount = 0; // Count of missing words

    $stmt = $conn->prepare("SELECT $targetColumn FROM translation WHERE $sourceColumn = ?");

    foreach ($words as $word) {
        $stmt->bind_param("s", $word);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $translatedWords[] = htmlspecialchars($row[$targetColumn]); // Prevent XSS
        } else {
            $translatedWords[] = htmlspecialchars($word); // Keep original word if not found
            $missingWordCount++; // Increment missing word count
        }
    }

    $stmt->close();
    if ($missingWordCount > 1) {
        return ['', true]; // Return empty if more than one word is missing
    } else {
        return [implode(" ", $translatedWords), false]; // Return translated sentence and no missing words
    }
}

// Process GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['sentence']) && !empty($_GET['source']) && !empty($_GET['target'])) {
        $sentence = $_GET['sentence'];
        $sourceColumn = $_GET['source'];
        $targetColumn = $_GET['target'];

        list($translationResult, $noTranslation) = translateSentence($conn, $sentence, $sourceColumn, $targetColumn);
        if ($noTranslation) {
            $errorMessage = "No translation found for the given sentence.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Language Translator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f9ff;
            background-image: 
                radial-gradient(at 80% 10%, rgba(59, 130, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 20% 90%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .custom-transition {
            transition: all 0.3s ease;
        }
        
        .language-option {
            transition: all 0.2s ease;
        }
        
        .language-option:hover {
            transform: translateY(-2px);
        }
        
        .hero-mini {
            background: linear-gradient(to right, #4f46e5, #3b82f6);
        }
        
        .compact-hero {
            height: auto;
            padding: 1.5rem 0;
        }
    </style>
    <script>
        // Voice recognition functionality
        document.addEventListener('DOMContentLoaded', () => {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            const sentenceInput = document.querySelector('textarea[name="sentence"]');
            const voiceButton = document.getElementById('voiceButton');
            const speakButton = document.getElementById('speakButton');
            const translationText = document.getElementById('translationText');

            recognition.lang = 'en-US'; 
            recognition.interimResults = false;

            voiceButton.addEventListener('click', () => {
                recognition.start();
                voiceButton.classList.add('animate-pulse');
                voiceButton.innerText = 'Listening...';
            });

            recognition.addEventListener('result', (event) => {
                const transcript = event.results[0][0].transcript;
                sentenceInput.value = transcript;
                voiceButton.classList.remove('animate-pulse');
                voiceButton.innerHTML = '<i class="fas fa-microphone mr-2"></i>Speak';
                document.querySelector('form').submit();
            });

            recognition.addEventListener('error', (event) => {
                voiceButton.classList.remove('animate-pulse');
                voiceButton.innerHTML = '<i class="fas fa-microphone mr-2"></i>Speak';
                alert('Voice recognition error: ' + event.error);
            });

            if(speakButton) {
                speakButton.addEventListener('click', () => {
                    const utterance = new SpeechSynthesisUtterance(translationText.textContent);
                    window.speechSynthesis.speak(utterance);
                    speakButton.classList.add('animate-pulse');
                    setTimeout(() => {
                        speakButton.classList.remove('animate-pulse');
                    }, 2000);
                });
            }
        });
    </script>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Compact Navigation -->
    <nav class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-3 px-6 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-language text-2xl"></i>
                <h1 class="text-xl font-bold">MultiLingual</h1>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-blue-100 font-medium">Home</a>
                <a href="test.php" class="hover:text-blue-100 font-medium">Mangyan Alphabet</a>
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
        <a href="test.php" class="block py-2 hover:bg-blue-700 px-3 rounded">Mangyan Alphabet</a>
        <a href="#about" class="block py-2 hover:bg-blue-700 px-3 rounded">About</a>
    </div>

    <!-- Mini Hero Section - Compact Version -->
    <div class="hero-mini compact-hero text-white px-6">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="py-2">
                <h1 class="text-2xl font-bold">Translate Between Languages</h1>
                <p class="text-sm md:text-base opacity-90">Tagalog, Mangyan, and English Translation</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-globe-asia text-4xl text-blue-100"></i>
            </div>
        </div>
    </div>

    <!-- Main Content - Translation First -->
    <main class="flex-grow px-4 md:px-6 pt-4 pb-12">
        <div class="max-w-5xl mx-auto">
            <!-- Translation Card - Now First -->
            <div class="glass-card rounded-2xl overflow-hidden mb-8">
                <!-- Tabs for Translation Type -->
                <div class="flex border-b border-gray-200">
                    <button class="flex-1 py-3 font-medium text-blue-600 border-b-2 border-blue-600">
                        <i class="fas fa-exchange-alt mr-2"></i> Text Translation
                    </button>
                    <button class="flex-1 py-3 font-medium text-gray-500 hover:text-gray-800 custom-transition">
                        <i class="fas fa-book mr-2"></i> Dictionary
                    </button>
                </div>
                
                <!-- Translation Form -->
                <form action="" method="GET" class="p-4 md:p-6">
                    <!-- Language Selection Area - More Compact -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 md:space-x-4">
                        <!-- Source Language -->
                        <div class="w-full md:w-5/12 mb-3 md:mb-0">
                            <label class="block text-gray-700 font-medium mb-2">Translate from:</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="language-option">
                                    <input type="radio" name="source" value="tagalog" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">Tagalog</span>
                                    </div>
                                </label>
                                <label class="language-option">
                                    <input type="radio" name="source" value="mangyan" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">Mangyan</span>
                                    </div>
                                </label>
                                <label class="language-option">
                                    <input type="radio" name="source" value="english" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">English</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Switch Icon -->
                        <div class="flex items-center justify-center mb-3 md:mb-0 transform rotate-90 md:rotate-0">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-blue-500"></i>
                            </div>
                        </div>
                        
                        <!-- Target Language -->
                        <div class="w-full md:w-5/12">
                            <label class="block text-gray-700 font-medium mb-2">Translate to:</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="language-option">
                                    <input type="radio" name="target" value="tagalog" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">Tagalog</span>
                                    </div>
                                </label>
                                <label class="language-option">
                                    <input type="radio" name="target" value="mangyan" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">Mangyan</span>
                                    </div>
                                </label>
                                <label class="language-option">
                                    <input type="radio" name="target" value="english" class="hidden peer" required>
                                    <div class="h-full flex flex-col items-center justify-center p-3 border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                        <span class="font-medium text-gray-800">English</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input and Output Area -->
                    <div class="space-y-4">
                        <!-- Text Input Area -->
                        <div class="relative">
                            <textarea name="sentence" rows="3" placeholder="Enter text to translate..." class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" required></textarea>
                            <div class="absolute right-3 bottom-3 flex space-x-2">
                                <button type="button" id="voiceButton" class="flex items-center justify-center bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">
                                    <i class="fas fa-microphone mr-2"></i>Speak
                                </button>
                                <button type="submit" class="flex items-center justify-center bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600">
                                    <i class="fas fa-arrow-right mr-2"></i>Translate
                                </button>
                            </div>
                        </div>
                        
                        <!-- Translation Result Area -->
                        <?php if ($translationResult): ?>
                        <div class="bg-white border border-gray-300 rounded-xl p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-gray-700">Translation Result</h3>
                                <button type="button" id="speakButton" class="text-blue-500 hover:text-blue-700 flex items-center">
                                    <i class="fas fa-volume-up mr-1"></i> Listen
                                </button>
                            </div>
                            <p id="translationText" class="text-gray-800 text-lg"><?php echo $translationResult; ?></p>
                        </div>
                        <?php elseif ($errorMessage): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 flex items-start">
                            <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                            <p><?php echo $errorMessage; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-language text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-base mb-1">Multiple Languages</h3>
                    <p class="text-gray-600 text-sm">Translate between Tagalog, Mangyan, and English effortlessly.</p>
                </div>
                
                <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-microphone text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-base mb-1">Voice Input</h3>
                    <p class="text-gray-600 text-sm">Speak your text for quick and hands-free translation.</p>
                </div>
                
                <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-volume-up text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-base mb-1">Audio Pronunciation</h3>
                    <p class="text-gray-600 text-sm">Listen to the pronunciation of translated text.</p>
                </div>
            </div>
            
            <!-- About Section -->
            <div id="about" class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-3 text-gray-800">About Our Translation Tool</h2>
                <p class="text-gray-700 mb-3 text-sm">
                    Our multi-language translator bridges linguistic gaps between Tagalog, Mangyan, and English. 
                    This tool is designed to preserve indigenous languages while making communication accessible.
                </p>
                <p class="text-gray-700 text-sm">
                    Whether you're learning a new language, conducting research, or facilitating communication, 
                    our translator helps you break down language barriers with ease.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer Section - More Compact -->
    <footer class="bg-gray-800 text-gray-300 py-6 px-4 mt-auto">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start space-x-2">
                        <i class="fas fa-language text-xl"></i>
                        <h2 class="text-lg font-bold text-white">MultiLingual</h2>
                    </div>
                    <p class="text-xs mt-1">Breaking language barriers since 2025</p>
                </div>
                
                <div class="flex space-x-6 mb-4 md:mb-0 text-sm">
                    <div>
                        <h3 class="font-medium text-white mb-1">Links</h3>
                        <ul class="space-y-1">
                            <li><a href="index.php" class="hover:text-white transition">Home</a></li>
                            <li><a href="test.php" class="hover:text-white transition">Mangyan Alphabet</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-4 pt-4 flex flex-col md:flex-row justify-between items-center text-xs">
                <p class="mb-2 md:mb-0">&copy; 2025 MultiLingual Translator</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('mobileMenu').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenuItems');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>