<?php
/**
 * Template Name: Catégories Inventaire
 */

if (!defined('ABSPATH')) {
    exit;
}

$assetVersion = '1.0.0';

// Charger les styles
wp_enqueue_style(
    'inventory-categories-style',
    get_stylesheet_directory_uri() . '/style-categories.css',
    [],
    $assetVersion
);

// Charger les scripts
wp_enqueue_script(
    'inventory-categories-script',
    get_stylesheet_directory_uri() . '/script-categories.js',
    ['jquery'],
    $assetVersion,
    true
);

// Localiser le script avec les paramètres nécessaires
wp_localize_script(
    'inventory-categories-script',
    'inventorySettings',
    [
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ]
);

get_header();
?>

<div class="inventory-page" data-theme="light">
    <?php if (!is_user_logged_in()) : ?>
        <section class="inventory-access-denied">
            <div class="inventory-card">
                <h2><?php esc_html_e('Accès restreint', 'uncode'); ?></h2>
                <p><?php esc_html_e('Vous devez être connecté pour gérer les catégories.', 'uncode'); ?></p>
                <a class="inventory-button primary-button" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">
                    <?php esc_html_e('Se connecter', 'uncode'); ?>
                </a>
            </div>
        </section>
    <?php else : ?>
        <main class="inventory-dashboard" role="main">
            <header class="inventory-hero" aria-labelledby="categories-title">
                <p class="inventory-kicker"><?php esc_html_e('Gestion', 'uncode'); ?></p>
                <h1 id="categories-title" class="inventory-title"><?php esc_html_e('Catégories & Tags', 'uncode'); ?></h1>
                <p class="inventory-subtitle"><?php esc_html_e('Organisez votre inventaire', 'uncode'); ?></p>
            </header>

            <div class="inventory-container">
                <?php include get_stylesheet_directory() . '/categories.php'; ?>
            </div>

            <div class="inventory-toast-stack" aria-live="polite" aria-atomic="true"></div>
        </main>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
