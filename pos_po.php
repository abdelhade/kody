<?php include('includes/header.php');?>
<style>
    /* Main POS Layout Styles */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    #pos {
        min-height: 100vh;
        background: transparent;
        padding: 20px;
        gap: 20px;
    }

    /* Left Panel - Products */
    #left {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        padding: 20px;
        height: calc(100vh - 40px);
        overflow: hidden;
    }

    /* Right Panel - Cart */
    #right {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        padding: 20px;
        height: calc(100vh - 40px);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Search Bar */
    #searchRow {
        margin-bottom: 20px;
        border-radius: 10px;
        padding: 10px;
    }

    #itemSearch {
        border: none;
        border-radius: 25px;
        padding: 15px 20px;
        font-size: 16px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    #itemSearch:focus {
        background: white;
        box-shadow: 0 0 20px rgba(103, 126, 234, 0.3);
        transform: translateY(-2px);
    }

    /* Categories */
    #categories {
        margin-bottom: 20px;
    }

    #categories ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    #categories li {
        flex: 0 0 auto;
    }

    .cat {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        min-width: 80px;
        text-align: center;
    }

    .cat:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        background: linear-gradient(45deg, #764ba2, #667eea);
    }

    /* Items Grid */
    .items-grid {
        height: calc(100% - 200px);
        overflow-y: auto;
        padding: 10px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        align-content: start;
    }

    .itemButton {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .itemButton:hover {
        transform: translateY(-5px);
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .itemlogo {
        margin-bottom: 10px;
    }

    .itemlogo i {
        font-size: 2rem;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .itemButton:hover .itemlogo i {
        color: white;
        transform: scale(1.1);
    }

    .itemname p {
        margin: 5px 0;
        font-weight: 600;
    }

    .itemname p:last-child {
        color: #28a745;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .itemButton:hover .itemname p:last-child {
        color: #ffd700;
    }

    /* Right Panel Styles */
    #upRight {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Cart and Action Sections */
    .cart-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        gap: 15px;
    }

    .cart-table-section {
        flex: 1;
        min-height: 200px;
        max-height: 300px;
        overflow: hidden;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }

    .payment-section {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        margin-top: 15px;
    }

    .action-section {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid rgba(102, 126, 234, 0.1);
    }


    /* Table Styles */
    .table {
        margin: 0;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table thead {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
    }

    .table thead td {
        padding: 15px 10px;
        font-weight: 600;
        text-align: center;
        border: none;
    }

    .table tbody td {
        padding: 12px 10px;
        text-align: center;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Input Styles */
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
    }

    .cashInput {
        width: 80px;
        text-align: center;
        font-weight: 600;
    }

    /* Button Styles */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 12px 20px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-danger {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
        padding: 8px 12px;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-danger:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
    }

    /* Navigation */
    nav.navbar {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px);
        border: none;
        margin-bottom: 20px;
        border-radius: 10px;
    }

    nav.navbar .nav-link {
        color: white !important;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    nav.navbar .nav-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Fullscreen Button */
    .btn-light {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #764ba2, #667eea);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #pos {
            flex-direction: column;
            padding: 10px;
        }

        #left, #right {
            height: auto;
            min-height: 400px;
        }

        .items-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .itemButton {
            min-height: 120px;
            padding: 15px;
        }
    }
</style>
<nav class="navbar navbar-expand font-xs font-light p-0 bg-slate-200" >
  <ul class="navbar-nav">   
    </li>
    <li class="nav-item d-none d-sm-inline-block" >
      <a href="index.php" class="nav-link"><?=$lang_sidemain?></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="do/do_logout.php" class="nav-link"><?=$lang_navlogout?></a>
    </li>   
  </ul>
</nav>


<div class="container-fluid">
    <div class="row h-100" id="pos">
        <!-- Left Panel - Products -->
        <div class="col-lg-8">
<?php include('elements/pos/left1.php') ?>
        </div>

        <!-- Right Panel - Cart & Payment -->
        <div class="col-lg-4">
            <div id="right">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-shopping-cart text-primary"></i> سلة المشتريات
                    </h4>
                    <button class="btn btn-light" title="وضع ملء الشاشة">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                
    <form action="do/doadd_invoice.php" method="post" id="myForm">
       <?php include('elements/pos/right0.php') ?> 
       <input type="text" name="pro_tybe" value="11" hidden>
                    
                    <!-- Cart Section -->
                    <div class="cart-section">
       <?php include('elements/pos/right1.php') ?> 
                    </div>
                    
                    <!-- Action Buttons Section -->
                    <div class="action-section">
       <?php include('elements/pos/right2.php') ?> 
                    </div>
    </form>
            </div>
        </div>
</div>
</div>

<div class="modal fade" id="addclmodal" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">اضافه عميل جديد في قاعدة البيانات</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addClientForm" >
                    <div class="form-group">
                        <label for="clname">اسم العميل</label>
                        <input type="text" class="form-control" id="clname" name="clname" placeholder="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">تليفون</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="phone2">تليفون2</label>
                        <input type="text" class="form-control" id="phone2" name="phone2" placeholder="phone2">
                    </div>
                    <div class="form-group">
                        <label for="address">عنوان</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="address">
                    </div>
                    <div class="form-group">
                        <label for="address2">عنوان 2</label>
                        <input type="text" class="form-control" id="address2" name="address2" placeholder="address2">
                    </div>
                    <div class="form-group">
                        <label for="address3">عنوان 3</label>
                        <input type="text" class="form-control" id="address3" name="address3" placeholder="address3">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-success btn-block" onclick=" dis();">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





<script>
        document.addEventListener('DOMContentLoaded', function() {
    var fullscreenButton = document.querySelector('.btn.btn-light.float-left');
    
    fullscreenButton.addEventListener('click', function() {
        if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
        }
    });
    });
</script>



<script src="js/pos.js"></script>


<?php include('includes/footer.php');?>