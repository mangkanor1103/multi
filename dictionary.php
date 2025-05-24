<?php
require 'config.php'; // Database connection

// Define search variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$language = isset($_GET['language']) ? $_GET['language'] : 'tagalog';
$recordsPerPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Get all available languages
$languages = ['tagalog', 'mangyan', 'english'];

// Build search query
$searchSql = '';
$params = [];
$types = '';

if (!empty($search)) {
    if ($language == 'all') {
        $searchSql = " WHERE tagalog LIKE ? OR mangyan LIKE ? OR english LIKE ?";
        $searchParam = "%{$search}%";
        $params = [$searchParam, $searchParam, $searchParam];
        $types = "sss";
    } else {
        $searchSql = " WHERE $language LIKE ?";
        $searchParam = "%{$search}%";
        $params = [$searchParam];
        $types = "s";
    }
}

// Get total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM translation" . $searchSql;
$stmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$stmt->close();

// Get dictionary entries
$entriesSql = "SELECT * FROM translation" . $searchSql . " ORDER BY $language ASC LIMIT ?, ?";
$stmt = $conn->prepare($entriesSql);

if (!empty($params)) {
    $types .= "ii";
    $params[] = $offset;
    $params[] = $recordsPerPage;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $offset, $recordsPerPage);
}

$stmt->execute();
$result = $stmt->get_result();
$entries = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}
$stmt->close();

// Get letter index for alphabetical filter
$lettersSql = "SELECT DISTINCT LEFT($language, 1) as letter FROM translation ORDER BY letter ASC";
$lettersResult = $conn->query($lettersSql);
$letters = [];

if ($lettersResult->num_rows > 0) {
    while ($row = $lettersResult->fetch_assoc()) {
        $letters[] = $row['letter'];
    }
}

