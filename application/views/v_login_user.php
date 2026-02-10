<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login | Backstore MR DIY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
    <style>
        .login-logo img {
            width: 100px;
            height: auto;
        }

        .login-box {
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #FFC107;
            border: none;
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a800;
            color: #000;
        }
    </style>
</head>

<body class="hold-transition login-page" style="background:rgb(249, 249, 244);">

    <div class="login-box p-3 bg-warning">
        <div class="login-logo">
            <img src="<?= base_url('assets/img/logomrdiy.png') ?>" alt="MR DIY Logo">
            <h3><b>Backstore</b> MR DIY</h3>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Masuk untuk mengakses sistem</p>

                <?php
                if ($this->session->flashdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h6><i class="icon fas fa-ban"></i>';
                    echo $this->session->flashdata('error');
                    echo '</h6></div>';
                }

                if ($this->session->flashdata('pesan')) {
                    echo '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h6><i class="icon fas fa-check"></i>';
                    echo $this->session->flashdata('pesan');
                    echo '</h6></div>';
                }

                echo form_open('auth/login_user')
                ?>

                <form action="<?= site_url('auth/login_user') ?>" method="post">
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>

                    </div>
                    <?= form_error('username', '<small class="text-danger pl-3">', '</small>'); ?>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>
                    <?= form_error('password', '<small class="text-danger pl-3">', '</small>'); ?>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" id="btn-login" class="btn btn-warning btn-block">
                                Masuk
                            </button>
                        </div>
                    </div>
                </form>
                <?php
                echo form_close();
                ?>

            </div>
        </div>
    </div>

    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
</body>

</html>
<script>
    document.getElementById('btn-login').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
        btn.form.submit(); // submit form secara eksplisit
    });
</script>

<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000)
</script>