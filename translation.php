<?php
session_start();
require 'config.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin username
$adminUsername = $_SESSION['admin_username'];

// Process logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Success and error message handling
$successMessage = '';
$errorMessage = '';

// Handle CSV import
if (isset($_POST['import_csv'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $fileName = $_FILES['csv_file']['name'];
        $fileType = $_FILES['csv_file']['type'];
        $fileSize = $_FILES['csv_file']['size'];
        $fileTmpName = $_FILES['csv_file']['tmp_name'];
        
        // Validate file type and extension
        $validExtensions = ['text/csv', 'application/vnd.ms-excel'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if ($fileExtension != 'csv' && !in_array($fileType, $validExtensions)) {
            $errorMessage = "Please upload a valid CSV file.";
        } else {
            // Process the CSV file
            $handle = fopen($fileTmpName, "r");
            $importCount = 0;
            $skipCount = 0;
            $errorCount = 0;
            
            // Skip header row if it exists
            $skipHeader = isset($_POST['skip_header']) ? true : false;
            if ($skipHeader) {
                fgetcsv($handle);
            }
            
            // Process rows
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Ensure we have at least 3 columns
                if (count($data) >= 3) {
                    $tagalog = $conn->real_escape_string(trim($data[0]));
                    $mangyan = $conn->real_escape_string(trim($data[1]));
                    $english = $conn->real_escape_string(trim($data[2]));
                    
                    // Skip empty rows
                    if (empty($tagalog) && empty($mangyan) && empty($english)) {
                        continue;
                    }
                    
                    // Check if translation already exists
                    $checkSql = "SELECT * FROM translation WHERE tagalog = '$tagalog' OR mangyan = '$mangyan' OR english = '$english'";
                    $checkResult = $conn->query($checkSql);
                    
                    if ($checkResult && $checkResult->num_rows > 0) {
                        $skipCount++;
                    } else {
                        $insertSql = "INSERT INTO translation (tagalog, mangyan, english) VALUES ('$tagalog', '$mangyan', '$english')";
                        
                        if ($conn->query($insertSql)) {
                            $importCount++;
                        } else {
                            $errorCount++;
                        }
                    }
                }
            }
            
            fclose($handle);
            
            $successMessage = "Import completed! $importCount translations imported successfully. $skipCount duplicates skipped. $errorCount errors.";
        }
    } else {
        $errorMessage = "Please select a CSV file to upload.";
    }
}

// Handle translation addition
if (isset($_POST['add_translation'])) {
    $tagalog = $conn->real_escape_string($_POST['tagalog']);
    $mangyan = $conn->real_escape_string($_POST['mangyan']);
    $english = $conn->real_escape_string($_POST['english']);
    
    // Check if translation already exists
    $checkSql = "SELECT * FROM translation WHERE tagalog = '$tagalog' OR mangyan = '$mangyan' OR english = '$english'";
    $checkResult = $conn->query($checkSql);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Translation already exists for one or more of these terms.";
    } else {
        $insertSql = "INSERT INTO translation (tagalog, mangyan, english) VALUES ('$tagalog', '$mangyan', '$english')";
        
        if ($conn->query($insertSql)) {
            $successMessage = "Translation added successfully.";
        } else {
            $errorMessage = "Error adding translation: " . $conn->error;
        }
    }
}

// Handle translation update
if (isset($_POST['update_translation'])) {
    $id = (int)$_POST['id'];
    $tagalog = $conn->real_escape_string($_POST['tagalog']);
    $mangyan = $conn->real_escape_string($_POST['mangyan']);
    $english = $conn->real_escape_string($_POST['english']);
    
    $updateSql = "UPDATE translation SET tagalog = '$tagalog', mangyan = '$mangyan', english = '$english' WHERE id = $id";
    
    if ($conn->query($updateSql)) {
        $successMessage = "Translation updated successfully.";
    } else {
        $errorMessage = "Error updating translation: " . $conn->error;
    }
}

// Handle translation deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $deleteSql = "DELETE FROM translation WHERE id = $id";
    
    if ($conn->query($deleteSql)) {
        $successMessage = "Translation deleted successfully.";
    } else {
        $errorMessage = "Error deleting translation: " . $conn->error;
    }
}

// Define variables for pagination and filtering
$recordsPerPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filterSql = '';
if (!empty($search)) {
    $filterSql = " WHERE tagalog LIKE '%$search%' OR mangyan LIKE '%$search%' OR english LIKE '%$search%'";
}

// Get total number of translation records
$totalRecordsQuery = "SELECT COUNT(*) as total FROM translation" . $filterSql;
$totalResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get translations with pagination and filtering
$sql = "SELECT * FROM translation" . $filterSql . " ORDER BY id DESC LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
$translations = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $translations[] = $row;
    }
}

