<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Função para filtrar pedidos via AJAX
function awtd_filter_orders() {
    $order_type = sanitize_text_field($_POST['order_type']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);

    $meta_query = array(
        array(
            'key' => 'lpac_dps_order_type',
            'value' => $order_type,
            'compare' => '='
        ),
    );

    if ($order_type == 'pickup') {
        $meta_query[] = array(
            'key'     => 'lpac_dps_pickup_date',
            'value'   => array($start_date, $end_date),
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        );
    } else {
        $meta_query[] = array(
            'key'     => 'lpac_dps_delivery_date',
            'value'   => array($start_date, $end_date),
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        );
    }

    $args = array(
        'status' => array('completed', 'processing', 'on-hold'),
        'limit' => -1,
        'meta_query' => $meta_query,
        'orderby' => 'date_created',
        'order' => 'DESC',
    );

    $orders = wc_get_orders($args);

    if (!empty($orders)) {
        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $order_date = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d') : 'N/A';
            $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            $address = $order->get_billing_address_1() . ', ' . $order->get_meta('_billing_number') . '. ' . $order->get_billing_city() . '. CEP: ' . $order->get_billing_postcode();
            $phone = $order->get_billing_phone();
            $payment_method = $order->get_payment_method_title();
            $delivery_pickup_date = $order->get_meta($order_type == 'pickup' ? 'lpac_dps_pickup_date' : 'lpac_dps_delivery_date');
            $delivery_pickup_time = $order->get_meta($order_type == 'pickup' ? 'lpac_dps_pickup_time' : 'lpac_dps_delivery_time');
            $order_total = $order->get_total();

            $items = $order->get_items();
            $items_output = '';
            foreach ($items as $item) {
                $product_name = $item->get_name();
                $product_quantity = $item->get_quantity();
                $product_total = wc_price($item->get_total());
                $items_output .= "{$product_name} (x{$product_quantity}) - {$product_total}<br>";
            }

            echo "<tr>
                    <td>{$order_id}</td>
                    <td>{$customer_name}</td>
                    <td>{$order_date}</td>
                    <td>{$order_type}</td>
                    <td>{$address}</td>
                    <td>{$phone}</td>
                    <td>{$payment_method}</td>
                    <td>{$delivery_pickup_date}</td>
                    <td>{$delivery_pickup_time}</td>
                    <td>{$order_total}</td>
                    <td>{$items_output}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='11'>Nenhum pedido encontrado para os critérios especificados!</td></tr>";
    }

    wp_die();
}
add_action('wp_ajax_awtd_filter_orders', 'awtd_filter_orders');
add_action('wp_ajax_nopriv_awtd_filter_orders', 'awtd_filter_orders');

// Função para exportar pedidos para XLSX via AJAX
function awtd_export_orders_to_xlsx() {
    // Recupera as variáveis usando $_GET
    $order_type = sanitize_text_field($_GET['order_type']);
    $start_date = sanitize_text_field($_GET['start_date']);
    $end_date = sanitize_text_field($_GET['end_date']);

    $meta_query = array(
        array(
            'key' => 'lpac_dps_order_type',
            'value' => $order_type,
            'compare' => '='
        ),
    );

    if ($order_type == 'pickup') {
        $meta_query[] = array(
            'key'     => 'lpac_dps_pickup_date',
            'value'   => array($start_date, $end_date),
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        );
    } else {
        $meta_query[] = array(
            'key'     => 'lpac_dps_delivery_date',
            'value'   => array($start_date, $end_date),
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        );
    }

    $args = array(
        'status' => array('completed', 'processing', 'on-hold'),
        'limit' => -1,
        'meta_query' => $meta_query,
        'orderby' => 'date_created',
        'order' => 'DESC',
    );

    $orders = wc_get_orders($args);

    // Cria uma nova planilha
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Define os cabeçalhos básicos
    $headers = ['ID do Pedido', 'Nome do Cliente', 'Data do Pedido', 'Tipo de Pedido', 'Endereço do Cliente', 'Telefone', 'Forma de pagamento', 'Data de Entrega/Retirada', 'Horário de Entrega/Retirada', 'Total do Pedido'];

    // Determina o maior número de itens em um único pedido para criar as colunas dinamicamente
    $max_items = 0;
    foreach ($orders as $order) {
        $item_count = count($order->get_items());
        if ($item_count > $max_items) {
            $max_items = $item_count;
        }
    }

    // Adiciona colunas para cada item até o máximo encontrado
    for ($i = 1; $i <= $max_items; $i++) {
        $headers[] = "Item {$i}";
        $headers[] = "Quantidade Item {$i}";
        $headers[] = "Preço Item {$i}";
    }

    $sheet->fromArray($headers, NULL, 'A1');

    $row = 2; // Começa na segunda linha para os dados

    // Verifica se há pedidos
    if (!empty($orders)) {
        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $order_date = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d') : 'N/A';
            $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            $address = $order->get_billing_address_1() . ', ' . $order->get_meta('_billing_number') . '. ' . $order->get_billing_city() . '. CEP: ' . $order->get_billing_postcode();
            $phone = $order->get_billing_phone();
            $payment_method = $order->get_payment_method_title();
            $delivery_pickup_date = $order->get_meta($order_type == 'pickup' ? 'lpac_dps_pickup_date' : 'lpac_dps_delivery_date');
            $delivery_pickup_time = $order->get_meta($order_type == 'pickup' ? 'lpac_dps_pickup_time' : 'lpac_dps_delivery_time');
            $order_total = $order->get_total();

            // Preenche as colunas básicas do pedido
            $data = [
                $order_id,
                $customer_name,
                $order_date,
                $order_type,
                $address,
                $phone,
                $payment_method,
                $delivery_pickup_date,
                $delivery_pickup_time,
                number_format((float)$order_total, 2, '.', '') // Formata o total do pedido como número com duas casas decimais
            ];

            // Obtenha os itens do pedido e preencha as colunas dinamicamente
            $items = $order->get_items();
            $item_index = 0;

            foreach ($items as $item) {
                $product_name = $item->get_name();
                $product_quantity = $item->get_quantity();
                $product_total = $item->get_total();

                $data[] = $product_name;
                $data[] = $product_quantity;
                $data[] = number_format((float)$product_total, 2, '.', ''); // Formata o valor como número com duas casas decimais

                $item_index++;
            }

            // Preenche células vazias para os itens restantes (caso o pedido não tenha todos os itens possíveis)
            for ($i = $item_index; $i < $max_items; $i++) {
                $data[] = '';
                $data[] = '';
                $data[] = '';
            }

            // Insere a linha na planilha
            $sheet->fromArray($data, NULL, 'A' . $row);
            $row++;
        }
    } else {
        // Se não houver pedidos, insere uma mensagem informativa
        $sheet->setCellValue('A2', 'Nenhum pedido encontrado para os critérios especificados.');
    }

    // Gera o arquivo XLSX
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="pedidos.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

// Registra as ações AJAX para exportar os pedidos
add_action('wp_ajax_awtd_export_orders_to_xlsx', 'awtd_export_orders_to_xlsx');
add_action('wp_ajax_nopriv_awtd_export_orders_to_xlsx', 'awtd_export_orders_to_xlsx');