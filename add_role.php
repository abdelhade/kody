<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
<?php include('includes/navbar.php'); ?>

<div class="content-wrapper">
<section class="content-header">
<div class="container-fluid">


<form action="do/doadd_role.php" method="post">
<div class="card card-primary">
    <div class="card-header ">
        <div class="row">

        
        <div class="col-md-10">
            <h3>اضافه دور جديد</h3>
        </div>
        <div class="col-md-2">
        </div>
        <button type="submit" class="btn btn-light col-sm-2">حفظ</button>
    </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
        <div class="form-group border">
            <label for="rollname">اسم الدور</label>
            <input type="text" name="rollname" class="form-control form-control-sm" required>
        </div></div>
            <div class="col-md-8">
        <div class="form-group border">
            <label for="info">وصف الدور</label>
            <input type="text" name="info" class="form-control form-control-sm">
        </div></div>
        </div>
        <div class="row">
            <div class="col-md-6">

            
        <div class="table  table-responsive table-bordered table-hover" >
        <input type="text" id="itmsearch1" class="form-control form-control-sm" placeholder="search">
        <center>
            
            <table id="horsTable1" class="table  ">
                <thead>
                    <tr>
                        
                        <th class="">اسم الصلاحيه</th>
                        <th>عرض</th>
                        <th>جديد</th>
                        <th>تعديل</th>
                        <th>حذف</th>
                        <th>المفضلة</th>
                    </tr>
                    <tr>
                        <th>اختيار الكل <input type="checkbox" name="" id="checkall"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="tr1">
                        <td>المستخدمين</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_users" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_users" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_users" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_users" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_users" id="" ></td>
                    </tr>

                    <tr class="tr1">
                    <td>العملاء</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_clients" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>الموردين</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_suppliers" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_suppliers" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_suppliers" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_suppliers" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_suppliers" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>الصناديق</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_funds" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_funds" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_funds" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_funds" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_funds" id="" ></td>
                    </tr>
                    
                    <tr class="tr1">
                        <td>البنوك</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_banks" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_banks" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_banks" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_banks" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_banks" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>المخزون</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_stock" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_stock" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_stock" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_stock" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_stock" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>المصروفات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_expenses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_expenses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_expenses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_expenses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_expenses" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>الايرادات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_revenuses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_revenuses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_revenuses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_revenuses" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_revenuses" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>دائنين آخرين</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_credits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_credits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_credits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_credits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_credits" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>مدينين آخرين</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_depits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_depits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_depits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_depits" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_depits" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>الشركاء</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_partners" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_partners" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_partners" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_partners" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_partners" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>الاصول</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_assets" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_assets" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_assets" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_assets" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_assets" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الاصول القابلة للتأجير</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_rentables" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_rentables" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_rentables" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_rentables" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_rentables" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الموظفين</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_employees" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_employees" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_employees" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_employees" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_employees" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الاصناف</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_items" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_items" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_items" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_items" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_items" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>مجموعات الاصناف</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_item_groups" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_item_groups" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_item_groups" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_item_groups" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_item_groups" id="" ></td>
                    </tr>
                    
                    <tr class="tr1">
                        <td>المبيعات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_sales" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_sales" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_sales" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_sales" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_sales" id="" ></td>
                    </tr>
                    

                    <tr class="tr1">
                        <td>مردود المبيعات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_resale" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_resale" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_resale" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_resale" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_resale" id="" ></td>
                    </tr>


                    <tr class="tr1">
                        <td> المشتريات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_purchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_purchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_purchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_purchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_purcases" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>مردود المشتريات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_repurchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_repurchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_repurchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_repurchases" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_repurchases" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>سندات القبض</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_recive" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_recive" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_recive" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_recive" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_recive" id="" ></td>
                    </tr>

                    <tr class="tr1">
                        <td>سندات الدفع</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_payment" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_payment" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_payment" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_payment" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_payment" id="" ></td>
                    </tr>
                    
                    <tr class="tr1">
                        <td>العيادات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_clinics" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_clinics" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_clinics" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_clinics" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_clinics" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الحجوزات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_reservations" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_reservations" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_reservations" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_reservations" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_reservations" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>العملاء متقدم</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_advanced_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_advanced_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_advanced_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_advanced_clients" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_advanced_clients" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الادوية</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_drugs" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_drugs" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_drugs" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_drugs" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_drugs" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>بروفايل لعميل</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_client_profile" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_client_profile" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_client_profile" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_client_profile" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_client_profile" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>الفرص</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_attandance" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_attandance" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_attandance" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_attandance" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_attandance" id="" ></td>
                    </tr>
                    
                    <tr class="tr1">
                        <td>موديول الحضور ة الانصراف</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_chances" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_chances" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_chances" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_chances" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_chances" id="" ></td>
                    </tr>
                    
                    <tr class="tr1">
                        <td>المكالمات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_calls" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_calls" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_calls" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_calls" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_calls" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>قيود اليومية</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_journals" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_journals" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_journals" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_journals" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_journals" id="" ></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>حسابات الاستاذ</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_gl_reports" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_gl_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_gl_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_gl_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_gl_reports" id="" disabled></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>تقارير العيادات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_clinic_reports" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_clinic_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_clinic_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_clinic_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_clinic_reports" id="" disabled></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>تقارير التأجير</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_rent_reports" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_rent_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_rent_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_rent_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_rent_reports" id="" disabled></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>تقاريرالمرتبات</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_payroll_reports" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_payroll_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_payroll_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_payroll_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_payroll_reports" id="" disabled></td>
                    </tr>

                    
                    <tr class="tr1">
                        <td>تقارير الحضور</td>
                        <td><input type="checkbox" class="user-checkbox" name="show_hr_reports" id="" checked></td>
                        <td><input type="checkbox" class="user-checkbox" name="add_hr_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="edit_hr_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="delete_hr_reports" id="" disabled></td>
                        <td><input type="checkbox" class="user-checkbox" name="is_fav_hr_reports" id="" disabled></td>
                    </tr>
                    </tbody>

            </table>
        </div>
        </center>


            </div>
            <div class="col-md-6">
                <div class="table table-responsive">
                    <table id="horsTable1" class="table table-bordered table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>خيارات الجانب</th>
                                <th>عرض</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="tr1">
                                <td>اظهار قائمة البيانات الاساسية من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_entry"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قائمة المخزون من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_stock"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم المبيعات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_sales"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم المشتريات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_purchases"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار السندات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_vouchers"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم العيادات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_clinics"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم ادارة علاقات العملاء من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_crm"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم الحسابات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_accounts"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم الاصول من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_assets"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار التقارير من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_reports"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم اداره الموارد البشرية من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_hr"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم المرتبات من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_payroll"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم التأجير من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_rents"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار قسم ادارة الكروت من الجانب الايمن</td>
                                <td><input type="checkbox"  name="sid_cards"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>تعديل كلمات مرور المستخدمين</td>
                                <td><input type="checkbox"  name="edit_user_passwords"  class="user-checkbox" checked></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table table-responsive">
                    <table id="horsTable1" class="table table-bordered table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>الخيارات العامة </th>
                                <th>عرض</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="tr1">
                                <td>اظهار الحجوزات المنتهية</td>
                                <td><input type="checkbox"  name="show_ended_reservation"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار اجمالي الحجوزات</td>
                                <td><input type="checkbox"  name="show_total_reservation"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td> (مكرر)اظهار بيانات المريض  <span title="قد يتم التعارض مع اظهار بيانات العميل" class="text-red-500">?</span></td>
                                <td><input type="checkbox"  name="show_client_profile"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار مهمات كل الاشخاص</td>
                                <td><input type="checkbox"  name="show_all_tasks"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار الكروت في الشاشة الرئيسية</td>
                                <td><input type="checkbox"  name="show_main_cards"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار الاختصارات في الشاشة الرئيسية</td>
                                <td><input type="checkbox"  name="show_main_elements"  class="user-checkbox" checked></td>
                            </tr>
                            <tr class="tr1">
                                <td>اظهار الجداول في الشاشة الرئيسية</td>
                                <td><input type="checkbox"  name="show_main_tables"  class="user-checkbox" checked></td>
                            </tr>
                        </tbody>
                        </table>
                        </div>
                        
            </div>
        </div>
        
    </div>
</div>
</form>


<script>
    $(document).ready(function(){
      $("#itmsearch1").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#horsTable1 .tr1").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });   
</script>
<script>
    document.getElementById('checkall').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.tr1 .user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
</div>
</section>
</div>

  <?php include('includes/footer.php'); ?>