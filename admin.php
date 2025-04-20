<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "mangyan");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search variable
$search = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search_term']);
}

// Pagination variables
$limit = 10; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Fetch translations based on search term
$query = "SELECT * FROM translations";
if ($search) {
    $query .= " WHERE tagalog_word LIKE '%$search%' OR mangyan_word LIKE '%$search%'";
}
$query .= " LIMIT $limit OFFSET $offset"; // Add limit and offset
$result = $conn->query($query);

// Get total number of records for pagination
$totalQuery = "SELECT COUNT(*) as total FROM translations";
if ($search) {
    $totalQuery .= " WHERE tagalog_word LIKE '%$search%' OR mangyan_word LIKE '%$search%'";
}
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit); // Total pages

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $tagalog_word = $_POST['tagalog_word'];
    $mangyan_word = $_POST['mangyan_word'];
    $dialect = $_POST['dialect'];

    $stmt = $conn->prepare("INSERT INTO translations (tagalog_word, mangyan_word, dialect) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tagalog_word, $mangyan_word, $dialect);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $tagalog_word = $_POST['tagalog_word'];
    $mangyan_word = $_POST['mangyan_word'];

    $stmt = $conn->prepare("UPDATE translations SET tagalog_word=?, mangyan_word=? WHERE id=?");
    $stmt->bind_param("ssi", $tagalog_word, $mangyan_word, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM translations WHERE id=$id");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: index.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 80%; /* Increased width for the container */
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            display: flex; /* Use flexbox for layout */
            flex-direction: column; /* Stack items vertically */
        }
        .top-section {
            display: flex; /* Use flexbox for top section */
            justify-content: space-between; /* Space between left and right */
            margin-bottom: 20px; /* Space below the top section */
        }
        .form-section, .search-logout-section {
            width: 48%; /* Set width for both sections */
        }
        input, select, button {
            width: 100%; /* Full width for inputs and buttons */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #006600; /* Green border */
            border-radius: 5px;
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }
        input:focus, select:focus {
            border-color: #004d00; /* Darker green on focus */
            outline: none;
        }
        button {
            background: #006600; /* Green background */
            color: white;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        button:hover {
            background: #009900; /* Lighter green on hover */
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #006600; /* Green border */
        }
        th, td {
            padding: 5px; /* Reduced padding for closer columns */
            text-align: center;
        }
        th {
            background-color: #006600; /* Green header */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #e0ffe0; /* Light green for even rows */
        }
        tr:nth-child(odd) {
            background-color: #ffffff; /* White for odd rows */
        }
        .action-buttons {
            display: flex; /* Use flexbox for horizontal alignment */
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Space between buttons */
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #006600;
            padding: 5px 10px;
            border: 1px solid #006600;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #006600;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            animation: slideIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <div class="top-section">
            <div class="search-logout-section">
                <form method="POST" style="margin-bottom: 20px;">
                    <input type="text" name="search_term" placeholder="Search for a word..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" name="search"><i class="fas fa-search"></i> Search</button>
                </form>
                <form method="POST">
                    <button type="submit" name="logout" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
            <div class="form-section">
                <form method="POST">
                    <input type="text" name="tagalog_word" placeholder="Tagalog Word" required>
                    <input type="text" name="mangyan_word" placeholder="Mangyan Word" required>
                    <select name="dialect" required>
                        <option value="Hanunuo">Hanunuo</option>
                        <option value="Buhid">Buhid</option>
                        <option value="Tawbuid">Tawbuid</option>
                        <option value="Iraya">Iraya</option>
                    </select>
                    <button type="submit" name="add"><i class="fas fa-plus-circle"></i> Add Translation</button>
                </form>
            </div>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Tagalog Word</th>
                <th>Mangyan Word</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['tagalog_word']; ?></td>
                    <td><?php echo $row['mangyan_word']; ?></td>
                    <td>
                        <div class="action-buttons">
                            <button onclick="openModal('editModal', '<?php echo $row['id']; ?>', '<?php echo $row['tagalog_word']; ?>', '<?php echo $row['mangyan_word']; ?>')"><i class="fas fa-edit"></i> Edit</button>
                            <button onclick="openModal('deleteModal', '<?php echo $row['id']; ?>')"><i class="fas fa-trash-alt"></i> Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
 <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($i === $page) echo 'style="font-weight:bold;"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Translation</h3>
            <form method="POST">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="tagalog_word" id="editTagalog" required>
                <input type="text" name="mangyan_word" id="editMangyan" required>
                <button type="submit" name="edit">Save Changes</button>
                <button type="button" onclick="closeModal('editModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this translation?</h3>
            <form method="POST">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" name="delete">Delete</button>
                <button type="button" onclick="closeModal('deleteModal')">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId, id, tagalog = '', mangyan = '') {
            document.getElementById(modalId).style.display = 'flex';
            if (modalId === 'editModal') {
                document.getElementById('editId').value = id;
                document.getElementById('editTagalog').value = tagalog;
                document.getElementById('editMangyan').value = mangyan;
            } else if (modalId === 'deleteModal') {
                document.getElementById('deleteId').value = id;
            }
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>