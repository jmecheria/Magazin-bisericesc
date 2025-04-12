<?php
header("Content-Type: application/json");

// Definim căile pentru fișierele JSON
$productsFile = "products.json";
$ordersFile   = "orders.json";

// Dacă fișierul cu produse nu există, îl creăm cu stocurile inițiale pentru cele 20 de produse
if (!file_exists($productsFile)) {
    $initialProducts = [
      ["id" => 1,  "stock" => 50],
      ["id" => 2,  "stock" => 40],
      ["id" => 3,  "stock" => 30],
      ["id" => 4,  "stock" => 60],
      ["id" => 5,  "stock" => 80],
      ["id" => 6,  "stock" => 70],
      ["id" => 7,  "stock" => 35],
      ["id" => 8,  "stock" => 45],
      ["id" => 9,  "stock" => 55],
      ["id" => 10, "stock" => 50],
      ["id" => 11, "stock" => 60],
      ["id" => 12, "stock" => 40],
      ["id" => 13, "stock" => 30],
      ["id" => 14, "stock" => 45],
      ["id" => 15, "stock" => 20],
      ["id" => 16, "stock" => 100],
      ["id" => 17, "stock" => 25],
      ["id" => 18, "stock" => 30],
      ["id" => 19, "stock" => 15],
      ["id" => 20, "stock" => 40]
    ];
    file_put_contents($productsFile, json_encode($initialProducts));
}

// Dacă fișierul cu comenzi nu există, îl creăm ca un array gol
if (!file_exists($ordersFile)) {
    file_put_contents($ordersFile, json_encode([]));
}

$action = "";
if (isset($_GET['action'])) { $action = $_GET['action']; }
else if (isset($_POST['action'])) { $action = $_POST['action']; }

switch ($action) {
    case "get_products":
        $products = json_decode(file_get_contents($productsFile), true);
        echo json_encode(["status" => "success", "products" => $products]);
        break;

    case "get_orders":
        $orders = json_decode(file_get_contents($ordersFile), true);
        echo json_encode(["status" => "success", "orders" => $orders]);
        break;

    case "place_order":
        // Se așteaptă un POST JSON cu "order"
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input["order"])) {
            echo json_encode(["status" => "error", "message" => "No order data"]);
            exit;
        }
        $order = $input["order"];
        // Actualizăm stocurile pentru produsele din comandă
        $products = json_decode(file_get_contents($productsFile), true);
        foreach ($order["items"] as $item) {
            foreach ($products as &$prod) {
                if ($prod["id"] == $item["id"]) {
                    $prod["stock"] = max(0, $prod["stock"] - $item["quantity"]);
                }
            }
        }
        file_put_contents($productsFile, json_encode($products));
        // Adăugăm comanda la lista globală
        $orders = json_decode(file_get_contents($ordersFile), true);
        $orders[] = $order;
        file_put_contents($ordersFile, json_encode($orders));
        echo json_encode(["status" => "success", "message" => "Order placed"]);
        break;

    case "update_stock_custom":
        // Așteptăm POST JSON cu "product_id" și "new_stock"
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input["product_id"]) || !isset($input["new_stock"])) {
            echo json_encode(["status" => "error", "message" => "Missing parameters"]);
            exit;
        }
        $product_id = intval($input["product_id"]);
        $new_stock = intval($input["new_stock"]);
        $products = json_decode(file_get_contents($productsFile), true);
        foreach ($products as &$prod) {
            if ($prod["id"] == $product_id) {
                $prod["stock"] = $new_stock;
                break;
            }
        }
        file_put_contents($productsFile, json_encode($products));
        echo json_encode(["status" => "success", "message" => "Product stock updated"]);
        break;

    case "reset_all_stocks":
        // Resetează stocurile tuturor produselor la valorile inițiale
        $initialProducts = [
          ["id" => 1,  "stock" => 50],
          ["id" => 2,  "stock" => 40],
          ["id" => 3,  "stock" => 30],
          ["id" => 4,  "stock" => 60],
          ["id" => 5,  "stock" => 80],
          ["id" => 6,  "stock" => 70],
          ["id" => 7,  "stock" => 35],
          ["id" => 8,  "stock" => 45],
          ["id" => 9,  "stock" => 55],
          ["id" => 10, "stock" => 50],
          ["id" => 11, "stock" => 60],
          ["id" => 12, "stock" => 40],
          ["id" => 13, "stock" => 30],
          ["id" => 14, "stock" => 45],
          ["id" => 15, "stock" => 20],
          ["id" => 16, "stock" => 100],
          ["id" => 17, "stock" => 25],
          ["id" => 18, "stock" => 30],
          ["id" => 19, "stock" => 15],
          ["id" => 20, "stock" => 40]
        ];
        file_put_contents($productsFile, json_encode($initialProducts));
        echo json_encode(["status" => "success", "message" => "All stocks reset"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}
?>
