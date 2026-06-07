<?php /** @var \App\Common\Bmvc\BaseView $this */ ?>
<nav class="bv-nav">
    <div class="bv-nav-inner">

        <!-- Brand -->
        <a href="<?php echo $base; ?>" class="bv-brand">⚡ <span>up</span>MVC SaaS</a>

        <!-- Primary links -->
        <ul class="bv-links">

            <li><a href="<?php echo $base; ?>" class="<?php echo $this->isActive($base); ?>">Home</a></li>

            <?php if ($role === 'platform_admin'): ?>
            <li><a href="<?php echo $base; ?>/platform-admin" class="bv-hi <?php echo $this->isActive($base . '/platform-admin'); ?>">Platform Admin</a></li>
            <?php endif; ?>

            <?php if (in_array($role, ['tenant_owner','tenant_user'], true)): ?>
            <li><a href="<?php echo $base; ?>/app" class="bv-hi <?php echo $this->isActive($base . '/app'); ?>">My App</a></li>
            <?php endif; ?>

        </ul>

        <!-- Right side -->
        <div class="bv-right">
            <?php if ($logged): ?>
                <?php if ($roleBadge): ?>
                <span class="bv-role-badge"
                      style="background:<?php echo $roleBadge['bg']; ?>;color:<?php echo $roleBadge['fg']; ?>;">
                    <?php echo $roleBadge['label']; ?>
                </span>
                <?php endif; ?>
                <span class="bv-uname"><?php echo $uname; ?></span>
                <a href="<?php echo $base; ?>/logout" class="bv-btn">Sign out</a>
            <?php else: ?>
                <a href="<?php echo $base; ?>/auth" class="bv-btn bv-btn-primary">Sign in</a>
            <?php endif; ?>
        </div>

    </div>
</nav>
