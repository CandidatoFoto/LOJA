<?php
// products/edit.php

require '../config.php';
require '../db.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$product_id = $_GET['id'];
$product = getProductById($product_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedImage = $_FILES['image']['name'];
        if ($uploadedImage) {
            // Remove old image if it exists
            $oldImagePath = '../uploads/' . $product['image'];
            if (file_exists($oldImagePath) && $product['image']) {
                unlink($oldImagePath);
            }
            // Move the new image
            $image = basename($uploadedImage);
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
        }
    }

    try {
        $db = getDB();
        $stmt = $db->prepare('UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, image = :image WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'image' => $image,
            'id' => $product_id
        ]);
        header('Location: list.php');
        exit();
    } catch (Exception $e) {
        $error = "Erro ao atualizar o produto: " . $e->getMessage();
    }
}

function getProductById($id) {
    $db = getDB();
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Editar Produto</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="edit.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="description">Descrição:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="price">Preço:</label>
        <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="stock">Estoque:</label>
        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>

        <label for="image">Imagem:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Atualizar Produto</button>
    </form>

    <a href="list.php">Voltar para a lista de produtos</a>
</body>
</html>
