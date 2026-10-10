<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Staff sign in · Tamaraw POS</title><link rel="icon" type="image/svg+xml" href="<?= base_url('assets/favicon.svg') ?>"><link rel="stylesheet" href="<?= base_url('assets/app.css') ?>"></head>
<body class="login-body">
    <section class="login-story">
        <a class="brand" href="<?= site_url('login') ?>"><span class="brand-mark">T<span>✦</span></span><span>TAMARAW<small>CAMPUS STORE</small></span></a>
        <div class="login-story-content"><span class="eyebrow gold">THE GREEN & GOLD STANDARD</span><h1>A little campus spirit.<br>A better store experience.</h1><p>One workspace for your products, your people,<br>and every sale in between.</p><div class="login-art"><div class="art-orbit"></div><img src="<?= base_url('assets/products/sample-shirt.svg') ?>" alt="Green campus shirt"><img src="<?= base_url('assets/products/sample-tumbler.svg') ?>" alt="Gold campus tumbler"><span class="art-badge"><?= icon('leaf') ?> Made for the Tamaraw community</span></div></div>
        <small class="login-note">An academic project inspired by FEU Tech. Not an official university service.</small>
    </section>
    <section class="login-panel">
        <div class="login-form-wrap"><span class="eyebrow">STAFF PORTAL</span><h2>Welcome back.</h2><p class="muted">Sign in to keep your campus store moving.</p>
            <?= $this->include('partials/alerts') ?>
            <form action="<?= site_url('login') ?>" method="post" class="stack-form">
                <?= csrf_field() ?>
                <label for="username">Username</label><input id="username" name="username" autocomplete="username" required maxlength="50" value="<?= form_value('username') ?>" placeholder="Enter your username" autofocus>
                <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required maxlength="72" placeholder="Enter your password">
                <button class="button primary wide" type="submit">Sign in to workspace <?= icon('arrow') ?></button>
            </form>
            <div class="secure-note"><?= icon('lock') ?> Secure access for authorized store staff</div>
            <?php if (ENVIRONMENT === 'development' && env('POS_DEMO', false)): ?><div class="demo-note"><strong>Explore the demo</strong><span>Username: <code>admin</code></span><span>Password: <code>TamarawDemo!2026</code></span></div><?php endif ?>
        </div>
    </section>
</body>
</html>

