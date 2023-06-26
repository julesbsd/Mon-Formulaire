<?php


/*
Plugin Name: Mon formulaire
Plugin URI: http://wordpress.org/plugins/mon-formulaire/
Description:  Ce plugin ajoute un formulaire à mon site WordPress. Via le panneau d'administration de wordpress "parametre->Mon formulaure, vous pouvez ajouter des options au formulaire.
Author: BOISMOND Jules
Version: 1.2
Author URI: http://boismondjules.fr
*/

function mon_formulaire()
{
    // Récupére les options
    $options = get_option('mon_formulaire_options');
    ob_start();
?>

    <div class="custom-dropdown">
        <select id="custom-select">
            <option value="" disabled selected>Choisissez un motif de consultation</option>
            <?php
            foreach ($options as $key => $option) {
                $nom = isset($option['nom']) ? esc_attr($option['nom']) : '';
                $url = isset($option['url']) ? esc_attr($option['url']) : '';
                echo '<option value="' . $url . '">' . $nom . '</option>';
            }
            ?>
        </select>
        <div class="divider"></div>
        <input type="date" id="appointment-date" />
        <button id="custom-search-button" class="search-icon-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('mon_formulaire', 'mon_formulaire');




function mon_formulaire_styles()
{
    // Enregistre le style du formulaire
    wp_enqueue_style('formulaire_css', plugins_url('formulaire.css', __FILE__));
    //enregistre Style FontAwesome
    wp_enqueue_style('font_awesome', plugins_url('all.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'mon_formulaire_styles');


function mon_formulaire_scripts()
{
    // Enregistre le script du formulaire
    wp_enqueue_script('formulaire_js', plugin_dir_url(__FILE__) . 'formulaire.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'mon_formulaire_scripts');

function mon_formulaire_menu()
{
    add_options_page(
        'Options de mon formulaire',
        'Mon Formulaire',
        'manage_options',
        'mon-formulaire',
        'mon_formulaire_options'
    );
}
add_action('admin_menu', 'mon_formulaire_menu');

function mon_formulaire_options()
{
    // Récupére les options
    $options = get_option('mon_formulaire_options', array());

    // Vérifie si les options sont un tableau, sinon initialisation
    if (!is_array($options)) {
        $options = array();
    }
    if (isset($_POST['add_option'])) {
        $new_key = count($options) + 1;
        $options[$new_key] = array(
            'nom' => '',
            'url' => '',
        );

        // Mettre à jour les options avec la nouvelle option ajoutée
        update_option('mon_formulaire_options', $options);
    }

    // Vérifiez si le formulaire a été soumis et mettez à jour les options si nécessaire
    if (isset($_POST['submit'])) {
        $new_options = array();

        // Parcourez chaque option et vérifiez si elle est définie
        foreach ($_POST['mon_formulaire_options'] as $key => $value) {
            $nom = isset($value['nom']) ? sanitize_text_field($value['nom']) : '';
            $url = isset($value['url']) ? sanitize_text_field($value['url']) : '';

            $new_options[$key] = array(
                'nom' => $nom,
                'url' => $url,
            );
        }

        // Mets à jour les options
        update_option('mon_formulaire_options', $new_options);

        // Affiche un message de succès
    ?>
        <div class="notice notice-success is-dismissible">
            <p>Les options ont été mises à jour avec succès.</p>
        </div>
    <?php
        // Vérifie si le bouton de réinitialisation a été cliqué
    } elseif (isset($_POST['reset'])) {
        // Réinitialise les options
        update_option('mon_formulaire_options', array());

        // Affiche un message de succès
    ?>
        <div class="notice notice-success is-dismissible">
            <p>Les options ont été réinitialisées avec succès.</p>
        </div>
    <?php
    }
    // Récupérez les options actuelles de l'input select
    $options = get_option('mon_formulaire_options', array());

    // Vérifie si un champ a été supprimé et mettez à jour les options
    if (isset($_POST['delete_option'])) {
        $deleted_option = $_POST['delete_option'];
        if (isset($options[$deleted_option])) {
            unset($options[$deleted_option]);
            update_option('mon_formulaire_options', $options);
        }
    }


    // Affiche le formulaire d'administration
    ?>
    <div class="wrap">
        <h1>Options du formulaire</h1>
        <form method="post" action="">
            <table class="form-table">
                <?php
                foreach ($options as $key => $option) {
                    $nom = isset($option['nom']) ? esc_attr($option['nom']) : '';
                    $url = isset($option['url']) ? esc_attr($option['url']) : '';
                ?>
                    <tr>
                        <th scope="row"><label for="option_<?php echo $key; ?>">Option <?php echo $key; ?></label></th>
                        <td>
                            <span>Nom :</span><input type="text" id="option_<?php echo $key; ?>" name="mon_formulaire_options[<?php echo $key; ?>][nom]" value="<?php echo $nom; ?>" placeholder="Nom">
                            <span>URL :</span> <input type="text" id="option_<?php echo $key; ?>_url" name="mon_formulaire_options[<?php echo $key; ?>][url]" value="<?php echo $url; ?>" placeholder="URL">
                            <button type="button" class="button button-secondary delete-option" data-option-id="<?php echo $key; ?>">Supprimer</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
            <p class="submit">
                <input type="submit" name="add_option" id="add_option" class="button" value="Ajouter une option">
                <input type="submit" name="reset" id="reset" class="button" value="Réinitialiser les options">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Enregistrer les options">
            </p>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.delete-option').on('click', function() {
                var optionId = $(this).data('option-id');
                $('#option_' + optionId).closest('tr').remove();
                // location.reload();
            });
        });
    </script>
<?php
}




function mon_formulaire_section_text()
{
    echo '<p>Entrez ici les liens pour le formulaire :</p>';
}

function mon_formulaire_setting_input($args)
{
    // Récupérez les options actuelles de l'input select
    $options = get_option('mon_formulaire_options');
    $value = isset($options[$args['id']]) ? esc_attr($options[$args['id']]) : '';
    echo "<input id='{$args['id']}' name='mon_formulaire_options[{$args['id']}]' size='40' type='text' value='{$value}' />";
}




function mon_formulaire_validate_options($input)
{
    return $input;
}

function mon_formulaire_page()
{
?>
    <div class="wrap">
        <h1>Options de mon formulaire</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mon_formulaire_options');
            do_settings_sections('mon-formulaire');
            submit_button();
            ?>
        </form>
    </div>
<?php
}
