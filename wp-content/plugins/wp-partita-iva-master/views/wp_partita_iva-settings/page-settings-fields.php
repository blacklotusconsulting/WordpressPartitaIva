<?php
/*
 * Basic Section
 */
?>

<?php if ('wp_partita_iva_field-cf' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[basic][field-cf]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[basic][field-cf]'); ?>">
        <option value="1" <?php selected($settings['basic']['field-cf'], "1"); ?>>Abilitato</option>
        <option value="0" <?php selected($settings['basic']['field-cf'], "0"); ?>>Non abilitato</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-pi' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[basic][field-pi]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[basic][field-pi]'); ?>">
        <option value="1" <?php selected($settings['basic']['field-pi'], "1"); ?>>Abilitato</option>
        <option value="0" <?php selected($settings['basic']['field-pi'], "0"); ?>>Non abilitato</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-nin' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[basic][field-nin]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[basic][field-nin]'); ?>">
        <option value="1" <?php selected($settings['basic']['field-nin'], "1"); ?>>Abilitato</option>
        <option value="0" <?php selected($settings['basic']['field-nin'], "0"); ?>>Non abilitato</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-pec' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[basic][field-pec]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[basic][field-pec]'); ?>">
        <option value="1" <?php selected($settings['basic']['field-pec'], "1"); ?>>Abilitato</option>
        <option value="0" <?php selected($settings['basic']['field-pec'], "0"); ?>>Non abilitato</option>
    </select>

<?php endif; ?>
<?php
/*
 * Advanced Section
 */
?>

<?php if ('wp_partita_iva_field-obb-cf' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-cf]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-cf]'); ?>">
        <option value="1" <?php selected($settings['advanced']['field-obb-cf'], "1"); ?>>Obbligatorio</option>
        <option value="0" <?php selected($settings['advanced']['field-obb-cf'], "0"); ?>>Non Obbligatorio</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-obb-pi' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-pi]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-pi]'); ?>">
        <option value="1" <?php selected($settings['advanced']['field-obb-pi'], "1"); ?>>Obbligatorio</option>
        <option value="0" <?php selected($settings['advanced']['field-obb-pi'], "0"); ?>>Non Obbligatorio</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-obb-nin' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-nin]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-nin]'); ?>">
        <option value="1" <?php selected($settings['advanced']['field-obb-nin'], "1"); ?>>Obbligatorio</option>
        <option value="0" <?php selected($settings['advanced']['field-obb-nin'], "0"); ?>>Non Obbligatorio</option>
    </select>

<?php endif; ?>

<?php if ('wp_partita_iva_field-obb-pec' == $field['label_for']) : ?>

    <select id="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-pec]'); ?>"
            name="<?php esc_attr_e('wp_partita_iva_settings[advanced][field-obb-pec]'); ?>">
        <option value="1" <?php selected($settings['advanced']['field-obb-pec'], "1"); ?>>Obbligatorio</option>
        <option value="0" <?php selected($settings['advanced']['field-obb-pec'], "0"); ?>>Non Obbligatorio</option>
    </select>

<?php endif; ?>
