<?php
class Msrp_calculation {
    private $wpdb;
    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        //$this->ProcessData();
    }
    function ProcessData($products, $calculation_results) {

        // Fetch daily_linear_meter for each production style
        $results = $this->wpdb->get_results("SELECT * FROM production_style_variables", ARRAY_A);
        $daily_linear_meters = [];
        foreach ($results as $row) {
            $daily_linear_meters[$row['production_style']] = $row['daily_linear_meter'];
        }

        // Fetch tax, shipping variables, and production cost per day from the costs table
        $query = "SELECT sales_tax_br, shipping_cost, trans_mexico_import_tax, trans_mexico_sales_tax, 
                inter_mexico_import_tax, inter_mexico_sales_tax, brlmxn_exchange_rate, brlusd_exchange_rate, 
                production_cost_per_day 
                FROM costs";
        $result = $this->wpdb->get_results($query, ARRAY_A);
        

        $sales_tax_br = $result[0]['sales_tax_br'];
        $shipping_cost = $result[0]['shipping_cost'];
        $trans_Mexico_Import_Tax = $result[0]['trans_mexico_import_tax'];
        $trans_Mexico_Sales_Tax = $result[0]['trans_mexico_sales_tax'];
        $inter_Mexico_Import_Tax = $result[0]['inter_mexico_import_tax'];
        $inter_Mexico_Sales_Tax = $result[0]['inter_mexico_sales_tax'];
        $BRLMXN_exchange_rate = $result[0]['brlmxn_exchange_rate'];
        $BRLUSD_exchange_rate = $result[0]['brlusd_exchange_rate'];
        $production_cost_per_day = $result[0]['production_cost_per_day'];
        $production_style = strtolower($products->production_style); 

        //Calculate Perimeter
        $perimeter = $calculation_results['rubber_straps_calculation'] + $calculation_results['step_profile_calculation'];
 
       
        // Calculating QTY per Day by dividing daily linear meter with perimeter for a single product
        $qty_per_day = $daily_linear_meters[$production_style] / $perimeter;


        // Calculating Cost of production per M which is the value from fixed value / qty per day
        $cost_prod_per_m = $production_cost_per_day / $qty_per_day;

        // Calculate BR MSRP
        $cost_total_prod = $cost_prod_per_m + $calculation_results['cost_final'];
        $markup_profit = $cost_total_prod * 1.0; // 100% markup

        $custom_value = $products->custom;
        $markup_custom = $custom_value > 0 ? $cost_total_prod * 0.2 : 0; // 20% markup if custom value is greater than 0

        $total_markup = $markup_profit + $markup_custom;

        // Define pre-tax subtotal and Tax in BR
        $pre_tax_subtotal = $total_markup + $cost_total_prod;
        $tax_br = $pre_tax_subtotal * $sales_tax_br; // Assuming $sales_tax_br is set

        // Define BR MSRP = pre tax total + BR taxes
        $initial_MSRP = $pre_tax_subtotal + $tax_br;
        $rounded_MSRP = ceil($initial_MSRP / 5) * 5; // Round up to the nearest $5 in favor

        // Calculate export invoice price (BR MSRP converted to MXN)
        $msrp_mxn = $rounded_MSRP * $BRLMXN_exchange_rate; 
        $export_invoice_price = ceil($msrp_mxn / 5) * 5; // Round up to the nearest $5

        // Calculate Shipping Fee
        $shipping_fee = $total_weight * $shipping_cost; 

        // Start calculating Trans MX MSRP
        // Add Shipping fee to BR MSRP
        $subtotal = $rounded_MSRP + $shipping_fee;

        // Calculate and store import tax
        $import_tax = $subtotal * $trans_Mexico_Import_Tax; 

        // Calculate and store sales tax
        $sales_tax = $import_tax * $trans_Mexico_Sales_Tax; 

        // Store the total of import and sales taxes
        $total_trans_mx_taxes = $import_tax + $sales_tax;

        // Calculate Mexican MSRP in BRL, including the subtotal and taxes
        $msrp_brl = $total_trans_mx_taxes + $subtotal;

        // Round up to the nearest $5 in favor
        $trans_mx_MSRP_BRL = ceil($msrp_brl / 5) * 5;

        // Convert BRL to MXN using BRLMXN exchange rate;
        $msrp_mxn = $trans_mx_MSRP_BRL * $BRLMXN_exchange_rate; // Assuming $BRLMXN_exchange_rate is set

        // Round up to the nearest $5 in favor
        $trans_mx_MSRP_MXN = ceil($msrp_mxn / 5) * 5;

        // Prepare the array to return
        $processed_data = [
            'tax_br' => $tax_br,
            'trans_mx_taxes' => $total_trans_mx_taxes,
            'cost_prod_per_m' => $cost_prod_per_m,
            'cost_total_prod_br' => $cost_total_prod,
            'fee_shipping' => $shipping_fee,
            'markup_profit' => $markup_profit,
            'markup_custom' => $markup_custom,
            'total_markup' => $total_markup,
            'trans_mx_msrp' => $trans_mx_MSRP_MXN,
            'br_msrp' => $rounded_MSRP,
            'export_invoice_price' => $export_invoice_price
        ];

        $this->updateDatabase($processed_data, $products->id);
        $this->outputHTMLTable($products, $processed_data, $perimeter);
        return $processed_data;
    }
    private function updateDatabase($processed_data, $productId) {
        // Update costs and weights table
        $result = $this->wpdb->update('costs_and_weights', array(
            'tax_br' => $processed_data['tax_br'],
            'trans_mx_taxes' => $processed_data['trans_mx_taxes'],
            'cost_prod_per_m' => $processed_data['cost_prod_per_m'],
            'cost_total_prod_br' => $processed_data['cost_total_prod_br'],
            'fee_shipping' => $processed_data['fee_shipping'],
            'markup_profit' => $processed_data['markup_profit'],
            'markup_custom' => $processed_data['markup_custom'],
            'trans_mx_msrp' => $processed_data['trans_mx_msrp'],
            'br_msrp' => $processed_data['br_msrp'],
            'export_invoice_price' => $processed_data['export_invoice_price']
        ), array('id' => $productId));

    }
    private function outputHTMLTable($products, $processed_data, $perimeter) {
        
            echo '
            <style>
            .avedik-table, .avedik-table th, .avedik-table td {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 4px;
                text-align: left;
            }

            .avedik-table {
                width: 100%;
                margin-top: 20px;
            }

            .avedik-table th {
                background-color: #e6e6e6; /* Light grey background for headers */
                position: sticky;
                top: 0;
                z-index: 10; /* Keep the header on top */
            }

            .avedik-table tr:nth-child(even) {
                background-color: #f2f2f2; /* Zebra striping for rows */
            }

            .avedik-table tr:hover {
                background-color: #d0d0d0; /* Highlight row on hover */
            }

            .avedik-table th, .avedik-table td {
                padding: 8px; /* More padding for cell content */
            }
        </style>';
            echo '
            <table class="avedik-table">
            <th>Product Id</th>
            <th>Model Number</th>
            <th>Perimeter</th>
            <th>QTY / Day</th>
            <th>Prod Cost (per m)</th>
            <th>Subtotal No Profit (BR)</th>
            <th>Total Markup</th>
            <th>Tax BR</th>
            <th>Br MSRP</th>
            <th>Export Invoice Price</th>
            <th>Shipping fee</th>
            <th>MX - T Import Tax (BRL)</th>
            <th>MX - T Sales Tax (BRL)</th>
            <th>MX - T Total Tax (BRL)</th>
            <th>MX - T MSRP (BRL)</th>
            <th>MX - T MSRP (MXN)</th>
            <tbody>';
           echo '<tr>
                <td> '. $products->id .' </td>
                <td> ' . $products->model_number . ' </td>
                <td>  ' . $perimeter . '</td>
                <td>'.  number_format($processed_data['qty_per_day'],2) .'</td>
                <td>'.  number_format($processed_data['cost_prod_per_m'],2) .'</td>
                <td>'.  number_format($processed_data['cost_total_prod'],2) .'</td>
                <td>'.  number_format($processed_data['total_markup'],2) .'</td>
                <td>'.  number_format($processed_data['tax_br'],2) .'</td>
                <td>'.  number_format($processed_data['rounded_MSRP'],2) .'</td>
                <td>'.  number_format($processed_data['export_invoice_price'], 2) .'</td>
                <td>'.  number_format($processed_data['shipping_fee'],2) .'</td>
                <td>'.  number_format($processed_data['import_tax'],2) .'</td>
                <td>'.  number_format($processed_data['sales_tax'],2) .'</td>
                <td>'.  number_format($processed_data['total_trans_mx_taxes'],2) .'</td>
                <td>'.  number_format($processed_data['trans_mx_MSRP_BRL'],2) .'</td>
                <td>'.  number_format($processed_data['trans_mx_MSRP_MXN'],2) .'</td>
            </tr>';
            echo '</tbody></table>'; 
    }
}
?>