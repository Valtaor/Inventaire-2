<?php
/**
 * Template Name: Inventaire Bijoux
 */

if (!defined('ABSPATH')) {
    exit;
}

$assetVersion = '3.1.0';

$uploadDir = wp_upload_dir();
$inventoryUploadsUrl = '';

if (empty($uploadDir['error'])) {
    $inventoryUploadsUrl = trailingslashit($uploadDir['baseurl'] . '/inventory');
} else {
    $inventoryUploadsUrl = trailingslashit(get_stylesheet_directory_uri()) . 'uploads/';
}

wp_enqueue_style(
    'inventory-style',
    get_stylesheet_directory_uri() . '/style-inventaire.css',
    [],
    $assetVersion
);

wp_enqueue_script(
    'inventory-script',
    get_stylesheet_directory_uri() . '/script-inventaire.js',
    ['jquery'],
    $assetVersion,
    true
);

wp_enqueue_script(
    'inventory-sales-script',
    get_stylesheet_directory_uri() . '/script-ventes.js',
    ['inventory-script'],
    $assetVersion,
    true
);

// Charger les scripts et styles pour la gestion des catégories
wp_enqueue_style(
    'inventory-categories-style',
    get_stylesheet_directory_uri() . '/style-categories.css',
    [],
    $assetVersion
);

wp_enqueue_script(
    'inventory-categories-script',
    get_stylesheet_directory_uri() . '/script-categories.js',
    ['inventory-script'],
    $assetVersion,
    true
);

wp_localize_script(
    'inventory-script',
    'inventorySettings',
    [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'uploadsUrl' => $inventoryUploadsUrl,
        'i18n' => [
            'emptyInventory' => __('Votre inventaire est vide.', 'uncode'),
            'emptySearch' => __('Aucun bijou dans l\'inventaire pour le moment.', 'uncode'),
            'toastAddSuccess' => __('Produit ajouté avec succès.', 'uncode'),
            'toastAddError' => __('Erreur lors de l\'ajout du produit.', 'uncode'),
            'toastDeleteSuccess' => __('Produit supprimé.', 'uncode'),
            'toastDeleteError' => __('Suppression impossible.', 'uncode'),
            'toastUpdateSuccess' => __('Valeur mise à jour.', 'uncode'),
            'toastUpdateError' => __('Mise à jour impossible.', 'uncode'),
            'toastEditSuccess' => __('Produit mis à jour.', 'uncode'),
            'toastEditError' => __('Erreur lors de la mise à jour du produit.', 'uncode'),
            'toggleIncomplete' => __('Marquer comme à compléter', 'uncode'),
            'toggleComplete' => __('Marquer comme complet', 'uncode'),
            'deleteConfirm' => __('Supprimer cet article ?', 'uncode'),
            'statusOutOfStock' => __('Rupture', 'uncode'),
            'statusInStock' => __('En stock', 'uncode'),
            'statusIncomplete' => __('À compléter', 'uncode'),
            'columnPhoto' => __('Photo', 'uncode'),
            'columnTitle' => __('Titre', 'uncode'),
            'columnCasier' => __('Casier', 'uncode'),
            'columnInfos' => __('Infos', 'uncode'),
            'columnQuantity' => __('Quantité', 'uncode'),
            'columnFollowUp' => __('Suivi', 'uncode'),
            'columnStatus' => __('Statut', 'uncode'),
            'columnActions' => __('Actions', 'uncode'),
            'labelPurchase' => __('Achat', 'uncode'),
            'labelSale' => __('Vente', 'uncode'),
            'labelDate' => __('Acheté le', 'uncode'),
            'filterAllCasiers' => __('Tous les casiers', 'uncode'),
            'filterAllStatus' => __('Tous les statuts', 'uncode'),
            'loadError' => __('Impossible de charger les produits.', 'uncode'),
            'markedIncomplete' => __('Objet marqué à compléter.', 'uncode'),
            'markedComplete' => __('Objet marqué complet.', 'uncode'),
            'submitLabel' => __('Ajouter à l\'inventaire', 'uncode'),
            'updateLabel' => __('Mettre à jour le produit', 'uncode'),
            'editLabel' => __('Modifier', 'uncode'),
        ],
    ]
);

