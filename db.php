<?php
// db.php

include_once 'config.php';

// Função para obter a conexão com o banco de dados
function getDB() {
    global $pdo;
    return $pdo;
}

// Função para adicionar um novo produto
function addProduct($name, $description, $price, $stock, $image) {
    $db = getDB();
    $sql = "INSERT INTO products (name, description, price, stock, image) VALUES (:name, :description, :price, :stock, :image)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':image', $image);
    return $stmt->execute();
}

// Função para editar um produto
function editProduct($id, $name, $description, $price, $stock, $image) {
    $db = getDB();
    $sql = "UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, image = :image WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':image', $image);
    return $stmt->execute();
}

// Função para deletar um produto
function deleteProduct($id) {
    $db = getDB();
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// Função para listar produtos
function getProducts() {
    $db = getDB();
    $sql = "SELECT * FROM products";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter detalhes do produto
function getProduct($id) {
    $db = getDB();
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Função para adicionar um item ao carrinho
function addToCart($userId, $productId, $quantity) {
    $db = getDB();
    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':quantity', $quantity);
    return $stmt->execute();
}

// Função para obter os itens do carrinho
function getCartItems($userId) {
    $db = getDB();
    $sql = "SELECT * FROM cart WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para processar o checkout
function checkout($userId, $totalPrice, $paymentMethod) {
    $db = getDB();
    $sql = "INSERT INTO orders (user_id, total_price, payment_method, payment_status, order_status) VALUES (:user_id, :total_price, :payment_method, 'pending', 'processing')";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':total_price', $totalPrice);
    $stmt->bindParam(':payment_method', $paymentMethod);
    $stmt->execute();
    $orderId = $db->lastInsertId();

    // Adicionar itens do carrinho ao pedido
    $cartItems = getCartItems($userId);
    foreach ($cartItems as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $item['product_id']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':price', $item['price']);
        $stmt->execute();
    }

    // Limpar o carrinho
    $sql = "DELETE FROM cart WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
}
?>
