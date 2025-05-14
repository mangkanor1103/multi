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
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
    <!-- Header Section -->
    <header class="bg-green-500 text-white py-6 shadow-lg">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl font-bold">Multi-Language Translator</h1>
            <p class="text-lg mt-2">Translate between Tagalog, Mangyan, and English</p>
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

        <!-- Translation Form -->
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Translate Between Languages</h2>
            <form action="" method="GET" class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-8">
                <!-- Left Column: Translation Button and Result -->
                <div class="flex-1">
                    <div class="mb-4">
                        <input type="text" name="sentence" placeholder="Enter sentence..." class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">Translate</button>
                    <?php if ($translationResult): ?>
                        <div class="mt-4 bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">Translation Result:</h3>
                            <p class="text-gray-700"><?php echo $translationResult; ?></p>
                        </div>
                    <?php elseif ($errorMessage): ?>
                        <p class="text-red-500 mt-4"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Source and Target Language Options -->
                <div class="flex-1">
                    <div class="mb-4">
                        <label class="block text-left mb-2 font-medium">Source Language:</label>
                        <div class="flex justify-center space-x-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="source" value="tagalog" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="block text-sm font-medium">Tagalog</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="source" value="mangyan" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                                    </svg>
                                    <span class="block text-sm font-medium">Mangyan</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="source" value="english" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                                    </svg>
                                    <span class="block text-sm font-medium">English</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-left mb-2 font-medium">Target Language:</label>
                        <div class="flex justify-center space-x-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="target" value="tagalog" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="block text-sm font-medium">Tagalog</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="target" value="mangyan" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                                    </svg>
                                    <span class="block text-sm font-medium">Mangyan</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="target" value="english" class="hidden peer" required>
                                <div class="p-4 bg-gray-100 rounded-lg shadow-md hover:bg-green-100 peer-checked:bg-green-500 peer-checked:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 peer-checked:text-white text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                                    </svg>
                                    <span class="block text-sm font-medium">English</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-sm">&copy; 2025 Multi-Language Translator. All rights reserved.</p>
            <p class="text-sm">Made with ❤️ for language enthusiasts.</p>
        </div>
    </footer>
</body>
</html>