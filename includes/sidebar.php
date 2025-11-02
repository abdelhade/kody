<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 font-light">
  <!-- Brand Logo -->
  <?php include('includes/userprev.php') ?>

  <!--                                             Sidebar                                                                        -->
  <div class="sidebar" style="height:100%; overflow-y: auto;">

    <div class="user-panel d-flex flex-column">
      <div class="d-flex align-items-center mb-2">
        <div class="image-user me-2">
          <img src="assets/logo/hors.png" alt="User Image"
            style="height: 45px; width: 45px; border-radius: 10px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
            onerror="this.onerror=null; this.src='assets/logo/hors.png';">
        </div>
        <div class="info flex-grow-1">
          <a href="" class="d-block" style="margin-bottom: 0;"><?php echo "اهلا يا " . $_SESSION['login'] ?></a>
        </div>
      </div>
      <div class="search-wrapper" style="position: relative;">
        <i class="fas fa-search"
          style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.85rem; pointer-events: none; z-index: 1;"></i>
        <input class="form-control form-control-sm" type="text" placeholder="ابحث في القائمة..." id="searchSide"
          style="padding-right: 35px;">
      </div>
    </div>
    <nav class="mt-2">

      <!--                                             main                                                                        -->

      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item has-treeview">
          <a href="index.php" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p> <?= $lang_sidemain ?> </p>
          </a>
        </li>


        <!--                                             البيانات الاساسيه                                                                        -->
        <?php if (($role['sid_entry'] ?? 0) == 1) { ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-pen"></i>
              <p>
                <?= $lang_sideentry ?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview bg-white-950 shadow-inner shadow-slate-500" id="acc-report"
              style="display: none;">


              <li class="nav-item">
                <a href="acc_report.php?acc=clients" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>العملاء</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="acc_report.php?acc=suppliers" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الموردين</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=funds" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الصناديق</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=banks" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>البنوك</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=stores" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>المخازن</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="acc_report.php?acc=expenses" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>المصروفات</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="acc_report.php?acc=revenous" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الايرادات</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=creditors" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>دائنين متوعين</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=depitors" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>مدينين متنوعين</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=partners" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الشركاء</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="acc_report.php?acc=assets" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الاصول</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="acc_report.php?acc=rentable" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>الاصول القابلة للتأجير</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="mytowns.php?acc=rentable" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>المدن</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php?acc=employees" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p><?= $lang_sideemployees ?></p>
                </a>
              </li>
            </ul>
          </li>
        <?php } ?>





        <!--                                                                        -->
        <?php if (($role['sid_stock'] ?? 0) == 1) { ?>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-store"></i>
              <p>
                ادارة المخزون
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" id="stock" style="display: none;">


              <li class="nav-item">
                <a href="add_item.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    صنف جديد
                  </p>
                </a>
              </li>




              <li class="nav-item">
                <a href="acc_report.php?acc=stores" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    المخازن
                  </p>
                </a>
              </li>


              <li class="nav-item" id="myitems">
                <a href="myitems.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    الاصناف
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="myunits.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    الوحدات
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="mygroups.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    المجموعات
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="item_categories.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    التصنيفات
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="barcode_search.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    عرض سعر الصنف
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="item_categories.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    ضبط الارصدة الافتتاحية للمخازن
                  </p>
                </a>
              </li>



            </ul>
          </li>
        <?php } ?>





        <?php if (($role['sid_sales'] ?? 0) == 1) { ?>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-store"></i>
              <p>
                نقاط البيع
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" id="stock" style="display: none;">

              <li class="nav-item">
                <a href="pos_barcode.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    نقطه بيع باركود
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="crud_tables.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>ادارة الطاولات </p>
                </a>
              </li>



              <li class="nav-item" id="myitems">
                <a href="closed_sessions.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>الشيفتات المنتهية</p>
                </a>
              </li>

            </ul>
          </li>
        <?php } ?>



        <?php if (($role['sid_cards'] ?? 0) == 1) { ?>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>
                ادارة الكروت <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" id="cards" style="display: none;">

              <li class="nav-item">
                <a href="add_booking.php" class="nav-link">
                  <i class="nav-icon fas fa-plus"></i>
                  <p>
                    اضافة كارت
                  </p>
                </a>
              </li>



              <li class="nav-item" id="card-pass">
                <a href="booking.php" class="nav-link">
                  <i class="nav-icon fas fa-arrow-right"></i>
                  <p>مرور الكارت</p>
                </a>
              </li>

              <li class="nav-item" id="card-management">
                <a href="bookings.php" class="nav-link">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p>ادارة الكروت</p>
                </a>
              </li>

              <li class="nav-item" id="card-clients">
                <a href="clients.php" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>ادارة العملاء</p>
                </a>
              </li>


            </ul>
          </li>
        <?php } ?>







        <?php if (($role['sid_purchases'] ?? 0) == 1) { ?>


          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fa fas-sharp fa-solid fa-file-invoice-dollar fas-2xl"></i>
              <p>
                المشتريات
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">





              <li class="nav-item">
                <a href="sales.php?q=sale" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    فاتورة مشتريات
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="sales.php?q=resale" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    فاتورة مردود مشتريات
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="sales.php?q=po" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>أمر شراء
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="pos_po.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p> أمر شراء باركود
                  </p>
                </a>
              </li>



            </ul>
          </li>
        <?php } ?>




        <?php if ($rowstg['showpay'] == 1) { ?>
          <?php if (($role['sid_sales'] ?? 0) == 1) { ?>
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link nav-link-basic">
                <i class="nav-icon fa fas-sharp fa-solid fa-file-invoice-dollar fas-2xl"></i>
                <p>
                  المبيعات
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">




                <li class="nav-item">
                  <a href="sales.php?q=buy" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      فاتورة مبيعات
                    </p>
                  </a>
                </li>




                <li class="nav-item">
                  <a href="sales.php?q=rebuy" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      أمر بيع
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="sales.php?q=offer" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>عرض سعر للعميل
                    </p>
                  </a>
                </li>
              </ul>
            </li>
          <?php }
        } ?>







        <?php if (($role['sid_vouchers'] ?? 0) == 1) { ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>
                السندات
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">


              <li class="nav-item">
                <a href="add_voucher.php?t=recive" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    سند القبض
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="add_voucher.php?t=payment" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    سند دفع
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="vouchers.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    السندات
                  </p>
                </a>
              </li>
            </ul>
          </li>
        <?php } ?>







        <!--                                                                        -->

        <!--                                                                        -->








        <?php if ($rowstg['showhr'] == 1) { ?>
          <?php if (($role['sid_hr'] ?? 0) == 1) { ?>

            <li class="nav-item has-treeview">
              <a href="#" class="nav-link nav-link-basic">

                <i class="nav-icon fas fa-address-book"></i>
                <p>
                  بيانات الوظائف
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
                <li class="nav-item">
                  <a href="employees.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideemployees ?></p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="shifts.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_shifts ?></p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="jops.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sidejops ?></p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="joprules.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_siderules ?></p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="joplevels.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sidejoplevels ?></p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="departments.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sidedepartments ?></p>
                  </a>
                </li>




              </ul>
            </li>
          <?php }
        } ?>


        <?php if ($rowstg['showrent'] == 1) { ?>
          <?php if (($role['sid_rents'] ?? 0) == 1) { ?>
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link nav-link-basic">
                <i class="nav-icon fas fa-money-bill-wave"></i>
                <p>
                  قسم التأجير
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">


                <li class="nav-item">
                  <a href="acc_report.php?acc=rentable" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      الاصول القابلة للتأجير
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="add_rent.php" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      عقد ايجار
                    </p>
                  </a>
                </li>


                <li class="nav-item">
                  <a href="rentables.php" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      الوحدات الايجارية
                    </p>
                  </a>
                </li>


                <li class="nav-item">
                  <a href="myrentables.php" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      المدة الايجارية
                    </p>
                  </a>
                </li>



              </ul>
            </li>



          <?php }
        } ?>









        <!-- clinck -->
        <?php if ($rowstg['showclinc'] == 1) { ?>
          <?php if (($role['sid_clinics'] ?? 0) == 1) { ?>
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link nav-link-basic">
                <i class="nav-icon fas fa-stethoscope" style="color:#FFD43B"></i>
                <p>
                  العيادة
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">

                <li class="nav-item">
                  <a href="reservations.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p> الحجوزات </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="clients.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p>بيانات المرضي</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="reservations.php?c=end" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p> الحجوزات المنتهية </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="drugs.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p>الادويه </p>
                  </a>
                </li>


              </ul>

            </li>
          <?php }
        } ?>

        <li class="divider"></li>



        <!-------------------------------الحضور---------------------------------------->
        <?php if ($rowstg['showatt'] == 1) { ?>
          <?php if (($role['sid_payroll'] ?? 0) == 1) { ?>


            <li class="nav-item has-treeview">
              <a href="#" class="nav-link nav-link-basic">
                <i class="nav-icon fas fa-calendar"></i>
                <p>
                  <?= $lang_sideattendance ?>
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
                <li class="nav-item">
                  <a href="manualattandance.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p> سجل الحضور و الانصراف </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="machinelog.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p>استيراد الحضور</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="calcsalary.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideattennotebook ?></p>
                  </a>
                </li>



                <li class="nav-item">
                  <a href="shifts.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideshiftmanagement ?></p>
                  </a>
                </li>
              </ul>
            <?php }
        } ?>



          <!--                                            المرتبات                               -->

          <?php if ($rowstg['showatt'] == 1) { ?>
            <?php if (($role['sid_payroll'] ?? 0) == 1) { ?>








              <!----------------------------------           المهمات          ---------------------------------------->


              <?php if ($up['tasksindex'] !== '1') { ?>





              <li class="nav-item has-treeview">
                <a href="#" class="nav-link nav-link-basic">
                  <i class="nav-icon fas fa-tasks"></i>
                  <p>
                    الموارد البشرية
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">

                  <li class="nav-item has-treeview">
                    <a href="#" class="nav-link nav-link-basic">
                      <i class="nav-icon fas fa-tasks"></i>
                      <p>
                        المهمات
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
                      <li class="nav-item">
                        <a href="add_task.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>مهمه جديده</p>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a href="tasks.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>كل المهمات</p>
                        </a>
                      </li>


                      <li class="nav-item">
                        <a href="followup.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p> المهمات المنتهية</p>
                        </a>
                      </li>




                    </ul>

                  </li>




                  <li class="nav-item has-treeview  shadow-slate-500">
                    <a href="#" class="nav-link nav-link-basic">
                      <i class="nav-icon fas fa-tasks"></i>
                      <p>
                        KPIs <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview shadow-inner  shadow-slate-500" style="display: none;">
                      <li class="nav-item">
                        <a href="kbis.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>معدلات الآداء</p>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a href="emp_kbis.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>وزن KPI</p>
                        </a>
                      </li>


                    </ul>
                  </li>




                  <li class="nav-item has-treeview  shadow-inner ">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-list"></i>
                      <p>
                        <?= $lang_sidecontracts ?>
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview shadow-inner shadow-inner " style="display: none;">


                      <li class="nav-item">
                        <a href="trainingcontracts.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p><?= $lang_sidetrainingcontracts ?></p>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a href="hiringcontracts.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p><?= $lang_sidehiringcontracts ?></p>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a href="externalcontracts.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p><?= $lang_sideoutsourcecontracts ?></p>
                        </a>
                      </li>
                    </ul>
                  </li>




                  <li class="nav-item has-treeview  shadow-inner ">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-list"></i>
                      <p>
                        الانتاجية
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview shadow-inner shadow-inner " style="display: none;">


                      <li class="nav-item">
                        <a href="production.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>الانتاج اليومي</p>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a href="hiringcontracts.php" class="nav-link">
                          <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                          <p>احتساب الانتاج لمدة</p>
                        </a>
                      </li>


                    </ul>
                  </li>






                  <li class="nav-item has-treeview">
                    <a href="cvs.php" class="nav-link">
                      <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                      <p>
                        السير الذاتيه

                      </p>
                    </a>
                  </li>


                </ul>
              </li>






            <?php }
            } ?>







        <?php } ?>










        <?php if (($role['sid_crm'] ?? 0) == 1) { ?>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                ادارة العملاء
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
              <li class="nav-item">
                <a href="acc_report.php?acc=clients" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>ادخال العملاء من الحسابات</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="clients.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>ادخال العملاء متقدم</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="chances.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p> ادارة الفرص</p>
                </a>
              </li>


              <li class="nav-item has-treeview">
                <a href="calls.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>
                    ادارة المكالمات
                  </p>
                </a>
              </li>

              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>

                  <p>
                    <?= $lang_siderequests ?>
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
                  <li class="nav-item">
                    <a href="orders.php" class="nav-link">
                      <i class="far "> --- </i>
                      <p><?= $lang_siderequestsmanagement ?></p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="prints.php" class="nav-link">
                      <i class="far "> --- </i>
                      <p><?= $lang_sidesettings ?></p>
                    </a>
                  </li>

                </ul>
              </li>
            </ul>
          </li>

        <?php } ?>







        <!-- تغيير كلمة المرور -->
        <li class="nav-item">
          <a href="change_password.php" class="nav-link">
            <i class="nav-icon fas fa-key"></i>
            <p>تغيير كلمة المرور</p>
          </a>
        </li>

        <?php if (($role['sid_accounts'] ?? 0) == 1) { ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-book"></i>
              <p>
                الحسابات العامه
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">


              <li class="nav-item">
                <a href="add_journal.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    انشاء قيد يومية
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="addmulti_journal.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    انشاء قيد متعدد
                  </p>
                </a>
              </li>




              <li class="nav-item">
                <a href="daily_journal.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    القيود اليوميه
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="accounts.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>شجرة الحسابات
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="acc_report.php" class="nav-link">
                  <i class="nav-icon fas fa-list "></i>
                  <p>قائمة الحسابات مع الارصدة
                  </p>
                </a>
              </li>



            </ul>
          </li>
        <?php } ?>




        <?php if (($role['sid_accounts'] ?? 0) == 1) { ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-book"></i>
              <p>النظام
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">


              <li class="nav-item">
                <a href="start_balance.php" class="nav-link">
                  <i class="nav-icon fas fa-list "></i>
                  <p> الرصيد الافتتاحي للحسابات
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="items_start_balance.php" class="nav-link">
                  <i class="nav-icon fas fa-list "></i>
                  <p>الرصيد الافتتاحي للأصناف</p>
                </a>
              </li>



            </ul>
          </li>
        <?php } ?>









        <?php if (($role['sid_assets'] ?? 0) == 1) { ?>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-book"></i>
              <p>
                عمليات الاصول
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">


              <li class="nav-item">
                <a href="add_journal.php?a=1" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    شراء اصل

                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="add_journal.php?a=2" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>بيع اصل
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="nav-icon fas fa-list "></i>
                  <p>اهلاك اصل
                  </p>
                </a>
              </li>


            </ul>
          </li>
        <?php } ?>



        <?php if (($role['sid_reports'] ?? 0) == 1) { ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-link-basic">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                <?= $lang_sidereports ?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
              <li class="nav-item">
                <a href="summary.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>كشف حساب</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="reps_cl.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>تقاير العيادات</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="reports.php?t=rents" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p>تقارير التأجير</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="sales-reports.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>تقارير المبيعات</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="operations_summary.php?q=sale" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    فواتير المشتريات
                  </p>
                </a>
              </li>



              <li class="nav-item">
                <a href="items_summery.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    المبيعات اصناف
                  </p>
                </a>
              </li>


              <li class="nav-item">
                <a href="prints.php" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p><?= $lang_sidesalariesreports ?></p>
                </a>
              </li>

            </ul>
          <?php } ?>





    </nav>


    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->


  <script>
    $(function () {
      if (window.location.href.includes('acc_report.php')) {
        $('#acc-report').show().addClass('bg-slate-100');
      }

      if (window.location.href.includes('myitems.php')) {
        $('#stock').show().addClass('bg-slate-100');
        $('#myitems').addClass('bg-slate-200');
      }

      if (window.location.href.includes('reservations.php')) {
        $('#clinic').show().addClass('bg-slate-100');
        $('#reservations').addClass('bg-slate-200');
      }


    });
  </script>
</aside>