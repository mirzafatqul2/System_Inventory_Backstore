<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-warning elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="<?= base_url() ?>assets/img/logomrdiy.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light font-weight-bold">BACKSTORE MR DIY</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url() ?>assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Mirza Fatqul</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php foreach ($menuItems as $key => $menu) : ?>
                    <?php if (isset($menu['submenu']) && !empty($menu['submenu'])): ?>
                        <li class="nav-item <?= $menu['active'] ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= $menu['active'] ? 'active' : '' ?>">
                                <i class="nav-icon <?= $menu['icon'] ?>"></i>
                                <p>
                                    <?= $menu['label'] ?>
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php foreach ($menu['submenu'] as $subKey => $subMenu): ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url($subMenu['url']) ?>" class="nav-link <?= $subMenu['active'] ? 'active' : '' ?>">
                                            <i class="far fa-circle nav-icon"> </i>
                                            <p><?= $subMenu['label'] ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= base_url($menu['url']) ?>" class="nav-link <?= $menu['active'] ? 'active' : '' ?>">
                                <i class="nav-icon <?= $menu['icon'] ?>"></i>
                                <p><?= $menu['label'] ?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">