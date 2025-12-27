<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="card card-stat blue shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-3x mb-3 text-primary"></i>
                    <h4>23</h4>
                    <p>Products Added</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat yellow shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-warning"></i>
                    <h4>5</h4>
                    <p>Number of Users</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat green shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-bag fa-3x mb-3 text-success"></i>
                    <h4>9</h4>
                    <p>Number of Orders</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat red shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-3x mb-3 text-danger"></i>
                    <h4>JD1055</h4>
                    <p>Total Sales</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Order Date</th>
                            <th>Total Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>34</td>
                            <td>2023-01-15 08:59:36</td>
                            <td>1</td>
                            <td>JD45</td>
                        </tr>
                        <tr>
                            <td>35</td>
                            <td>2023-01-15 09:02:16</td>
                            <td>1</td>
                            <td>JD60</td>
                        </tr>
                        <tr>
                            <td>36</td>
                            <td>2023-01-15 15:02:19</td>
                            <td>1</td>
                            <td>JD50</td>
                        </tr>
                        <tr>
                            <td>37</td>
                            <td>2023-01-17 17:02:25</td>
                            <td>1</td>
                            <td>JD80</td>
                        </tr>
                        <tr>
                            <td>38</td>
                            <td>2023-01-17 17:08:11</td>
                            <td>1</td>
                            <td>JD50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>