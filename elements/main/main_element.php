<div class="quick-actions-section">
    <div class="row g-3">
        <!-- العمود الأول -->
        <div class="col-lg-3 col-md-6">
            <div class="action-card-group">
                <!-- إضافة صنف -->
                <div class="action-card-main">
                    <a href="add_item.php" class="action-btn" style="background: var(--primary-dark) !important;">
                        <div class="btn-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="btn-text">
                            <span class="btn-title">إضافة صنف</span>
                        </div>
                        <div class="btn-hover-effect">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </a>
                </div>
                
                <div class="action-card-row">
                    <!-- إضافة عميل -->
                    <div class="action-card-half">
                        <a href="add_account.php?parent_id=122" class="action-btn" style="background: var(--primary-color) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">إضافة عميل</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- إضافة مورد -->
                    <div class="action-card-half">
                        <a href="add_account.php?parent_id=211" class="action-btn" style="background: var(--primary-light) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">إضافة مورد</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- العمود الثاني -->
        <div class="col-lg-3 col-md-6">
            <div class="action-card-group">
                <!-- فاتورة مبيعات -->
                <div class="action-card-main">
                    <a href="sales.php?q=buy" class="action-btn" style="background: var(--neutral-50) !important; color: var(--primary-dark) !important;">
                        <div class="btn-icon" style="color: var(--primary-dark) !important;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="btn-text">
                            <span class="btn-title">فاتورة مبيعات</span>
                        </div>
                        <div class="btn-hover-effect" style="color: var(--primary-dark) !important;">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </a>
                </div>
                
                <div class="action-card-row">
                    <!-- سند قبض -->
                    <div class="action-card-half">
                        <a href="add_voucher.php?t=recive" class="action-btn" style="background: var(--primary-dark) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">سند قبض</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- مرتجع مبيعات -->
                    <div class="action-card-half">
                        <a href="sales.php?q=rebuy" class="action-btn" style="background: var(--primary-color) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">مرتجع مبيعات</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- العمود الثالث -->
        <div class="col-lg-3 col-md-6">
            <div class="action-card-group">
                <!-- فاتورة مشتريات -->
                <div class="action-card-main">
                    <a href="sales.php?q=sale" class="action-btn" style="background: var(--primary-light) !important;">
                        <div class="btn-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="btn-text">
                            <span class="btn-title">فاتورة مشتريات</span>
                        </div>
                        <div class="btn-hover-effect">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </a>
                </div>
                
                <div class="action-card-row">
                    <!-- سند دفع -->
                    <div class="action-card-half">
                        <a href="add_voucher.php?t=payment" class="action-btn" style="background: var(--neutral-50) !important; color: var(--primary-dark) !important;">
                            <div class="btn-icon" style="color: var(--primary-dark) !important;">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">سند دفع</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- مرتجع مشتريات -->
                    <div class="action-card-half">
                        <a href="sales.php?q=resale" class="action-btn" style="background: var(--primary-dark) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">مرتجع مشتريات</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- العمود الرابع -->
        <div class="col-lg-3 col-md-6">
            <div class="action-card-group">
                <!-- Pulse تقييم لحظي -->
                <div class="action-card-main">
                    <a href="pulse.php" class="action-btn" style="background: var(--warning-color) !important;">
                        <div class="btn-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="btn-text">
                            <span class="btn-title">Pulse (تقييم لحظي)</span>
                        </div>
                        <div class="btn-hover-effect">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </a>
                </div>
                
                <div class="action-card-row">
                    <!-- إحصائيات Pulse -->
                    <div class="action-card-half">
                        <a href="pulse_stats.php" class="action-btn" style="background: var(--info-color) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">إحصائيات Pulse</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- قائمة الاصناف -->
                    <div class="action-card-half">
                        <a href="myitems.php" class="action-btn" style="background: var(--primary-color) !important;">
                            <div class="btn-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="btn-text">
                                <span class="btn-title">قائمة الأصناف</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
