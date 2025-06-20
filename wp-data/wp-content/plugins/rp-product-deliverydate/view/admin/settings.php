<h1 class="wp-heading-inline rp_main_title"><?php echo esc_html__(self::$plugin_title, self::$textdomain) ?></h1>
<?php if($settingSaves===true): ?>
    <div id="message" class="updated notice notice-success is-dismissible">
        <p><?php _e( 'Settings have been updated!', self::$textdomain); ?></p>
    </div>
<?php endif; ?>
<div class="rpesp-wrapper">    
    <div class="nav-tab-wrapper rpesp-tab-wrapper">
        <a href="#general-setting" class="nav-tab nav-tab-active"><?php echo esc_html__('General Settings', self::$textdomain) ?></a>
        <a href="#display-setting" class="nav-tab "><?php echo esc_html__('Display Settings', self::$textdomain) ?></a>
        <a href="#delivery-text-setting" class="nav-tab "><?php echo esc_html__('Delivery Text Settings', self::$textdomain) ?></a>
        <a href="#dayoff-settings" class="nav-tab "><?php echo esc_html__('Day Off Settings', self::$textdomain) ?></a>
        <a href="#design-settings" class="nav-tab "><?php echo esc_html__('Design Settings', self::$textdomain) ?></a>
        <a href="#shipping-delivery-settings" class="nav-tab "><?php echo esc_html__('Shipping Delivery Time', self::$textdomain) ?></a>
    </div>
    <form method="post" class="rpesp-form" action="" name="<?php echo self::$textdomain; ?>">
        <input type="hidden" name="<?php echo self::$textdomain; ?>" value="1"/>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/general.php'; ?>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/display.php'; ?>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/delivery-text.php'; ?>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/day-off.php'; ?>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/design-setting.php'; ?>
        <?php include_once self::$plugin_dir . 'view/admin/global/tabs/shipping-delivery-time.php'; ?>
    </form>
    <?php include_once self::$plugin_dir . 'view/admin/global/dayoff-field-row.php'; ?>
</div>





