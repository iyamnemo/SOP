<?php
//Guard
require_once '_guards.php';
Guard::cashierOnly();

$products = Product::all();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>omen</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/admin.css">
    <link rel="stylesheet" type="text/css" href="./css/cashier.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">

    <script src="./js/main.js"></script>
    <script src="./js/cashier.js"></script>
    
    <!-- Datatables  Library -->
    <link rel="stylesheet" type="text/css" href="./css/datatable.css">
    <script src="./js/datatable.js"></script>

    <!-- AlpineJS Library -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>



</head>
<body>

    <?php require 'templates/admin_header.php' ?>

    <div class="flex">
        <?php require 'templates/admin_navbar.php' ?>
        <main x-data='products(<?= json_encode($products) ?>)'>
            <div class="flex h-full">
                <div class="products">
                    <div class="subtitle">Products</div>
                    <hr/>

                    <?php displayFlashMessage('transaction') ?>

                    <table id="productsTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Stocks</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $skuCounter = 1; ?>
        <?php foreach ($products as $product) : ?>
        <tr>
            <!-- Dynamically generate SKU -->
            <td>SKU<?= str_pad($skuCounter++, 3, '0', STR_PAD_LEFT) ?></td>
            <td><?= $product->name ?></td>
            <td><?= $product->category->name ?></td>
            <td><?= $product->quantity ?></td>
            <td><?= $product->price ?></td>
            <td>
                <a @click="addToCart(<?= $product->id ?>)" href="#" class="text-green-300">Add Product</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                </div>
                <div class="forms">
                    <div class="flex flex-col h-full">
                        <div>
                            <div class="subtitle">Customer Orders</div>
                            <hr/>
                        </div>

                        <div id="cardItemsContainer" class="flex-grow" style="overflow-y: auto;">
                            <template x-for="cart in carts">
                                <div class="cart-item">
                                    <span class="left" x-text="cart.product.name"></span>
                                    <span class="left" x-text="cart.product.price"></span>
                                    <div class="middle">
                                        <div class="cart-item-buttons">
                                            <button @click="subtractQuantity(cart)">-</button>
                                            <span x-text="cart.quantity"></span>
                                            <button @click="addQuantity(cart)">+</button>
                                        </div>
                                    </div>
                                    <span class="right" x-text="(cart.quantity * cart.product.price) + 'PHP'"></span>
                                </div>                                
                            </template>
                        </div>

         
                        <form action="api/cashier_controller.php" method="POST" @submit="validate">
    <input type="hidden" name="action" value="proccess_order">

    <template x-for="(cart,i) in carts" :key="cart.product.id">
        <div>
            <input type="hidden" :name="`cart_item[${i}][id]`" :value="cart.product.id">
            <input type="hidden" :name="`cart_item[${i}][quantity]`" :value="cart.quantity">
        </div>
    </template>

    <div>
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required>
    </div>

    <div>
        <label for="payment">Payment:</label>
        <input type="number" x-model="payment" step="0.25" name="payment" id="payment" required/>
     
    </div>

    <div>
        <span>Total Price: </span>
        <span class="font-bold" x-text="totalPrice + 'php'">0php</span>
    </div>

    <!-- Display the Tax -->
    <div>
        <span>Tax (12%): </span>
        <span class="font-bold" x-text="(totalPrice * 0.12).toFixed(2) + 'php'">0php</span>
    </div>

    <!-- Display the Total with Tax -->
    <div>
        <span>Total with Tax: </span>
        <span class="font-bold" x-text="(totalPrice + (totalPrice * 0.12)).toFixed(2) + 'php'">0php</span>
    </div>

    <button class="btn btn-primary mt-16 w-full">Process Order</button>
</form>





</div>
</div>
</div>
</main>
</div>

<script type="text/javascript">
var dataTable = new simpleDatatables.DataTable("#productsTable")
</script>


</body>
</html>