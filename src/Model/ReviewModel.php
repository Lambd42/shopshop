<?php


    namespace MyApp\Model;

    use MyApp\Entity\Review;
    use MyApp\Entity\Product;
    use MyApp\Entity\Type;
    use MyApp\Entity\User;
    use PDO;

    class ReviewModel {

        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllReviews(): array {
            $sql = "SELECT * FROM Review INNER JOIN Product ON Review.productID = Product.productID INNER JOIN Type ON Product.type = Type.typeID INNER JOIN User on Review.userID = User.userID;";
            $stmt = $this->db->query($sql);
            $reviews = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $type = new Type($row['Type.typeId'], $row['label']);
                $product = new Product($row['Product.productId'], $row['name'], $row['description'], floatVal($row['price']), $type, $row['image']); 
                $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
                $reviews[] = new Review($product, $user, $row['note'], $row['text']);

            }

            return $reviews;
        }

        public function getReviewById(int $reviewID): ?Review {
            $sql = "SELECT * FROM Review INNER JOIN Product ON Review.productID = Product.productID INNER JOIN Type ON Product.type = Type.typeID INNER JOIN User on Review.userID = User.userID WHERE reviewID = :reviewID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":reviewID", $reviewID);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row){
                return null;
            }
            $type = new Type($row['Type.typeId'], $row['label']);
            $product = new Product($row['Product.productId'], $row['name'], $row['description'], floatVal($row['price']), $type, $row['image']); 
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $review = new Review($product, $user, $row['note'], $row['text']);
            return $review;
        }

        public function getAllReviewsByProduct(Product $product): array {
            $sql = "SELECT * FROM Review INNER JOIN Product ON Review.productID = Product.productID INNER JOIN Type ON Product.type = Type.typeID INNER JOIN User on Review.userID = User.userID WHERE Review.productID = :productID;";
            $stmt = $this->db->prepare($sql);
            $productID = $product->getId();
            $stmt->bindValue(":productID", $productID);
            $stmt->execute();

            $reviews = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $type = new Type($row['typeID'], $row['label']);
                $product = new Product($row['productID'], $row['name'], $row['description'], floatVal($row['price']), intval($row['stock']), $type, $row['image'], boolval($row['homepage'])); 
                $user = new User($row['userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
                $reviews[] = new Review(null, $product, $user, $row['note'], $row['text']);
            }

            return $reviews;
        }

        public function createReview(Review $review): ?bool {
            $sql = "INSERT INTO Review (productID, userID, note, text) VALUES (:productID, :userID, :note, :text);";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":productID", $review->getProduct()->getId());
            $stmt->bindValue(":userID", $review->getUser()->getUserId());
            $stmt->bindValue(":note", $review->getNote());
            $stmt->bindValue(":text", $review->getText());
            return $stmt->execute();
        }

    }

?>