<?php
require_once "classes/Database.php";
require_once "classes/Product.php";

$productObj = new Product();

$latestProducts = $productObj->readAll(3, 0); 
?>

<div class="header">
    <div class="container">

        <?php include "includes/header.php"; ?>

        <div class="row">
            <div class="col-2">
                <h1>Give your Workout <br>A New Style!</h1>
                <p>Success isn't always about greatness. It's about consistency. Consistent<br>hard work gains
                    success. Greatness will come.</p>
                <a href="products.html" class="btn">Explore Now &#8594;</a>
            </div>
            <div class="col-2">
                <img src="assets/image1.png">
            </div>
        </div>
    </div>
</div>
<!------------------------------ featured categories------------------------------>
<div class="categories">
    <div class="small-container">
        <div class="row">
            <div class="col-3">
                <img src="assets/training.jpg">
            </div>
            <div class="col-3">
                <img src="assets/acc.jpg">
            </div>
            <div class="col-3">
                <img src="assets/warmup.jpg">
            </div>
        </div>
    </div>
</div>

<!------------------------------ featured Products------------------------------>
<div class="small-container">
    <h2 class="title">Latest Products</h2>
    <div class="row">
        <?php foreach($latestProducts as $p): ?>
        <div class="col-3">
            <a href="products-details.php?id=<?= $p['id']; ?>">
                <img src="adminDashboard/handlers/assets/<?= $productObj->getPrimaryImage($p['id']); ?>" />
            </a>
            <a href="products-details.php?id=<?= $p['id']; ?>">
                <h4><?= htmlspecialchars($p['name']); ?></h4>
            </a>
            <p style="color:#ff523b;font-weight:bold;;font-size: 0.9em"><?= number_format($p['price'], 2); ?> JD</p>
        </div>
        <?php endforeach; ?>
    </div>
</div>



<!--------------------------`   offer   --------------------------------->
<div class="offer">
    <div class="small-container">
        <div class="row">
            <div class="col-2">
                <img src="assets/image1.png" class="offer-img">
            </div>
            <div class="col-2">
                <p>Exclusively Available on RedStore</p>
                <h1>Sports Tools</h1>
                <small> Buy latest collections of sports Tools online on Redstore at best prices from top brands
                     at your leisure at best prices. </small><br>
                <a href="products.php" class="btn">Buy Now &#8594;</a>
            </div>
        </div>
    </div>
</div>



<!------------------------------Testimonial---------------------------------->
<div class="testimonial">
    <div class="small-container">
        <div class="row">
            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                    the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                    of type and scrambled it to make a type specimen book. </p>
                <div class="rating">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star-o"></i>
                </div>
                <img src="assets/user-1.png">
                <h3>Sean Parkar</h3>
            </div>
            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <p>It is a long established fact that a reader will be distracted by the readable content of a page
                    when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
                    distribution of letters, as opposed to using 'Content here, content here', making it look like
                    readable English.</p>
                <div class="rating">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star-o"></i>
                </div>
                <img src="assets/user-2.png">
                <h3>Mike Smith</h3>
            </div>
            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                    the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                    of type and scrambled it to make a type specimen book. </p>
                <div class="rating">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star-o"></i>
                </div>
                <img src="assets/user-3.png">
                <h3>Mabel Joe</h3>
            </div>
        </div>
    </div>
</div>

<!----------------------------------Brands------------------------------------>
<div class="brands">
    <div class="small-container">
        <div class="row">
            <div class="col-5">
                <img src="assets/logo-godrej.png" alt="">
            </div>
            <div class="col-5">
                <img src="assets/logo-oppo.png" alt="">
            </div>
            <div class="col-5">
                <img src="assets/logo-coca-cola.png" alt="">
            </div>
            <div class="col-5">
                <img src="assets/logo-paypal.png" alt="">
            </div>
            <div class="col-5">
                <img src="assets/logo-philips.png" alt="">
            </div>
        </div>
    </div>
</div>


<?php include "includes/footer.php"; ?>