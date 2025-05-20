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

// Get statistics for dashboard
$translationCount = 0;
$feedbackCount = 0;

// Count translations
$sqlTranslations = "SELECT COUNT(*) as count FROM translation";
$resultTranslations = $conn->query($sqlTranslations);
if ($resultTranslations && $resultTranslations->num_rows > 0) {
    $translationCount = $resultTranslations->fetch_assoc()['count'];
}

// Count feedback messages
$sqlFeedback = "SELECT COUNT(*) as count FROM feedback";
$resultFeedback = $conn->query($sqlFeedback);
if ($resultFeedback && $resultFeedback->num_rows > 0) {
    $feedbackCount = $resultFeedback->fetch_assoc()['count'];
}

// Get recent feedback
$recentFeedback = [];
$sqlRecentFeedback = "SELECT * FROM feedback ORDER BY date_submitted DESC LIMIT 5";
$resultRecentFeedback = $conn->query($sqlRecentFeedback);
if ($resultRecentFeedback && $resultRecentFeedback->num_rows > 0) {
    while ($row = $resultRecentFeedback->fetch_assoc()) {
        $recentFeedback[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | MultiLingual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .dashboard-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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
                    <a href="home.php" class="sidebar-link active px-4 py-3 rounded-lg flex items-center space-x-3">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="translation.php" class="sidebar-link px-4 py-3 rounded-lg flex items-center space-x-3">
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
                    <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" target="_blank" class="text-gray-600 hover:text-indigo-600 flex items-center">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            View Site
                        </a>
                        <div class="relative">
                            <button class="text-gray-600 hover:text-indigo-600">
                                <i class="fas fa-bell"></i>
                            </button>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Translation Stats Card -->
                    <div class="dashboard-card bg-white rounded-xl p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Translations</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $translationCount; ?></h3>
                                <p class="text-sm text-green-500 mt-2">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>Available for translation</span>
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-language text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Feedback Stats Card -->
                    <div class="dashboard-card bg-white rounded-xl p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Feedback Messages</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $feedbackCount; ?></h3>
                                <p class="text-sm text-indigo-500 mt-2">
                                    <i class="fas fa-comments mr-1"></i>
                                    <span>From website visitors</span>
                                </p>
                            </div>
                            <div class="bg-indigo-100 p-3 rounded-lg text-indigo-600">
                                <i class="fas fa-comment-alt text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Card -->
                    <div class="bg-white rounded-xl p-5 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Translation Distribution</h4>
                        <div class="h-64">
                            <canvas id="translationChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Recent Feedback Card -->
                    <div class="bg-white rounded-xl p-5 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-700">Recent Feedback</h4>
                            <a href="feedback.php" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                        </div>
                        
                        <div class="space-y-4">
                            <?php if(count($recentFeedback) > 0): ?>
                                <?php foreach($recentFeedback as $feedback): ?>
                                    <div class="border-b border-gray-200 pb-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-medium text-gray-800"><?php echo htmlspecialchars($feedback['name']); ?></h5>
                                                <p class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($feedback['feedback_type']); ?> • 
                                                    <?php echo date('M j, Y', strtotime($feedback['date_submitted'])); ?>
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">New</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-2">
                                            <?php echo htmlspecialchars(substr($feedback['message'], 0, 100)) . (strlen($feedback['message']) > 100 ? '...' : ''); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4 text-gray-500">No feedback messages yet</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl p-5 shadow-sm mb-8">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="translation.php?action=add" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mb-2">
                                <i class="fas fa-plus"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Add Translation</p>
                        </a>
                        
                        <a href="feedback.php" class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-2">
                                <i class="fas fa-comment"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700">View Feedback</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Chart for translation distribution
        const ctx = document.getElementById('translationChart').getContext('2d');
        const translationChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Tagalog', 'Mangyan', 'English'],
                datasets: [{
                    data: [35, 25, 40],
                    backgroundColor: [
                        '#4f46e5',
                        '#06b6d4',
                        '#10b981'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>