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

// Define variables for pagination
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Get total number of feedback records
$totalRecordsQuery = "SELECT COUNT(*) as total FROM feedback";
$totalResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Handle feedback deletion
$deleteMessage = '';
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $deleteSql = "DELETE FROM feedback WHERE id = $id";
    
    if ($conn->query($deleteSql)) {
        $deleteMessage = "Feedback deleted successfully.";
    } else {
        $deleteMessage = "Error deleting feedback: " . $conn->error;
    }
}

// Get feedback with pagination
$sql = "SELECT * FROM feedback ORDER BY date_submitted DESC LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
$feedbacks = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management | MultiLingual Admin</title>
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
                    
                    <a href="translation.php" class="sidebar-link px-4 py-3 rounded-lg flex items-center space-x-3">
                        <i class="fas fa-language w-5"></i>
                        <span>Translations</span>
                    </a>
                    
                    <a href="feedback.php" class="sidebar-link active px-4 py-3 rounded-lg flex items-center space-x-3">
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
                    <h2 class="text-xl font-bold text-gray-800">Feedback Management</h2>
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
                <?php if (!empty($deleteMessage)): ?>
                <div id="alertMessage" class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 flex justify-between items-center">
                    <div><?php echo $deleteMessage; ?></div>
                    <button onclick="document.getElementById('alertMessage').style.display = 'none';" class="text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Feedback Messages</h3>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                            Total: <?php echo $totalRecords; ?>
                        </span>
                    </div>
                    
                    <?php if (count($feedbacks) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($feedbacks as $feedback): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $feedback['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo htmlspecialchars($feedback['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($feedback['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 rounded-full text-xs <?php 
                                            switch($feedback['feedback_type']) {
                                                case 'general':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'suggestion':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'translation':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'bug':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                        ?>">
                                            <?php echo htmlspecialchars($feedback['feedback_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($feedback['message']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y g:i A', strtotime($feedback['date_submitted'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button onclick="viewFeedback(<?php echo $feedback['id']; ?>, '<?php echo addslashes(htmlspecialchars($feedback['name'])); ?>', '<?php echo addslashes(htmlspecialchars($feedback['email'])); ?>', '<?php echo addslashes(htmlspecialchars($feedback['feedback_type'])); ?>', '<?php echo addslashes(htmlspecialchars($feedback['message'])); ?>', '<?php echo date('M j, Y g:i A', strtotime($feedback['date_submitted'])); ?>')" class="text-blue-600 hover:text-blue-900 mr-2">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $feedback['id']; ?>)" class="text-red-600 hover:text-red-900">
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
                                <a href="?page=1" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                for($i = $startPage; $i <= $endPage; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" class="px-3 py-1 rounded-md <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?php echo $totalPages; ?>" class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-comment-slash text-4xl mb-4 opacity-50"></i>
                        <p>No feedback submissions found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View feedback details in modal
        function viewFeedback(id, name, email, type, message, date) {
            Swal.fire({
                title: 'Feedback from ' + name,
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Email:</strong> ${email}</p>
                        <p class="mb-2"><strong>Type:</strong> ${type}</p>
                        <p class="mb-2"><strong>Date:</strong> ${date}</p>
                        <div class="mt-4">
                            <strong>Message:</strong>
                            <p class="mt-2 p-3 bg-gray-50 rounded text-gray-800">${message}</p>
                        </div>
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'Close',
                confirmButtonColor: '#4f46e5'
            });
        }
        
        // Confirm delete modal
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Feedback',
                text: 'Are you sure you want to delete this feedback? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'feedback.php?delete=' + id;
                }
            });
        }
    </script>
</body>
</html>