get_header();
?>

<div class="inventory-page dashboard-v9">
    <?php if (!is_user_logged_in()) : ?>
        <section class="inventory-access-denied">
            <div class="inventory-card">
                <h2><?php esc_html_e('Accès restreint', 'uncode'); ?></h2>
                <p><?php esc_html_e('Vous devez être connecté pour consulter le tableau de bord inventaire.', 'uncode'); ?></p>
                <a class="inventory-button primary-button" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">
                    <?php esc_html_e('Se connecter', 'uncode'); ?>
                </a>
            </div>
        </section>
    <?php else : ?>
        <!-- Menu de navigation latéral -->
        <nav class="sidebar-nav" id="sidebarNav" aria-label="<?php esc_attr_e('Navigation principale', 'uncode'); ?>">
            <div class="sidebar-header">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="<?php esc_attr_e('Basculer le menu', 'uncode'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="#form-section" class="sidebar-link" data-section="form-section">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span><?php esc_html_e('Ajouter', 'uncode'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#statsCard" class="sidebar-link" data-section="statsCard">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        <span><?php esc_html_e('Statistiques', 'uncode'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#inventoryCard" class="sidebar-link active" data-section="inventoryCard">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                        </svg>
                        <span><?php esc_html_e('Inventaire', 'uncode'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#salesHistoryCard" class="sidebar-link" data-section="salesHistoryCard">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        <span><?php esc_html_e('Ventes', 'uncode'); ?></span>
                    </a>
                </li>
            </ul>
        </nav>

        <main class="inventory-dashboard" role="main">
            <header class="inventory-hero" aria-labelledby="inventory-overview-title">
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="<?php esc_attr_e('Basculer le thème', 'uncode'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                <p class="inventory-kicker"><?php esc_html_e('Tableau de bord', 'uncode'); ?></p>
                <h1 id="inventory-overview-title" class="inventory-title"><?php esc_html_e('Inventaire', 'uncode'); ?></h1>
                <p class="inventory-subtitle"><?php esc_html_e('Mon assistant intelligent', 'uncode'); ?></p>
            </header>

            <div class="inventory-container">
                <section class="form-section card" id="form-section" aria-labelledby="inventory-form-title">
                    <h2 class="card-title" id="inventory-form-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <?php esc_html_e('Ajouter un objet', 'uncode'); ?>
                    </h2>
                    <form id="inventory-form" class="inventory-form" enctype="multipart/form-data" method="post">
                        <input type="hidden" id="product-id" value="" />
                        <input type="hidden" id="product-existing-image" value="" />
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="product-image" class="form-label"><?php esc_html_e('Photo', 'uncode'); ?></label>
                                <div class="file-upload-wrapper" role="presentation">
                                    <input type="file" id="product-image" name="image" accept="image/*" />
                                    <div class="file-upload-text">
                                        <p><?php esc_html_e('Glissez-déposez une image ou', 'uncode'); ?><br><span><?php esc_html_e('cliquez pour parcourir', 'uncode'); ?></span></p>
                                    </div>
                                </div>
                                <div class="photo-preview" id="photoPreviewContainer">
                                    <img id="image-preview" class="image-preview is-empty" src="" alt="<?php esc_attr_e('Aperçu de l\'image sélectionnée', 'uncode'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-name" class="form-label"><?php esc_html_e('Titre de l\'objet', 'uncode'); ?></label>
                                <input type="text" id="product-name" name="nom" class="form-control" placeholder="<?php esc_attr_e('Bague art déco, broche vintage...', 'uncode'); ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="product-reference" class="form-label"><?php esc_html_e('Référence', 'uncode'); ?></label>
                                <input type="text" id="product-reference" name="reference" class="form-control" placeholder="REF-001" required />
                            </div>
                            <div class="form-group">
                                <label for="product-location" class="form-label"><?php esc_html_e('Casier / emplacement', 'uncode'); ?></label>
                                <input type="text" id="product-location" name="emplacement" class="form-control" placeholder="Ex. A1, B1" />
                            </div>
                            <div class="form-group">
                                <label for="product-stock" class="form-label"><?php esc_html_e('Quantité', 'uncode'); ?></label>
                                <input type="number" min="0" id="product-stock" name="stock" class="form-control" placeholder="1" value="1" />
                            </div>
                            <div class="form-group">
                                <label for="product-prix-achat" class="form-label"><?php esc_html_e('Prix d\'achat (€)', 'uncode'); ?></label>
                                <input type="number" step="0.01" min="0" id="product-prix-achat" name="prix_achat" class="form-control" placeholder="0.00" />
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label"><?php esc_html_e('Prix de vente par plateforme (€)', 'uncode'); ?></label>
                                <div class="price-grid">
                                    <div class="price-item">
                                        <label for="product-prix-vente-ebay" class="price-label">eBay</label>
                                        <input type="number" step="0.01" min="0" id="product-prix-vente-ebay" name="prix_vente_ebay" class="form-control" placeholder="0.00" />
                                    </div>
                                    <div class="price-item">
                                        <label for="product-prix-vente-lbc" class="price-label">LeBonCoin</label>
                                        <input type="number" step="0.01" min="0" id="product-prix-vente-lbc" name="prix_vente_lbc" class="form-control" placeholder="0.00" />
                                    </div>
                                    <div class="price-item">
                                        <label for="product-prix-vente-vinted" class="price-label">Vinted</label>
                                        <input type="number" step="0.01" min="0" id="product-prix-vente-vinted" name="prix_vente_vinted" class="form-control" placeholder="0.00" />
                                    </div>
                                    <div class="price-item">
                                        <label for="product-prix-vente-autre" class="price-label">Autre</label>
                                        <input type="number" step="0.01" min="0" id="product-prix-vente-autre" name="prix_vente_autre" class="form-control" placeholder="0.00" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-date" class="form-label"><?php esc_html_e('Date d\'achat', 'uncode'); ?></label>
                                <input type="date" id="product-date" name="date_achat" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="product-category" class="form-label"><?php esc_html_e('Catégorie (facultatif)', 'uncode'); ?></label>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <select id="product-category" name="category_id" class="form-control" style="flex: 1;">
                                        <option value=""><?php esc_html_e('Aucune catégorie', 'uncode'); ?></option>
                                    </select>
                                    <button type="button" class="inventory-button ghost-button" id="manage-categories-button" style="white-space: nowrap;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                            <path d="M12 5v14M5 12h14"/>
                                        </svg>
                                        <?php esc_html_e('Gérer', 'uncode'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-notes" class="form-label"><?php esc_html_e('Notes (facultatif)', 'uncode'); ?></label>
                                <textarea id="product-notes" name="notes" class="form-control" rows="3" placeholder="<?php esc_attr_e('État, provenance...', 'uncode'); ?>"></textarea>
                            </div>
                            <div class="form-group full-width follow-up-group">
                                <div class="follow-up-top">
                                    <label class="follow-up-text" for="product-incomplete" id="completeLaterLabel">
                                        <span class="follow-up-title"><?php esc_html_e('À renseigner plus tard', 'uncode'); ?></span>
                                        <span class="follow-up-description" id="completeLaterHelp"><?php esc_html_e('Activez cette option pour vous rappeler qu\'il manque des informations à compléter.', 'uncode'); ?></span>
                                    </label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="product-incomplete" name="a_completer" value="1" aria-labelledby="completeLaterLabel" aria-describedby="completeLaterHelp" />
                                        <span class="toggle-slider" aria-hidden="true"></span>
                                    </label>
                                </div>
                                <div class="follow-up-hint">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><?php esc_html_e('Les objets marqués affichent un badge « À compléter » dans votre inventaire.', 'uncode'); ?></span>
                                </div>
                                <label for="product-description" class="form-label"><?php esc_html_e('Informations manquantes', 'uncode'); ?></label>
                                <textarea id="product-description" name="description" class="form-control" rows="3" placeholder="<?php esc_attr_e('Certificat, expertise, à vérifier...', 'uncode'); ?>"></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="secondary" id="cancel-edit" hidden><?php esc_html_e('Annuler', 'uncode'); ?></button>
                            <button type="submit" class="primary" id="submit-button"><?php esc_html_e('Ajouter à l\'inventaire', 'uncode'); ?></button>
                        </div>
                    </form>
                </section>

                <section class="dashboard-grid">
                    <div class="card" id="statsCard">
                        <h2 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <?php esc_html_e('Statistiques', 'uncode'); ?>
                        </h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                    <?php esc_html_e('Articles en stock', 'uncode'); ?>
                                </h3>
                                <div class="value" id="stat-total-articles">0</div>
                            </div>
                            <div class="stat-card">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                    </svg>
                                    <?php esc_html_e('En rupture', 'uncode'); ?>
                                </h3>
                                <div class="value" id="stat-out-of-stock">0</div>
                            </div>
                            <div class="stat-card">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    <?php esc_html_e('Valeur de vente', 'uncode'); ?>
                                </h3>
                                <div class="value" id="stat-valeur-vente">0,00 €</div>
                            </div>
                            <div class="stat-card">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.071.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php esc_html_e('Marge', 'uncode'); ?>
                                </h3>
                                <div class="value" id="stat-marge-totale">0,00 €</div>
                            </div>
                        </div>
                        <span id="stat-valeur-achat" class="sr-only">0,00 €</span>
                        <div class="insights-grid">
                            <div class="insight-card">
                                <span class="insight-title"><?php esc_html_e('Stock faible', 'uncode'); ?></span>
                                <span class="insight-value" id="stat-low-stock">0</span>
                            </div>
                            <div class="insight-card">
                                <span class="insight-title"><?php esc_html_e('Fiches à compléter', 'uncode'); ?></span>
                                <span class="insight-value" id="stat-incomplete">0</span>
                            </div>
                            <div class="insight-card">
                                <span class="insight-title"><?php esc_html_e('Marge moyenne', 'uncode'); ?></span>
                                <span class="insight-value" id="stat-average-margin">0,00 €</span>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="inventoryCard">
                        <h2 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                            </svg>
                            <?php esc_html_e('Inventaire', 'uncode'); ?>
                        </h2>
                        <button type="button" class="mobile-filters-toggle" id="toggleFilters" aria-controls="filtersPanel" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                            </svg>
                            <?php esc_html_e('Filtres & actions', 'uncode'); ?>
                        </button>
                        <div class="quick-filters" id="quickFilters">
                            <button type="button" class="quick-filter-btn" data-status-filter="all" aria-pressed="true"><?php esc_html_e('Tout', 'uncode'); ?></button>
                            <button type="button" class="quick-filter-btn" data-status-filter="en-stock" aria-pressed="false"><?php esc_html_e('En stock', 'uncode'); ?></button>
                            <button type="button" class="quick-filter-btn" data-status-filter="rupture" aria-pressed="false"><?php esc_html_e('Rupture', 'uncode'); ?></button>
                            <button type="button" class="quick-filter-btn" data-status-filter="incomplet" aria-pressed="false"><?php esc_html_e('À compléter', 'uncode'); ?></button>
                        </div>
                        <div class="filters" id="filtersPanel">
                            <input type="search" id="inventory-search" class="search-input" placeholder="<?php esc_attr_e('Rechercher...', 'uncode'); ?>" aria-label="<?php esc_attr_e('Rechercher', 'uncode'); ?>" />
                            <select id="filterCasier"><option value="all"><?php esc_html_e('Tous les casiers', 'uncode'); ?></option></select>
                            <select id="filterCategory"><option value="all"><?php esc_html_e('Toutes les catégories', 'uncode'); ?></option></select>
                            <select id="filterStatus"><option value="all"><?php esc_html_e('Tous les statuts', 'uncode'); ?></option></select>
                            <button type="button" class="secondary" id="export-csv"><?php esc_html_e('Exporter CSV', 'uncode'); ?></button>
                        </div>
                        <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none;">
                            <div class="bulk-actions-info">
                                <span id="bulkSelectedCount">0</span> <?php esc_html_e('produit(s) sélectionné(s)', 'uncode'); ?>
                            </div>
                            <div class="bulk-actions-buttons">
                                <button type="button" class="inventory-button ghost-button" id="bulkEditButton">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    <?php esc_html_e('Modifier', 'uncode'); ?>
                                </button>
                                <button type="button" class="inventory-button ghost-button" id="bulkDeleteButton">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    <?php esc_html_e('Supprimer', 'uncode'); ?>
                                </button>
                                <button type="button" class="inventory-button ghost-button" id="bulkDeselectButton">
                                    <?php esc_html_e('Désélectionner', 'uncode'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="table-wrapper">
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-checkbox">
                                            <input type="checkbox" id="selectAllProducts" aria-label="<?php esc_attr_e('Sélectionner tous les produits', 'uncode'); ?>" />
                                        </th>
                                        <th scope="col"><?php esc_html_e('Photo', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Titre', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Infos', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Quantité', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Suivi', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Statut', 'uncode'); ?></th>
                                        <th scope="col"><?php esc_html_e('Actions', 'uncode'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-table-body"></tbody>
                            </table>
                            <div class="empty-state" id="empty-state">
                                <h3><?php esc_html_e('Votre inventaire est vide.', 'uncode'); ?></h3>
                                <p><?php esc_html_e('Commencez par ajouter votre premier objet !', 'uncode'); ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section Historique des ventes -->
                <section class="card" id="salesHistoryCard">
                    <div class="card-header-with-actions">
                        <h2 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <?php esc_html_e('Historique des ventes', 'uncode'); ?>
                        </h2>
                        <div class="card-actions">
                            <button type="button" class="secondary" id="export-sales-csv">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <?php esc_html_e('Exporter CSV', 'uncode'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="sales-filters">
                        <div class="form-group">
                            <label for="filterSalesPlatform" class="form-label"><?php esc_html_e('Plateforme', 'uncode'); ?></label>
                            <select id="filterSalesPlatform" class="form-control">
                                <option value=""><?php esc_html_e('Toutes les plateformes', 'uncode'); ?></option>
                                <option value="ebay">eBay</option>
                                <option value="lbc">LeBonCoin</option>
                                <option value="vinted">Vinted</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filterSalesDateStart" class="form-label"><?php esc_html_e('Du', 'uncode'); ?></label>
                            <input type="date" id="filterSalesDateStart" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="filterSalesDateEnd" class="form-label"><?php esc_html_e('Au', 'uncode'); ?></label>
                            <input type="date" id="filterSalesDateEnd" class="form-control" />
                        </div>
                    </div>

                    <!-- Statistiques des ventes -->
                    <div class="sales-stats-grid">
                        <div class="stat-card">
                            <h3><?php esc_html_e('Ventes totales', 'uncode'); ?></h3>
                            <div class="value" id="stat-sales-count">0</div>
                        </div>
                        <div class="stat-card">
                            <h3><?php esc_html_e('Articles vendus', 'uncode'); ?></h3>
                            <div class="value" id="stat-sales-quantity">0</div>
                        </div>
                        <div class="stat-card">
                            <h3><?php esc_html_e('Chiffre d\'affaires', 'uncode'); ?></h3>
                            <div class="value" id="stat-sales-revenue">0,00 €</div>
                        </div>
                        <div class="stat-card">
                            <h3><?php esc_html_e('Marge nette', 'uncode'); ?></h3>
                            <div class="value" id="stat-sales-margin">0,00 €</div>
                        </div>
                    </div>

                    <!-- Tableau des ventes -->
                    <div class="table-wrapper">
                        <table class="inventory-table sales-table">
                            <thead>
                                <tr>
                                    <th scope="col"><?php esc_html_e('Date', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Produit', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Qté', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Plateforme', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Prix vente', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Frais', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Marge', 'uncode'); ?></th>
                                    <th scope="col"><?php esc_html_e('Actions', 'uncode'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="sales-table-body"></tbody>
                        </table>
                        <div class="empty-state" id="empty-sales-state" style="display: none;">
                            <h3><?php esc_html_e('Aucune vente enregistrée.', 'uncode'); ?></h3>
                            <p><?php esc_html_e('Cliquez sur "Vendu" à côté d\'un produit pour enregistrer une vente !', 'uncode'); ?></p>
                        </div>
                    </div>
                </section>
            </div>

            <nav class="mobile-action-bar" id="mobileActionBar" aria-label="<?php esc_attr_e('Actions rapides', 'uncode'); ?>">
                <button type="button" id="mobileScrollInventory">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M3 9h18M3 13.5h18M3 18h18" />
                    </svg>
                    <?php esc_html_e('Liste', 'uncode'); ?>
                </button>
                <button type="button" class="primary-action" id="mobileAddItem">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <?php esc_html_e('Ajouter', 'uncode'); ?>
                </button>
                <button type="button" id="mobileScrollStats">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.071.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?php esc_html_e('Statistiques', 'uncode'); ?>
                </button>
            </nav>

            <!-- Modal de vente -->
            <div class="modal-overlay" id="sale-modal-overlay" style="display: none;">
                <div class="modal-dialog" role="dialog" aria-labelledby="sale-modal-title" aria-modal="true">
                    <div class="modal-header">
                        <h2 id="sale-modal-title"><?php esc_html_e('Enregistrer une vente', 'uncode'); ?></h2>
                        <button type="button" class="modal-close" id="sale-modal-close" aria-label="<?php esc_attr_e('Fermer', 'uncode'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="sale-form" class="modal-body">
                        <input type="hidden" id="sale-product-id" />

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e('Produit', 'uncode'); ?></label>
                            <div id="sale-product-info" class="product-info-display"></div>
                        </div>

                        <div class="form-group">
                            <label for="sale-quantite" class="form-label"><?php esc_html_e('Quantité vendue', 'uncode'); ?></label>
                            <input type="number" id="sale-quantite" name="quantite" class="form-control" min="1" value="1" required />
                            <small class="form-hint" id="sale-stock-hint"></small>
                        </div>

                        <div class="form-group">
                            <label for="sale-plateforme" class="form-label"><?php esc_html_e('Plateforme de vente', 'uncode'); ?></label>
                            <select id="sale-plateforme" name="plateforme" class="form-control" required>
                                <option value=""><?php esc_html_e('Sélectionner...', 'uncode'); ?></option>
                                <option value="ebay">eBay</option>
                                <option value="lbc">LeBonCoin</option>
                                <option value="vinted">Vinted</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sale-prix-vente" class="form-label"><?php esc_html_e('Prix de vente réel (€)', 'uncode'); ?></label>
                            <input type="number" step="0.01" min="0" id="sale-prix-vente" name="prix_vente" class="form-control" placeholder="0.00" required />
                            <small class="form-hint" id="sale-price-hint"></small>
                        </div>

                        <div class="form-group">
                            <label for="sale-frais" class="form-label"><?php esc_html_e('Frais (commission, port, etc.) (€)', 'uncode'); ?></label>
                            <input type="number" step="0.01" min="0" id="sale-frais" name="frais" class="form-control" placeholder="0.00" value="0" />
                        </div>

                        <div class="form-group">
                            <label for="sale-date-vente" class="form-label"><?php esc_html_e('Date de vente', 'uncode'); ?></label>
                            <input type="date" id="sale-date-vente" name="date_vente" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label for="sale-notes" class="form-label"><?php esc_html_e('Notes (facultatif)', 'uncode'); ?></label>
                            <textarea id="sale-notes" name="notes" class="form-control" rows="3" placeholder="<?php esc_attr_e('Informations complémentaires...', 'uncode'); ?>"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="secondary" id="sale-modal-cancel"><?php esc_html_e('Annuler', 'uncode'); ?></button>
                            <button type="submit" class="primary" id="sale-submit-button"><?php esc_html_e('Enregistrer la vente', 'uncode'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de modification en masse -->
            <div class="modal-overlay" id="bulk-edit-modal-overlay" style="display: none;">
                <div class="modal-dialog" role="dialog" aria-labelledby="bulk-edit-modal-title" aria-modal="true">
                    <div class="modal-header">
                        <h2 id="bulk-edit-modal-title"><?php esc_html_e('Modifier en masse', 'uncode'); ?></h2>
                        <button type="button" class="modal-close" id="bulk-edit-modal-close" aria-label="<?php esc_attr_e('Fermer', 'uncode'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="bulk-edit-form" class="modal-body">
                        <p class="bulk-edit-description"><?php esc_html_e('Les modifications seront appliquées uniquement aux champs renseignés.', 'uncode'); ?></p>

                        <div class="form-group">
                            <label for="bulk-category" class="form-label"><?php esc_html_e('Catégorie', 'uncode'); ?></label>
                            <select id="bulk-category" class="form-control">
                                <option value=""><?php esc_html_e('Ne pas modifier', 'uncode'); ?></option>
                                <option value="__none__"><?php esc_html_e('Aucune catégorie', 'uncode'); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="bulk-casier" class="form-label"><?php esc_html_e('Casier / emplacement', 'uncode'); ?></label>
                            <input type="text" id="bulk-casier" class="form-control" placeholder="<?php esc_attr_e('Ne pas modifier', 'uncode'); ?>" />
                        </div>

                        <div class="form-group">
                            <label for="bulk-incomplete" class="form-label"><?php esc_html_e('Marquer comme à compléter', 'uncode'); ?></label>
                            <select id="bulk-incomplete" class="form-control">
                                <option value=""><?php esc_html_e('Ne pas modifier', 'uncode'); ?></option>
                                <option value="1"><?php esc_html_e('Oui', 'uncode'); ?></option>
                                <option value="0"><?php esc_html_e('Non', 'uncode'); ?></option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="secondary" id="bulk-edit-modal-cancel"><?php esc_html_e('Annuler', 'uncode'); ?></button>
                            <button type="submit" class="primary" id="bulk-edit-submit-button"><?php esc_html_e('Appliquer les modifications', 'uncode'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de gestion des catégories -->
            <div class="modal-overlay" id="categories-modal-overlay" style="display: none;">
                <div class="modal-dialog modal-dialog-large" role="dialog" aria-labelledby="categories-modal-title" aria-modal="true">
                    <div class="modal-header">
                        <h2 id="categories-modal-title"><?php esc_html_e('Gérer les catégories et tags', 'uncode'); ?></h2>
                        <button type="button" class="modal-close" id="categories-modal-close" aria-label="<?php esc_attr_e('Fermer', 'uncode'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <?php include get_stylesheet_directory() . '/categories.php'; ?>
                    </div>
                </div>
            </div>

            <div class="inventory-toast-stack" aria-live="polite" aria-atomic="true"></div>
        </main>
    <?php endif; ?>
</div>

<?php get_footer(); ?>