
<?php
include "includes/header.php";
require_once "classes/Database.php";
require_once "classes/Product.php";
require_once "classes/Category.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product");
}

$productObj  = new Product();
$categoryObj = new Category();

$product = $productObj->readOne($_GET['id']);
if (!$product) {
    die("Product not found");
}

$category = $categoryObj->readOne($product['category_id']);

$relatedProducts = array_filter(
    $productObj->getByCategory($product['category_id'], 8, 0),
    fn($p) => $p['id'] != $product['id']
);
?>


    <!------------------------------ Single product details------------------------------>
    <div class="small-container single-product">
  <div class="row">
    <div class="col-2">
      <img 
        src="adminDashboard/handlers/assets/<?= $productObj->getPrimaryImage($product['id']); ?>" 
        width="100%" 
      />
    </div>

    <div class="col-2">
      <p>Home / <?= htmlspecialchars($category['name']); ?></p>

      <h1><?= htmlspecialchars($product['name']); ?></h1>

      <h4 style="color: #ff523b;font-weight: bold"><?= number_format($product['price'], 2); ?> JD</h4>

      <input type="number" value="1" min="1" />
      <a href="cart.php?id=<?= $product['id']; ?>" class="btn">Add to Cart</a>

      <h3>Product Details <i class="fa fa-indent"></i></h3>
      <br />
      <p>
        <?= nl2br(htmlspecialchars($product['description'])); ?>
      </p>
    </div>
  </div>
</div>

    <!----------------------------------products------------------------------------->
    <div class="small-container">
  <div class="row row-2">
    <h2>Related Products</h2>
    <a href="products.php?category_id=<?= $category['id']; ?>">
      <p>View More</p>
    </a>
  </div>
</div>

<div class="small-container">
  <div class="row">
    <?php if (!empty($relatedProducts)): ?>
      <?php foreach (array_slice($relatedProducts, 0, 4) as $rp): ?>
        <div class="col-4">
          <a href="product-details.php?id=<?= $rp['id']; ?>">
            <img src="adminDashboard/handlers/assets/<?= $productObj->getPrimaryImage($rp['id']); ?>" />
          </a>

          <h4><?= htmlspecialchars($rp['name']); ?></h4>
          <p style="color: #ff523b;font-weight: bold"><?= number_format($rp['price'], 2); ?> JD</p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No related products.</p>
    <?php endif; ?>
  </div>
</div>


    
    <!-----------------------js for product gallery-------------------->

    <script>
      var productImg = document.getElementById("productImg");
      var smallImg = document.getElementsByClassName("small-img");

      smallImg[0].onclick = function () {
        productImg.src = smallImg[0].src;
      };
      smallImg[1].onclick = function () {
        productImg.src = smallImg[1].src;
      };
      smallImg[2].onclick = function () {
        productImg.src = smallImg[2].src;
      };
      smallImg[3].onclick = function () {
        productImg.src = smallImg[3].src;
      };
    </script>

<?php include "includes/footer.php"; ?>
