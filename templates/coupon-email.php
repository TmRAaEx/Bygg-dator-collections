<?php
/** @var string $coupon_code */
/** @var WP_User $user */
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title><?php _e('Du har fått en rabattkod!', 'pc-builds'); ?></title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h1><?php _e('Tack för ditt köp!', 'pc-builds'); ?></h1>
    <p><?php echo esc_html($user->display_name); ?>,</p>
    <p>
        <?php printf(
            __('Här är din 10%% rabattkod: <strong>%s</strong> (giltig för ett köp)', 'pc-builds'),
            esc_html($coupon_code)
        ); ?>
    </p>
    <p><?php _e('Vi hoppas du får glädje av din nya dator!', 'pc-builds'); ?></p>
</body>
</html>