// Get translation for editing
$editTranslation = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editSql = "SELECT * FROM translation WHERE id = $id";
    $editResult = $conn->query($editSql);
    
    if ($editResult && $editResult->num_rows > 0) {
        $editTranslation = $editResult->fetch_assoc();
    }
}

// Determine current action
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_GET['edit']) ? 'edit' : 'view');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation Management | MultiLingual Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            background: linear-gradient(to bottom, #4f46e5, #3b82f6);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid white;
        }

        .tab {
            @apply px-4 py-2 font-medium text-sm rounded-md transition-all duration-200;
        }
        
        .tab.active {
            @apply bg-indigo-600 text-white;
        }
        
        .tab:not(.active) {
            @apply text-gray-700 hover:bg-gray-100;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 fixed h-full text-white">
            <div class="p-5 border-b border-blue-700">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-language text-2xl"></i>
                    <div>
                        <h1 class="text-lg font-bold">MultiLingual</h1>
                        <p class="text-xs opacity-75">Admin Dashboard</p>
                    </div>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="bg-white rounded-full h-10 w-10 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($adminUsername); ?></p>
                        <p class="text-xs opacity-75">Administrator</p>
                    </div>
                </div>
                
                <nav class="space-y-1">
                    <a href="home.php" class="sidebar-link px-4 py-3 rounded-lg flex items-center space-x-3">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="translation.php" class="sidebar-link active px-4 py-3 rounded-lg flex items-center space-x-3">
                        <i class="fas fa-language w-5"></i>
                        <span>Translations</span>
                    </a>
                    
                    <a href="feedback.php" class="sidebar-link px-4 py-3 rounded-lg flex items-center space-x-3">
                        <i class="fas fa-comment w-5"></i>
                        <span>Feedback</span>
                    </a>
                    
                    <div class="pt-4 mt-4 border-t border-blue-700">
                        <a href="?logout=true" class="sidebar-link px-4 py-3 rounded-lg flex items-center space-x-3 text-red-200 hover:text-white">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="ml-64 w-full">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-3">
                    <h2 class="text-xl font-bold text-gray-800">Translation Management</h2>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" target="_blank" class="text-gray-600 hover:text-indigo-600 flex items-center">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            View Site
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Action Tabs -->
                <div class="flex space-x-2 mb-6">
                    <a href="translation.php" class="tab <?php echo $action === 'view' ? 'active' : ''; ?>">
                        <i class="fas fa-list mr-1"></i> View All
                    </a>
                    <a href="?action=add" class="tab <?php echo $action === 'add' ? 'active' : ''; ?>">
                        <i class="fas fa-plus mr-1"></i> Add New
                    </a>
                    <a href="?action=import" class="tab <?php echo $action === 'import' ? 'active' : ''; ?>">
                        <i class="fas fa-file-import mr-1"></i> Bulk Import
                    </a>
                </div>
                
                <?php if (!empty($successMessage)): ?>
                <div id="successAlert" class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 flex justify-between items-center">
                    <div><?php echo $successMessage; ?></div>
                    <button onclick="document.getElementById('successAlert').style.display = 'none';" class="text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                <div id="errorAlert" class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 flex justify-between items-center">
                    <div><?php echo $errorMessage; ?></div>
                    <button onclick="document.getElementById('errorAlert').style.display = 'none';" class="text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <?php if ($action === 'import'): ?>
                <!-- Bulk Import Form -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Bulk Import Translations</h3>
                        <p class="text-gray-600 text-sm mt-1">Upload a CSV file with translations in the format: Tagalog, Mangyan, English</p>
                    </div>
                    <form action="translation.php?action=import" method="POST" enctype="multipart/form-data" class="p-6">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload CSV File</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <div class="flex flex-col items-center justify-center pt-7">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium text-indigo-600">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">CSV file only</p>
                                    </div>
                                    <input type="file" name="csv_file" class="opacity-0" accept=".csv" required />
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="skip_header" name="skip_header" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="skip_header" class="ml-2 block text-sm text-gray-700">
                                    Skip header row (first row contains column names)
                                </label>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">CSV Format Instructions</h4>
                            <p class="text-sm text-gray-600 mb-2">Your CSV file should have the following format:</p>
                            <div class="bg-gray-100 p-2 rounded text-sm font-mono">
                                tagalog_word,mangyan_word,english_word<br>
                                kumusta,kamusta,hello<br>
                                salamat,salamat,thank you<br>
                                ...
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle"></i>
                                Duplicate entries will be skipped automatically.
                            </p>
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="#" onclick="downloadSampleCsv()" class="text-indigo-600 hover:text-indigo-800 flex items-center text-sm">
                                <i class="fas fa-download mr-1"></i> Download Sample CSV
                            </a>
                            <div>
                                <a href="translation.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 mr-2 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" name="import_csv" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-file-import mr-1"></i> Import Translations
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <?php elseif ($action === 'add'): ?>
                <!-- Add Translation Form -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Add New Translation</h3>
                    </div>
                    <form action="translation.php" method="POST" class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="tagalog" class="block text-sm font-medium text-gray-700 mb-1">Tagalog</label>
                                <input type="text" name="tagalog" id="tagalog" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="mangyan" class="block text-sm font-medium text-gray-700 mb-1">Mangyan</label>
                                <input type="text" name="mangyan" id="mangyan" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="english" class="block text-sm font-medium text-gray-700 mb-1">English</label>
                                <input type="text" name="english" id="english" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="translation.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 mr-2 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" name="add_translation" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1"></i> Add Translation
                            </button>
                        </div>
                    </form>
                </div>
                
                <?php elseif ($action === 'edit' && $editTranslation): ?>
                <!-- Edit Translation Form -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Edit Translation</h3>
                    </div>
                    <form action="translation.php" method="POST" class="p-6">
                        <input type="hidden" name="id" value="<?php echo $editTranslation['id']; ?>">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="tagalog" class="block text-sm font-medium text-gray-700 mb-1">Tagalog</label>
                                <input type="text" name="tagalog" id="tagalog" required 
                                    value="<?php echo htmlspecialchars($editTranslation['tagalog']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="mangyan" class="block text-sm font-medium text-gray-700 mb-1">Mangyan</label>
                                <input type="text" name="mangyan" id="mangyan" required 
                                    value="<?php echo htmlspecialchars($editTranslation['mangyan']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="english" class="block text-sm font-medium text-gray-700 mb-1">English</label>
                                <input type="text" name="english" id="english" required 
                                    value="<?php echo htmlspecialchars($editTranslation['english']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="translation.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 mr-2 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" name="update_translation" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-save mr-1"></i> Update Translation
                            </button>
                        </div>
                    </form>
                </div>
                
                <?php else: ?>
                <!-- Translation List -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">All Translations</h3>
                        <div class="flex space-x-2">
                            <form action="" method="GET" class="flex">
                                <input type="text" name="search" placeholder="Search translations..." 
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                            
                            <div class="flex space-x-2">
                                <a href="?action=add" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                                    <i class="fas fa-plus mr-1"></i> New
                                </a>
                                <a href="?action=import" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                                    <i class="fas fa-file-import mr-1"></i> Import
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (count($translations) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tagalog</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mangyan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">English</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($translations as $translation): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $translation['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo htmlspecialchars($translation['tagalog']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($translation['mangyan']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?php echo htmlspecialchars($translation['english']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="?edit=<?php echo $translation['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $translation['id']; ?>)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                                <span class="font-medium"><?php echo min($offset + $recordsPerPage, $totalRecords); ?></span> of 
                                <span class="font-medium"><?php echo $totalRecords; ?></span> results
                            </div>
                            <div class="flex space-x-1">
                                <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                for($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 rounded-md <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?php echo $totalPages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-12 text-gray-500">
                        <?php if (!empty($search)): ?>
                            <i class="fas fa-search text-4xl mb-4 opacity-50"></i>
                            <p>No translations found matching your search</p>
                            <a href="translation.php" class="text-indigo-600 hover:text-indigo-800 mt-4 inline-block">Clear Search</a>
                        <?php else: ?>
                            <i class="fas fa-language text-4xl mb-4 opacity-50"></i>
                            <p>No translations found in the database</p>
                            <a href="?action=add" class="text-indigo-600 hover:text-indigo-800 mt-4 inline-block">Add your first translation</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Confirm delete modal
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Translation',
                text: 'Are you sure you want to delete this translation? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'translation.php?delete=' + id;
                }
            });
        }

        // Handle file input display
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    if (fileName) {
                        const parent = this.closest('label');
                        const uploadText = parent.querySelector('div');
                        uploadText.innerHTML = `
                            <i class="fas fa-file-csv text-3xl text-indigo-500 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">${fileName}</p>
                            <p class="text-xs text-gray-500 mt-1">Click to change file</p>
                        `;
                    }
                });
            }
        });
        
        // Function to download sample CSV
        function downloadSampleCsv() {
            const csvContent = "tagalog,mangyan,english\nkumusta,kamusta,hello\nsalamat,salamat,thank you\nmagandang araw,mayad nga adlaw,good day";
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'sample_translations.csv');
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>