<?php
include "includes/header.php";
require_once "classes/Database.php";
require_once "classes/Product.php";
require_once "classes/Category.php";

$productObj = new Product();
$categoryObj = new Category();

$selectedCategory = isset($_GET['category_id']) && is_numeric($_GET['category_id'])
  ? (int) $_GET['category_id']
  : null;

$categories = $categoryObj->readAll(100, 0);

if ($selectedCategory) {
  $products = $productObj->getByCategory($selectedCategory, 100, 0);
} else {
  $products = $productObj->readAll(100, 0);
}
?>

<!------------------------------ Products------------------------------>
<div class="small-container">
  <div class="row row-2">
    <h2>All Products</h2>

    <form method="GET">
      <select name="category_id" onchange="this.form.submit()">
        <option value="">All Categories</option>

        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id']; ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($cat['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <!--<h2 class="title" >Featured Products</h2>-->
  <div class="row">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
        <div class="col-3">
          <a href="products-details.php?id=<?= $product['id']; ?>">
            <img src="adminDashboard/handlers/assets/<?= $productObj->getPrimaryImage($product['id']); ?>" />
          </a>

          <h4><?= htmlspecialchars($product['name']); ?></h4>

          <p style="font-weight: bold;color: #ff523b ;font-size: 0.9em"><?= number_format($product['price'], 2); ?> JD</p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>
  </div>


  <!-------------- new row----------------->




  <div class="page-btn">
    <span>1</span>
    <span>2</span>
    <span>3</span>
    <span>4</span>
    <span>&#8594;</span>
  </div>
</div>

<!-- <div class="page-btn">
    <span>1</span>
    <span>2</span>
    <span>3</span>
    <span>4</span>
    <span>&#8594;</span> -->

<?php include "includes/footer.php"; ?>