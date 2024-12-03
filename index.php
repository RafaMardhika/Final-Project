<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyek-akhir";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$noteAdded = false;  // Variabel untuk memeriksa apakah catatan ditambahkan
$noteDeleted = false;  // Variabel untuk memeriksa apakah catatan dihapus

// Fungsi untuk menambahkan catatan baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_note'])) {
    $title = htmlspecialchars($_POST['title']);
    $note = htmlspecialchars($_POST['note']);
    
    $stmt = $conn->prepare("INSERT INTO note (title, note) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $note);
    $stmt->execute();
    $stmt->close();
    
    $noteAdded = true;  // Set variabel menjadi true saat catatan ditambahkan
}

// Fungsi untuk menghapus catatan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM note WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $noteDeleted = true;  // Set variabel menjadi true saat catatan dihapus
}

// Mengambil semua catatan dari database
$result = $conn->query("SELECT * FROM note ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Management</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h1>My Notes 🗒️</h1>
        <p>Make some note for reminding you !</p>
        <form method="POST" action="">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="note">Note:</label>
            <textarea id="note" name="note" required></textarea>
            <br>
            <input type="submit" name="add_note" value="Add Note">
        </form>

        <h2>Notes</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['note']; ?></td>
                    <td>
                        <a href="javascript:void(0)" onclick="Alertshow(<?php echo $row['id']; ?>)">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script>
        function AlertCreated() {
            Swal.fire({
                icon: 'success',
                title: 'Note added',
                showConfirmButton: false,
                timer: 1500
            });
        }
        
        function AlertDeleteSuccess() {
            Swal.fire({
                icon: 'success',
                title: 'Note deleted',
                showConfirmButton: false,
                timer: 1500
            });
        }

        function Alertshow(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?delete=' + id;
                }
            });
        }

        <?php if ($noteAdded) { ?>
            AlertCreated();
        <?php } ?>

        <?php if ($noteDeleted) { ?>
            AlertDeleteSuccess();
        <?php } ?>
    </script>
</body>
</html>

<?php
$conn->close();
?>
