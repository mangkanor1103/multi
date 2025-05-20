<?php
require 'config.php'; // Include database connection

$feedbackSubmitted = false;
$feedbackError = false;

// Process feedback form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form processing with database storage
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $feedback_type = $conn->real_escape_string($_POST['feedback_type']);
        $message = $conn->real_escape_string($_POST['message']);
        $date_submitted = date('Y-m-d H:i:s');
        
        // Insert into feedback table
        $sql = "INSERT INTO feedback (name, email, feedback_type, message, date_submitted) 
                VALUES ('$name', '$email', '$feedback_type', '$message', '$date_submitted')";
        
        if ($conn->query($sql) === TRUE) {
            $feedbackSubmitted = true;
        } else {
            $feedbackError = true;
        }
    } else {
        $feedbackError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About MultiLingual | The Translator System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <a href="about.php" class="text-white font-medium border-b-2 border-white">About</a>
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
        <a href="about.php" class="block py-2 bg-blue-700 px-3 rounded">About</a>
    </div>

    <!-- Mini Header -->
    <div class="bg-gradient-to-b from-indigo-600 to-blue-500 text-white py-4 px-6">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold">About MultiLingual</h1>
            <p class="opacity-90">Learn about our mission to bridge language barriers</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Main About Content - 2/3 width on desktop -->
                <div class="md:col-span-2">
                    <!-- About Card -->
                    <div class="glass-card rounded-2xl p-6 mb-8">
                        <h2 class="text-2xl font-bold mb-4 text-indigo-700">Our Mission</h2>
                        <p class="text-gray-700 mb-4">
                            MultiLingual is dedicated to preserving indigenous languages and fostering cross-cultural 
                            communication through technology. Our translation platform focuses on bridging the gap 
                            between Tagalog, Mangyan, and English languages.
                        </p>
                        <p class="text-gray-700 mb-4">
                            We believe that language preservation is crucial for maintaining cultural identity and heritage. 
                            By providing accessible translation tools, we aim to support language revitalization efforts 
                            and make indigenous languages more accessible to new generations.
                        </p>
                    </div>

                    <!-- System Information Card -->
                    <div class="glass-card rounded-2xl p-6 mb-8">
                        <h2 class="text-2xl font-bold mb-4 text-indigo-700">About the System</h2>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Translation Technology</h3>
                            <p class="text-gray-700 mb-3">
                                Our translation system employs a comprehensive word-by-word translation approach, 
                                carefully mapping terms between languages to maintain accuracy and cultural context.
                            </p>
                            <p class="text-gray-700">
                                The database contains over 1,000 commonly used words and phrases, with continuous 
                                additions to expand our vocabulary coverage.
                            </p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Key Features</h3>
                            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                                <li>Bidirectional translation between Tagalog, Mangyan, and English</li>
                                <li>Voice input for hands-free translation</li>
                                <li>Text-to-speech pronunciation of translated content</li>
                                <li>Interactive Mangyan alphabet learning tool</li>
                                <li>Mobile-friendly design for on-the-go translation needs</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800">Development & Research</h3>
                            <p class="text-gray-700 mb-3">
                                MultiLingual was developed in collaboration with language experts and members of 
                                the Mangyan community to ensure cultural sensitivity and linguistic accuracy.
                            </p>
                            <p class="text-gray-700">
                                Our research involved extensive documentation of the Mangyan language, including phonology, 
                                vocabulary, and grammatical structures, to create an authentic translation system.
                            </p>
                        </div>
                    </div>

                    <!-- Team Information -->
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-2xl font-bold mb-4 text-indigo-700">Development Team</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 shadow-sm">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-code text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">Technical Development</h3>
                                        <p class="text-sm text-gray-600">BSIT Students</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700">
                                    Our technical team designed and implemented the translation system, 
                                    web interface, and mobile responsiveness features.
                                </p>
                            </div>
                            
                            <div class="bg-white rounded-xl p-4 shadow-sm">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">Language Consultants</h3>
                                        <p class="text-sm text-gray-600">Cultural Experts</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700">
                                    Language experts and native speakers contributed to building our 
                                    translation database and verifying accuracy.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - 1/3 width on desktop -->
                <div>
                    <!-- Feedback Form Card -->
                    <div class="glass-card rounded-2xl p-6 mb-8">
                        <h2 class="text-xl font-bold mb-4 text-indigo-700">
                            <i class="fas fa-comment-alt mr-2"></i>
                            Share Your Feedback
                        </h2>
                        
                        <form action="about.php" method="POST" id="feedbackForm">
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-medium mb-1 text-sm">Your Name</label>
                                <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="block text-gray-700 font-medium mb-1 text-sm">Email Address</label>
                                <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="feedback_type" class="block text-gray-700 font-medium mb-1 text-sm">Feedback Type</label>
                                <select name="feedback_type" id="feedback_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="general">General Feedback</option>
                                    <option value="suggestion">Feature Suggestion</option>
                                    <option value="translation">Translation Correction</option>
                                    <option value="bug">Bug Report</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="block text-gray-700 font-medium mb-1 text-sm">Your Message</label>
                                <textarea name="message" id="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" required></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Submit Feedback
                            </button>
                        </form>
                    </div>
                    
                    <!-- Contact Information Card -->
                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-4 text-indigo-700">
                            <i class="fas fa-phone-alt mr-2"></i>
                            Contact Information
                        </h2>
                        
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <i class="fas fa-envelope text-indigo-500 mt-1 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">Email</p>
                                    <p class="text-gray-600">info@multilingual.edu.ph</p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt text-indigo-500 mt-1 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">Address</p>
                                    <p class="text-gray-600">Oriental Mindoro State University<br>
                                    Calapan City, Oriental Mindoro<br>
                                    Philippines</p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <i class="fab fa-github text-indigo-500 mt-1 mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-800">Project Repository</p>
                                    <a href="#" class="text-blue-600 hover:text-blue-800 transition">github.com/multilingual-project</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="glass-card rounded-2xl p-6 mb-12">
                <h2 class="text-2xl font-bold mb-6 text-indigo-700 text-center">Project Development Timeline</h2>
                
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-0 md:left-1/2 w-0.5 h-full bg-indigo-200 transform -translate-x-1/2"></div>
                    
                    <!-- Timeline Items -->
                    <div class="space-y-8">
                        <!-- Item 1 -->
                        <div class="relative flex flex-col md:flex-row">
                            <!-- Date for mobile (shown above content) -->
                            <div class="md:hidden text-sm font-bold text-indigo-700 mb-2">
                                January 2025
                            </div>
                            
                            <!-- Content positioning -->
                            <div class="ml-6 md:ml-0 md:w-1/2 md:pr-8 md:text-right">
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="font-semibold text-gray-800">Research Phase</h3>
                                    <p class="text-gray-600 text-sm">
                                        Initial research and documentation of the Mangyan language, consulting with 
                                        language experts and community members.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Center dot -->
                            <div class="absolute left-0 md:left-1/2 w-6 h-6 bg-indigo-500 rounded-full border-4 border-white transform -translate-x-1/2"></div>
                            
                            <!-- Date for desktop (right side) -->
                            <div class="hidden md:block md:w-1/2 md:pl-8 self-center">
                                <span class="text-sm font-bold text-indigo-700">January 2025</span>
                            </div>
                        </div>
                        
                        <!-- Item 2 -->
                        <div class="relative flex flex-col md:flex-row">
                            <!-- Date for mobile -->
                            <div class="md:hidden text-sm font-bold text-indigo-700 mb-2">
                                February 2025
                            </div>
                            
                            <!-- For even items, content is on right -->
                            <div class="md:w-1/2"></div>
                            
                            <div class="absolute left-0 md:left-1/2 w-6 h-6 bg-indigo-500 rounded-full border-4 border-white transform -translate-x-1/2"></div>
                            
                            <div class="ml-6 md:ml-0 md:w-1/2 md:pl-8">
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="font-semibold text-gray-800">Database Development</h3>
                                    <p class="text-gray-600 text-sm">
                                        Creation of the translation database with initial word mapping between the three languages.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Item 3 -->
                        <div class="relative flex flex-col md:flex-row">
                            <!-- Date for mobile -->
                            <div class="md:hidden text-sm font-bold text-indigo-700 mb-2">
                                March 2025
                            </div>
                            
                            <div class="ml-6 md:ml-0 md:w-1/2 md:pr-8 md:text-right">
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="font-semibold text-gray-800">Web Interface Design</h3>
                                    <p class="text-gray-600 text-sm">
                                        Development of the user interface with focus on accessibility and intuitive design.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="absolute left-0 md:left-1/2 w-6 h-6 bg-indigo-500 rounded-full border-4 border-white transform -translate-x-1/2"></div>
                            
                            <div class="hidden md:block md:w-1/2 md:pl-8 self-center">
                                <span class="text-sm font-bold text-indigo-700">March 2025</span>
                            </div>
                        </div>
                        
                        <!-- Item 4 -->
                        <div class="relative flex flex-col md:flex-row">
                            <!-- Date for mobile -->
                            <div class="md:hidden text-sm font-bold text-indigo-700 mb-2">
                                April 2025
                            </div>
                            
                            <div class="md:w-1/2"></div>
                            
                            <div class="absolute left-0 md:left-1/2 w-6 h-6 bg-indigo-500 rounded-full border-4 border-white transform -translate-x-1/2"></div>
                            
                            <div class="ml-6 md:ml-0 md:w-1/2 md:pl-8">
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="font-semibold text-gray-800">Launch and Ongoing Development</h3>
                                    <p class="text-gray-600 text-sm">
                                        Initial release of MultiLingual with continuous improvements and vocabulary expansion.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-2xl font-bold mb-6 text-indigo-700">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-question-circle text-indigo-500 mr-2"></i>
                            What languages does MultiLingual support?
                        </h3>
                        <p class="text-gray-700">
                            Currently, MultiLingual supports translation between Tagalog, Mangyan, and English languages.
                            We plan to expand to other indigenous Philippine languages in future updates.
                        </p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-question-circle text-indigo-500 mr-2"></i>
                            How accurate are the translations?
                        </h3>
                        <p class="text-gray-700">
                            Our translations aim for high accuracy, especially for common phrases and vocabulary.
                            However, as with any translation system, nuanced cultural expressions may not always
                            translate perfectly. We continuously improve our database based on feedback.
                        </p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-question-circle text-indigo-500 mr-2"></i>
                            Is MultiLingual available as a mobile app?
                        </h3>
                        <p class="text-gray-700">
                            Currently, MultiLingual is available as a web application optimized for mobile devices.
                            A dedicated mobile app for Android and iOS is in our development roadmap for the near future.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-question-circle text-indigo-500 mr-2"></i>
                            How can I contribute to the MultiLingual project?
                        </h3>
                        <p class="text-gray-700">
                            We welcome contributions! You can help by submitting translation corrections, suggesting
                            new words for our database, or providing feedback through our feedback form. For technical
                            contributions, please visit our GitHub repository.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Section - More Compact -->
    <footer class="bg-gray-800 text-gray-300 py-6 px-4 mt-8">
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
                            <li><a href="about.php" class="hover:text-white transition">About</a></li>
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
        
        // Sweet Alert for form submission status
        <?php if($feedbackSubmitted): ?>
        Swal.fire({
            title: 'Thank You!',
            text: 'Your feedback has been submitted successfully.',
            icon: 'success',
            confirmButtonText: 'Close',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-semibold text-gray-800',
                htmlContainer: 'text-gray-700',
            }
        }).then((result) => {
            // Reset form after successful submission
            document.getElementById('feedbackForm').reset();
        });
        <?php endif; ?>
        
        <?php if($feedbackError): ?>
        Swal.fire({
            title: 'Oops!',
            text: 'There was an error submitting your feedback. Please make sure all required fields are filled out.',
            icon: 'error',
            confirmButtonText: 'Try Again',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-semibold text-gray-800',
                htmlContainer: 'text-gray-700',
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>