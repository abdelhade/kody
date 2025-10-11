<?php include('includes/header.php');?>
<style>
    
    #upRight{
        height: 450px;
        position: relative;
        display: flex;
        flex-direction: column;
        

    }
    #upRight2 {
        position: absolute;
        bottom: 200px;
        width:100%;
    }
    #upRight1{
        height: 500px;
        position: absolute; 
        width:100%;
        overflow:scroll;

    }
    #downRight{
        height: 400px;
    }
    .cat{
        width:120px;
    }
    .cashInput{
        width:60px;

    }
    #right2 input{
        border:3px !important;
        background-color:white;
        color:black;

    }
    #upRight2 .row{
        width:100%;             
    }
    #downRight2 {
    
    background-color: white; /* تأكد من وضوح الخلفية */
    padding: 10px;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1); /* ظل خفيف */
    z-index: 1000; /* اجعله فوق العناصر الأخرى */
    }
</style>
<nav class="navbar navbar-expand font-xs font-light p-0 bg-slate-200" >
  <ul class="navbar-nav">     <svg class="mr-3 size-5 animate-spin ..." viewBox="0 0 24 24">
    <!-- ... -->
  </svg>
    </li>
    <li class="nav-item d-none d-sm-inline-block" >
      <a href="index.php" class="nav-link"><?=$lang_sidemain?></a>
    </li>
    <li>
    <?php include('elements/pos/close_modal.php')?>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="do/do_logout.php" class="nav-link"><?=$lang_navlogout?></a>

    </li>   
  </ul>


</nav>

<?php if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();
} ?>



<div class="row" id="pos">
<div class="col-md-4 " id="right">
<button class="btn btn-light float-left"><i class="fas fa-vector-square"></i></button>   
    <form action="do/doadd_invoice.php" method="post" id="myForm">
       <?php include('elements/pos/right0.php') ?> 
       <?php include('elements/pos/right1.php') ?> 
       <?php include('elements/pos/right2.php') ?> 
    </form>
</div>
      <?php include('elements/pos/left1.php') ?>
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


<script>
    const myModal = document.getElementById('closed')
    const myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', () => {
  myInput.focus()
    })
</script>

<script src="js/pos.js"></script>


<?php include('includes/footer.php');?>