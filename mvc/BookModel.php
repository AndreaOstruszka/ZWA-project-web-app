<?php
class BookModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;          // Database connection
    }

    public function getBooks($limit, $offset) {
        $sql = "SELECT name, isbn, literary_genre, fiction_genre FROM books LIMIT ? OFFSET ?";      // Limit for number of returned rows, offset sets beginning of returned rows
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = [
                'name' => htmlspecialchars($row['name']),
                'isbn' => htmlspecialchars($row['isbn']),
                'literary_genre' => htmlspecialchars($row['literary_genre']),
                'fiction_genre' => htmlspecialchars($row['fiction_genre'])
            ];
        }
        return $books;
    }

    public function getTotalBooks() {                   // Total number of books in database
        $sql = "SELECT COUNT(*) as total FROM books";
        $result = $this->conn->query($sql);
        $total = $result->fetch_assoc();
        return $total['total'];
    }
}
?>