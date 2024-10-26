jQuery(document).ready(function($) {
    // Função para buscar pedidos via AJAX
    $('#filter_orders_btn').on('click', function() {
        var orderType = $('#order_type').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $('#filter_orders_btn').prop('disabled', true).text('Buscando...');

        $.ajax({
            url: awtd_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'awtd_filter_orders',
                order_type: orderType,
                start_date: startDate,
                end_date: endDate,
            },
            success: function(response) {
                $('#awtd-orders-table tbody').html(response);
                $('#filter_orders_btn').prop('disabled', false).text('Filtrar Pedidos');
                $('#export_xlsx_btn').prop('disabled', false);
            },
            error: function() {
                alert('Erro ao buscar os pedidos. Por favor, tente novamente.');
                $('#filter_orders_btn').prop('disabled', false).text('Filtrar Pedidos');
            }
        });
    });

    // Função para exportar XLSX
    $('#export_xlsx_btn').on('click', function() {
        var orderType = $('#order_type').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        var exportUrl = awtd_ajax.ajax_url + '?action=awtd_export_orders_to_xlsx' +
                        '&order_type=' + encodeURIComponent(orderType) +
                        '&start_date=' + encodeURIComponent(startDate) +
                        '&end_date=' + encodeURIComponent(endDate);

        $('#export_xlsx_btn').prop('disabled', true).text('Exportando...');

        var link = document.createElement('a');
        link.href = exportUrl;
        link.setAttribute('download', 'pedidos.xlsx');
        document.body.appendChild(link);
        link.click();

        setTimeout(function() {
            $('#export_xlsx_btn').prop('disabled', false).text('Exportar XLSX');
        }, 3000);
    });
});