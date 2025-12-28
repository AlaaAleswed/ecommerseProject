<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php include "includes/header.php"; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card shadow">
                <div class="card-body">

                    <h4 class="mb-3">Order Summary</h4>

                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Running Shoes × 2</span>
                            <strong>100 JD</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Sport T-Shirt × 1</span>
                            <strong>25 JD</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Cap × 3</span>
                            <strong>30 JD</strong>
                        </li>

                        <li class="list-group-item d-flex justify-content-between text-danger">
                            <strong>Total</strong>
                            <strong>155 JD</strong>
                        </li>
                    </ul>
                    <hr>
                    <h4 class="mb-3">Payment Information</h4>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" placeholder="Full Name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" placeholder="Phone Number">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" class="form-control" placeholder="Country">
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" placeholder="City">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" placeholder="XXXX XXXX XXXX XXXX">
                        </div>

                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label">Expiry Month</label>
                                <input type="text" class="form-control" placeholder="MM">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Expiry Year</label>
                                <input type="text" class="form-control" placeholder="YY">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">CVC</label>
                                <input type="text" class="form-control" placeholder="CVC">
                            </div>
                        </div>

                        <button class="btn btn-danger w-100 mt-3">
                            Pay Now
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include "includes/footer.php"; ?>