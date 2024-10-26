<?php
/*
Plugin Name: AWTD Order Filter
Plugin URI: https://luizreimann.dev
Description: Plugin para filtrar e exportar pedidos do WooCommerce para XLSX.
Version: 1.0
Author: Luiz Reimann
Author URI: https://luizreimann.dev
License: GPL-3.0
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Inclui o autoload do Composer para PhpSpreadsheet
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'includes/export-orders.php';

// Enfileirar scripts e styles
function awtd_enqueue_assets() {
    wp_enqueue_style('awtd-styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
    wp_enqueue_script('awtd-scripts', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', array('jquery'), null, true);
    wp_localize_script('awtd-scripts', 'awtd_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'awtd_enqueue_assets');

// Registrar shortcode
function awtd_woocommerce_order_filter_shortcode() {
    ob_start(); ?>

<div id="awtd-order-filter">
    <form id="awtd-order-filter-form">
        <div class="awtd-column-form">
            <label for="order_type">Tipo de Pedido:</label>
            <select id="order_type" name="order_type">
                <option value="delivery">Delivery</option>
                <option value="pickup">Retirada</option>
            </select>
        </div>
        <div class="awtd-column-form">
            <label for="start_date">Data Inicial:</label>
            <input type="date" id="start_date" name="start_date">
        </div>
        <div class="awtd-column-form">
            <label for="end_date">Data Final:</label>
            <input type="date" id="end_date" name="end_date">
        </div>
        <div>
            <button type="button" id="filter_orders_btn" class="awtd-button">Filtrar Pedidos</button>
            <button type="button" id="export_xlsx_btn" class="awtd-button" disabled>Exportar XLSX</button>
        </div>
    </form>

    <div id="awtd-order-results">
        <table id="awtd-orders-table" border="1">
            <thead>
                <tr>
                    <th>ID do Pedido</th>
                    <th>Nome do Cliente</th>
                    <th>Data do Pedido</th>
                    <th>Tipo de Pedido</th>
                    <th>Endereço do Cliente</th>
                    <th>Telefone</th>
                    <th>Forma de pagamento</th>
                    <th>Data de Entrega/Retirada</th>
                    <th>Horário de Entrega/Retirada</th>
                    <th>Total do Pedido</th>
                    <th>Itens do Pedido</th>
                </tr>
            </thead>
            <tbody>
                <!-- Os resultados dos pedidos serão exibidos aqui -->
            </tbody>
        </table>
    </div>
</div>

    <?php
    return ob_get_clean();
}
add_shortcode('awtd_order_filter', 'awtd_woocommerce_order_filter_shortcode');