// Filter by letter if specified
$activeLetter = isset($_GET['letter']) ? $_GET['letter'] : null;
if ($activeLetter && empty($search)) {
    $letterSql = "SELECT * FROM translation WHERE $language LIKE ? ORDER BY $language ASC LIMIT ?, ?";
    $stmt = $conn->prepare($letterSql);
    $letterParam = $activeLetter . "%";
    $stmt->bind_param("sii", $letterParam, $offset, $recordsPerPage);
    $stmt->execute();
    $result = $stmt->get_result();
    $entries = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }
    $stmt->close();
    
    // Update total count for pagination
    $countLetterSql = "SELECT COUNT(*) as total FROM translation WHERE $language LIKE ?";
    $stmt = $conn->prepare($countLetterSql);
    $stmt->bind_param("s", $letterParam);
    $stmt->execute();
    $totalResult = $stmt->get_result();
    $totalRecords = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary | MultiLingual</title>
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
        
        .hero-mini {
            background: linear-gradient(to right, #4f46e5, #3b82f6);
        }
        
        .compact-hero {
            height: auto;
            padding: 1.5rem 0;
        }
        
        .language-option {
            transition: all 0.2s ease;
        }
        
        .language-option:hover {
            transform: translateY(-2px);
        }
        
        .letter-nav {
            user-select: none;
        }
        
        .letter-nav span {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .letter-nav span:hover {
            background-color: #e0e7ff;
        }
        
        .letter-nav span.active {
            background-color: #4f46e5;
            color: white;
        }
        
        .dictionary-entry {
            transition: all 0.2s ease;
        }
        
        .dictionary-entry:hover {
            transform: translateX(3px);
            border-left-color: #4f46e5;
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
        <a href="about.php" class="block py-2 hover:bg-blue-700 px-3 rounded">About</a>
    </div>

    <!-- Mini Hero Section -->
    <div class="hero-mini compact-hero text-white px-6">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="py-2">
                <h1 class="text-2xl font-bold">Multilingual Dictionary</h1>
                <p class="text-sm md:text-base opacity-90">Browse translations in Tagalog, Mangyan, and English</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-book text-4xl text-blue-100"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow px-4 md:px-6 pt-4 pb-12">
        <div class="max-w-5xl mx-auto">
            <!-- Dictionary Card -->
            <div class="glass-card rounded-2xl overflow-hidden mb-8">
                <!-- Tabs for Translation Type -->
                <div class="flex border-b border-gray-200">
                    <a href="index.php" class="flex-1 py-3 font-medium text-gray-500 hover:text-gray-800 custom-transition text-center">
                        <i class="fas fa-exchange-alt mr-2"></i> Text Translation
                    </a>
                    <a href="dictionary.php" class="flex-1 py-3 font-medium text-blue-600 border-b-2 border-blue-600 text-center">
                        <i class="fas fa-book mr-2"></i> Dictionary
                    </a>
                </div>
                
                <!-- Search Form -->
                <form action="" method="GET" class="p-4 md:p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Word</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Type a word..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full md:w-48">
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                            <select id="language" name="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" <?php echo $language == 'all' ? 'selected' : ''; ?>>All Languages</option>
                                <option value="tagalog" <?php echo $language == 'tagalog' ? 'selected' : ''; ?>>Tagalog</option>
                                <option value="mangyan" <?php echo $language == 'mangyan' ? 'selected' : ''; ?>>Mangyan</option>
                                <option value="english" <?php echo $language == 'english' ? 'selected' : ''; ?>>English</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Alphabetical Filter -->
                <?php if ($language != 'all' && count($letters) > 0 && empty($search)): ?>
                <div class="letter-nav bg-gray-50 p-4 overflow-x-auto whitespace-nowrap">
                    <span class="text-sm text-gray-600 mr-2">Browse by letter:</span>
                    <a href="?language=<?php echo $language; ?>" class="inline-block px-2 py-1 text-sm <?php echo !$activeLetter ? 'bg-blue-600 text-white rounded' : 'text-blue-600'; ?>">All</a>
                    <?php foreach($letters as $letter): ?>
                    <a href="?language=<?php echo $language; ?>&letter=<?php echo urlencode($letter); ?>" class="inline-block px-2 py-1 text-sm <?php echo $activeLetter == $letter ? 'bg-blue-600 text-white rounded' : 'text-blue-600'; ?>">
                        <?php echo strtoupper($letter); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Dictionary Results -->
                <div class="p-4 md:p-6">
                    <?php if (!empty($search) || $activeLetter): ?>
                        <p class="text-sm text-gray-600 mb-4">
                            <?php if (!empty($search)): ?>
                                Showing results for: <span class="font-medium"><?php echo htmlspecialchars($search); ?></span>
                            <?php elseif ($activeLetter): ?>
                                Showing words starting with: <span class="font-medium"><?php echo strtoupper($activeLetter); ?></span>
                            <?php endif; ?>
                            (<?php echo $totalRecords; ?> entries found)
                        </p>
                    <?php endif; ?>
                    
                    <?php if (count($entries) > 0): ?>
                        <div class="divide-y divide-gray-100">
                            <?php foreach($entries as $entry): ?>
                            <div class="dictionary-entry py-3 pl-3 border-l-4 border-transparent">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div>
                                        <span class="text-xs text-gray-500 block">Tagalog</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($entry['tagalog']); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Mangyan</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($entry['mangyan']); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">English</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($entry['english']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="mt-6 flex justify-center">
                            <div class="flex space-x-1">
                                <?php
                                // Calculate pagination links
                                $queryParams = http_build_query(array_filter([
                                    'search' => $search,
                                    'language' => $language,
                                    'letter' => $activeLetter
                                ]));
                                $queryString = !empty($queryParams) ? "&$queryParams" : "";
                                ?>
                                
                                <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo $queryString; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                for($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="px-3 py-1 rounded-md <?php echo $i == $page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?php echo $totalPages; ?><?php echo $queryString; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-search text-4xl mb-4 opacity-50"></i>
                            <p class="mb-3">No dictionary entries found</p>
                            <?php if (!empty($search) || $activeLetter): ?>
                            <a href="dictionary.php" class="text-blue-600 hover:text-blue-800">Show all entries</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Dictionary Info -->
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-3 text-gray-800">About the Dictionary</h2>
                <p class="text-gray-700 mb-3 text-sm">
                    This multilingual dictionary provides translations between Tagalog, Mangyan, and English. 
                    Use the search function to find specific words or browse alphabetically.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Can't find a word? Try the <a href="index.php" class="underline">translator</a> for 
                                full sentences or phrases.
                            </p>
                        </div>
                    </div>
                </div>
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
                            <li><a href="dictionary.php" class="hover:text-white transition">Dictionary</a></li>
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