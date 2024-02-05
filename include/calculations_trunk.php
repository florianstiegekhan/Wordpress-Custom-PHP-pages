
<?php
include  get_stylesheet_directory().'/inc/msrp_calculation.php';
class Calculations_trunk extends Msrp_calculation {
    private $material_costs = array();
    private $material_weights = array();
    private $wpdb;
   
    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        //$this->fetchMaterialData();
    }
    function fetchMaterialData($product, $product_info) {
        // Fetch all materials, their costs, and weights
        $materials_results = $this->wpdb->get_results("SELECT * FROM mat");
        $material_costs = array();
        $material_weights = array();
        foreach ($materials_results as $material) {
            $this->material_costs[$material->material] = $material->price;
            $this->material_weights[$material->material] = $material->weight;
        }
        $calculation_results = $this->processProduct($product_info);
        $this->postCalculatedValues($product, $calculation_results );
        $Msrp = new Msrp_calculation();
        $Msrp->ProcessData($product, $calculation_results);

        //return $calculation_results;
    }

    private function processProduct($product_info) {

        // Step 1: Basic product dimensions
        $ext_l = $product_info['length'] + 0.02;
        $ext_w = $product_info['width'] + 0.02;
        $ext_h = $product_info['height'] + 0.02;
        $ext_h_d = ($product_info['height_d'] > 0) ? ($product_info['height_d'] + 0.02) : $product_info['height_d'];

        // Step 2: Shipping dimensions
        $ship_l = $ext_l + (($product_info['wheels_360'] || $product_info['wheels']) ? 0.08 : 0.02);
        $ship_w = $ext_w + 0.02;
        $ship_h = $ext_h_d > 0 ? $ext_h + $ext_h_d + 0.02 : $ext_h + 0.02;

        $dim_1 = $ext_h - 0.07;
        $dim_2 = $ext_h - $dim_1;

            
        $nylon_calc = (($ext_l + $ext_w + $ext_w + 0.2) / 2) + 0.02;
        $nylon_back_calc = (($ext_l - 0.2) + 0.02);

        // Steps 3 Start Calculations
        // Calculate nylon quantities and values
        $nylon_top_and_bottom_qty = 2;
        $nylon_top_and_bottom = $nylon_top_and_bottom_qty * ($ext_l * $ext_w);

        $nylon_lateral_bottom_qty = 2;
        $nylon_lateral_bottom = $nylon_lateral_bottom_qty * $nylon_calc * ($dim_1 + 0.01);

        $nylon_lateral_top_qty = 2;
        $nylon_lateral_top = $nylon_lateral_top_qty * $nylon_calc * ($dim_2 + 0.01);

        $nylon_lateral_double_qty = 0;
        $nylon_lateral_double = $nylon_lateral_double_qty;

        $nylon_back_qty = 1;
        $nylon_back = $nylon_back_qty * ((($ext_l - 0.2) + 0.02) * $product_info['height']);

        $nylon_pocket_large_qty = $product_info['nylon_pocket_large_qty'];
        $nylon_pocket_large = $nylon_pocket_large_qty * ($product_info['length'] * ($ext_w - 0.06));

        $nylon_backpack_qty = $product_info['backpack'] > 0 ? 4 : 0;
        $nylon_backpack = $nylon_backpack_qty * (0.4 * 0.12);

        $nylon_center_hand_strap_qty = $product_info['center_hand_strap'] > 0 ? 2 : 0;
        $nylon_center_hand_strap = $nylon_center_hand_strap_qty * (0.15 * 0.15);

        $nylon_shoulder_strap_cut_1_qty = $product_info['shoulder_strap'] > 0 ? 2 : 0;
        $nylon_shoulder_strap_cut_1 = $nylon_shoulder_strap_cut_1_qty * (0.4 * 0.15);

        $nylon_shoulder_strap_cut_2_qty = $product_info['shoulder_strap'] > 0 ? 1 : 0;
        $nylon_shoulder_strap_cut_2 = $nylon_shoulder_strap_cut_2_qty * (0.3 * 0.15);

        global $wpdb;
        $query="select length_of_material_nylon,length_of_material_eva,length_of_material_plush,length_of_material_foam,length_of_material_straps from costs";
        $cost_data=$wpdb->get_results($query, ARRAY_A);

        // Sum the results
        $length_of_material_nylon = $cost_data[0]['length_of_material_nylon'];

        $nylon_final = ($nylon_top_and_bottom + $nylon_lateral_bottom + $nylon_lateral_top + $nylon_lateral_double + $nylon_back + $nylon_pocket_large) / $length_of_material_nylon;
        $nylon_acc_backpack_total = $nylon_backpack / $length_of_material_nylon;
        $nylon_acc_center_hand_strap_total = $nylon_center_hand_strap / $length_of_material_nylon;
        $nylon_acc_shoulder_strap_cut_1_total = $nylon_shoulder_strap_cut_1 / $length_of_material_nylon;
        $nylon_acc_shoulder_strap_cut_2_total = $nylon_shoulder_strap_cut_2 / $length_of_material_nylon;

        $combined_nylon_acc_final = $nylon_acc_backpack_total + $nylon_acc_center_hand_strap_total + $nylon_acc_shoulder_strap_cut_1_total + $nylon_acc_shoulder_strap_cut_2_total;

        // EVA calculations
        $eva_top_and_bottom_qty = 2;
        $eva_top_and_bottom = $eva_top_and_bottom_qty * $product_info['length'] * $product_info['width'];

        $eva_lateral_bottom_front_qty = 2;
        $eva_lateral_bottom_front = $eva_lateral_bottom_front_qty * $product_info['length'] * $dim_1;

        $eva_lateral_top_front_qty = 2;
        $eva_lateral_top_front = $eva_lateral_top_front_qty * $product_info['length'] * $dim_2;

        $eva_lateral_side_bottom_qty = 2;
        $eva_lateral_side_bottom = $eva_lateral_side_bottom_qty * ($ext_w) * $dim_1;

        $eva_lateral_side_top_qty = 2;
        $eva_lateral_side_top = $eva_lateral_side_top_qty * ($ext_w) *  $dim_2;

        // start EVA divider

                // Calculate Divider Horizontal
        $eva_divider_horizontal_qty = $product_info['divider_horizontal'];
        $eva_divider_horizontal = $eva_divider_horizontal_qty * $product_info['length']* $dim_1;

        // Calculate Divider Vertical
        if ($eva_divider_horizontal_qty == 0) {
            $eva_divider_vertical_qty = $product_info['divider_vertical'];
        } else {
            $eva_divider_vertical_qty = 0;
        }
        $eva_divider_vertical = $eva_divider_vertical_qty * $product_info['width'] * $dim_1;

        // Calculate Divider Vertical W/ Horizontal
        if ($eva_divider_horizontal_qty > 0 && $product_info['divider_vertical'] > 0) {
            $eva_divider_vertical_w_horizontal_qty = $product_info['divider_vertical'];
        } else {
            $eva_divider_vertical_w_horizontal_qty = 0;
        }
        $eva_divider_vertical_w_horizontal = $eva_divider_vertical_w_horizontal_qty * ($product_info['width'] - 0.01) * $dim_1;

        // Calculate total EVA Divider
        $eva_divider_total = $eva_divider_horizontal + $eva_divider_vertical + $eva_divider_vertical_w_horizontal;

        // Initialize Velcro Divider Variables
        $velcro_divider_horizontal = 0;
        $velcro_divider_vertical = 0;
        $velcro_divider_vert_hor = 0;

        // Calculate Velcro Divider Horizontal
        if ($eva_divider_horizontal_qty > 0) {
            $velcro_divider_horizontal = ($product_info['length']* 2) * $eva_divider_horizontal_qty;
        }

        // Calculate Velcro Divider Vertical
        if ($eva_divider_vertical_qty > 0) {
            $velcro_divider_vertical = ($eva_divider_vertical_qty * 2) * $eva_divider_vertical_qty;
        }

        // Calculate Velcro Divider Vertical with Horizontal
        if ($eva_divider_vertical_w_horizontal_qty > 0) {
            $velcro_divider_vert_hor = (($product_info['width'] - 0.01) * 2) * $eva_divider_vertical_w_horizontal_qty;
        }

        // Calculate Velcro 50mm Total
        $eva_divider_velcro_total = $velcro_divider_horizontal + $velcro_divider_vertical + $velcro_divider_vert_hor;


        // END EVA divider
        // Sum up all EVA calculations
        $eva_total = $eva_top_and_bottom + $eva_lateral_bottom_front + $eva_lateral_top_front + $eva_lateral_side_bottom + $eva_lateral_side_top + $eva_divider_total;

        // Divide the EVA total by the length of material (2)
        $length_of_material_eva = $cost_data[0]['length_of_material_eva'];
        $eva_final = $eva_total / $length_of_material_eva;
            
        // Plush calculations
        $plush_top_and_bottom_qty = 2;
        $plush_top_and_bottom = $plush_top_and_bottom_qty * $product_info['length'] * $product_info['width'];

        $plush_lateral_bottom_front_qty = 2;
        $plush_lateral_bottom_front = $plush_lateral_bottom_front_qty * $product_info['length'] * ($dim_1 * 2);

        $plush_lateral_top_front_qty = 2;
        $plush_lateral_top_front = $plush_lateral_top_front_qty * $product_info['length'] * ($dim_2 * 2);

        $plush_lateral_side_bottom_qty = 2;
        $plush_lateral_side_bottom = $plush_lateral_side_bottom_qty * ($ext_w) * ($dim_1 * 2);

        $plush_lateral_side_top_qty = 2;
        $plush_lateral_side_top = $plush_lateral_side_top_qty * ($ext_w) * ($dim_2 * 2);

        // Changes for double
        $plush_lateral_bottom_double_qty = 0;
        $plush_lateral_side_bottom_double = $plush_lateral_bottom_double_qty * ($product_info['length']) *  ($product_info['height_d'] * 2);

        $plush_lateral_side_double_qty = 0;
        $plush_lateral_side_double = $plush_lateral_side_double_qty * ($product_info['width']-.01) *   ($product_info['height_d'] * 2);

        //end change
        // start PLUSH divider logic


        // Divider Horizontal    
        $plush_divider_horizontal_qty = $product_info['divider_horizontal'] * 2.05;    
        $plush_divider_horizontal = $plush_divider_horizontal_qty * $product_info['length']* (($dim_1 * 2) + .05); 

        // Divider Vertical    
        if ($plush_divider_horizontal_qty > 0) {        
            $plush_divider_vertical_qty = 0;
        } else {    
            $plush_divider_vertical_qty = $product_info['divider_vertical'] * 2.05;
        }   
        $plush_divider_vertical = $plush_divider_vertical_qty * $product_info['width'] * (($dim_1 * 2) + .05);

        // Divider Vertical W/ Horizontal    
        if ($plush_divider_horizontal_qty > 0 && $product_info['divider_vertical'] > 0) {    
            $plush_divider_vertical_w_horizontal_qty = $product_info['divider_vertical'] * 2.05;
        } else {    
            $plush_divider_vertical_w_horizontal_qty = 0;
        }   

        $plush_divider_vertical_w_horizontal = $plush_divider_vertical_w_horizontal_qty * ($product_info['width'] - 0.01) * (($dim_1 * 2) + .05);

        // Calculate total plush divider
        $plush_divider_total = $plush_divider_horizontal + $plush_divider_vertical + $plush_divider_vertical_w_horizontal;



        // end plush divider logic
        $plush_total = $plush_top_and_bottom + $plush_lateral_bottom_front + $plush_lateral_top_front + $plush_lateral_side_bottom + $plush_lateral_side_top + $plush_lateral_side_bottom_double + $plush_lateral_side_double + $plush_divider_total;

        // Divide the Plush total by the length of material (1.5)
        $length_of_material_plush = $cost_data[0]['length_of_material_plush'];
        $plush_final = $plush_total / $length_of_material_plush;

        // Foam Calculations for Accessories
        // Backpack Foam
        $backpack_foam_qty = $product_info['backpack'] >0 ? 2 : 0;
        $backpack_foam = $backpack_foam_qty * 0.38 * 0.1;

        // Center Hand Strap Foam
        $center_hand_strap_foam_qty = $product_info['center_hand_strap'] > 0 ? $product_info['center_hand_strap'] : 0;
        $center_hand_strap_foam = $center_hand_strap_foam_qty * 0.05 * 0.12;

        // Shoulder Strap Foam
        $shoulder_strap_foam_qty = $product_info['shoulder_strap'] > 0 ? $product_info['shoulder_strap'] : 0;
        $shoulder_strap_foam = $shoulder_strap_foam_qty * 0.36 * 0.11;

        // Sum up all foam calculations
        $foam_total = $backpack_foam + $center_hand_strap_foam + $shoulder_strap_foam;

        // Divide the foam total by the length of material (1)
        $length_of_material_foam = $cost_data[0]['length_of_material_foam'];
        $foam_final = $foam_total / $length_of_material_foam;  

        // Strap CA calculations
        $straps_ca_fixed_hand_strap_qty = $product_info['fixed_hand_strap'] > 0 ? $product_info['fixed_hand_strap'] : null;
        $straps_ca_fixed_hand_strap = $straps_ca_fixed_hand_strap_qty * (0.2 * 2);

        $straps_ca_center_hand_strap_qty = $product_info['center_hand_strap'] > 0 ? $product_info['center_hand_strap'] : null;
        $straps_ca_center_hand_strap = $straps_ca_center_hand_strap_qty * (2 * (($product_info['width'] * 2) + $product_info['height'] + 0.3));

        $straps_ca_backpack_reinforcement_qty = $product_info['backpack'] > 0 ? 1 : null;
        $straps_ca_backpack_reinforcement = $straps_ca_backpack_reinforcement_qty * $product_info['width'] * 1;

        $straps_ca_for_backpack_fixed_velcro_qty = $product_info['backpack'] > 0 ? 1 : null;
        $straps_ca_for_backpack_fixed_velcro = $straps_ca_for_backpack_fixed_velcro_qty * (0.127 * 2);

        $straps_ca_for_backpack_strap_rectangles_qty = $product_info['backpack'] > 0 ? 1 : null;
        $straps_ca_for_backpack_strap_rectangles = $straps_ca_for_backpack_strap_rectangles_qty * (0.8128 * 2);

        $straps_ca_for_shoulder_strap_qty = $product_info['shoulder_strap'] > 0 ? 1 : null;
        $straps_ca_for_shoulder_strap = $straps_ca_for_shoulder_strap_qty * 1.5;

        // Sum up all Strap_CA calculations
        $straps_ca_total = $straps_ca_fixed_hand_strap + $straps_ca_center_hand_strap + $straps_ca_backpack_reinforcement + $straps_ca_for_backpack_fixed_velcro + $straps_ca_for_backpack_strap_rectangles + $straps_ca_for_shoulder_strap;

        // Divide the Strap_CA total by the length of material (1)
        $length_of_material_straps = $cost_data[0]['length_of_material_straps'];
        $strap_final = $straps_ca_total / $length_of_material_straps;

        // Material Calculations

            // Nylon
        $nylon_cost = $this->material_costs['Nylon'];
        $nylon_mat_cost = $nylon_final * $nylon_cost;

        // Nylon for all accessories + backpack
        $nylon_acc_backpack_mat_cost = $combined_nylon_acc_final * $nylon_cost;

        // Plush
        $plush_cost = $this->material_costs['Plush'];
        $plush_mat_cost = $plush_final * $plush_cost;

        // Foam for all accessories + backpack
        $foam_cost = $this->material_costs['Foam for all accessories + backpack'];
        $foam_mat_cost = $foam_final * $foam_cost;

        // EVA Rubber Pro Line
        $eva_cost = $this->material_costs['EVA'];
        $eva_mat_cost = $eva_final * $eva_cost;

        // Zipper
        $zipper_cost = $this->material_costs['Zipper'];
        if ($product_info['nylon_pocket_large_qty'] > 0) {
            $zipper_calculation = ($nylon_calc*2) + $ext_l + .02;
        } else {
            $zipper_calculation = ($nylon_calc * 2)+.02;
        }
        $zipper_mat_cost = $zipper_cost * $zipper_calculation;

        // Zipper Pull Tab
        $zipper_pull_tab_cost = $this->material_costs['Zipper Pull Tab'];
        $zipper_pull_tab_calculation = 2;
        $zipper_pull_tab_mat_cost =  $zipper_pull_tab_calculation * $zipper_pull_tab_cost;
        
        // Straps CA 40mm Total
        $strap_cost = $this->material_costs['Straps CA 40mm'];
        $strap_mat_cost = $strap_final * $strap_cost;

        // Gorilla Glue 793
        $gorilla_glue_cost = $this->material_costs['Gorilla Glue 793'];
        $gorilla_glue_calculation = 1;
        $gorilla_glue_mat_cost = $gorilla_glue_calculation  * $gorilla_glue_cost;  

        // Step Profile
        $step_profile_cost = $this->material_costs['Step Profile'];
        $step_profile_calculation = ($ext_l + $ext_w) * 4;
        $step_profile_mat_cost =  $step_profile_calculation * $step_profile_cost;

        // ONLY ROUND Rubber Straps 1.6mm nylon_lat_bottom_qty is always 2
        // $rubber_straps_cost = $this->material_costs['Rubber Straps 1.6mm'];
        // $rubber_straps_calculation = ((((2 * $nylon_calc) + $nylon_back_calc) * 2) - ($nylon_back_calc * 4));
        // $rubber_straps_mat_cost = $rubber_straps_calculation * $rubber_straps_cost;

        // Velcro 50 mm
        $velcro_cost = $this->material_costs['Velcro 50 mm'];
        $velcro_calculation = (($product_info['width'] + $product_info['height']) * 2)+ $eva_divider_velcro_total;
        $velcro_mat_cost = $velcro_calculation * $velcro_cost;

        // Velcro 50 mm for Backpack + center hand strap
        $velcro_bp_cost = $this->material_costs['Velcro 50 mm'];
        $velcro_bp_calculation = ($product_info['backpack'] > 0) ? ((0.127*2) + 0.15) : 0.15;
        $velcro_bp_mat_cost = $velcro_bp_calculation * $velcro_bp_cost;

        // Finish straps case + accessories
        $finish_straps_cost = $this->material_costs['Finish straps case + accessories'];
        $finish_straps_calculation = $product_info['nylon_pocket_large_qty']> 0 ? ($product_info['width'] * 8) + ($product_info['length'] * 8) + ($ext_w - 0.06) + 4 : ($product_info['length'] * 8) + ($product_info['width'] * 8) + 4;
        $finish_straps_mat_cost = $finish_straps_calculation * $finish_straps_cost;

        // Strap Rectangle
        $strap_rectangle_cost = $this->material_costs['Strap Rectangle'];
        $strap_rectangle_calculation = 0;

        // Check if backpack exists, if so, add 2 to strap_rectangle_calculation
        if ($product_info['backpack'] > 0) {
            $strap_rectangle_calculation += 2;
        }

        // Check if shoulder_strap exists, if so, add 2 * shoulder_strap to strap_rectangle_calculation
        if ($product_info['shoulder_strap'] > 0) {
            $strap_rectangle_calculation += $product_info['shoulder_strap'] * 2;
        }

        $strap_rectangle_mat_cost = $strap_rectangle_calculation * $strap_rectangle_cost;

        // Strap Rectangle Regulator
        $strap_rectangle_regulator_cost = $this->material_costs['Strap Rectangle Regulator'];
        $strap_rectangle_regulator_calculation = 0;

        // Check if backpack exists, if so, add 2 to strap_rectangle_regulator_calculation
        if ($product_info['backpack'] > 0) {
            $strap_rectangle_regulator_calculation += 2;
        }

        // Check if shoulder_strap exists, if so, add 1 * shoulder_strap to strap_rectangle_regulator_calculation
        if ($product_info['shoulder_strap'] > 0) {
            $strap_rectangle_regulator_calculation += $product_info['shoulder_strap'];
        }

        $strap_rectangle_regulator_mat_cost = $strap_rectangle_regulator_calculation * $strap_rectangle_regulator_cost;

        // Hollow Screw
        $hollow_screw_cost = $this->material_costs['Hollow Screw'];
        $hollow_screw_calculation = ($product_info['fixed_hand_strap'] > 0) ? (($product_info['fixed_hand_strap'] * 2) + ($product_info['corners'] * 3) + 16) : 0;
        $hollow_screw_mat_cost = $hollow_screw_calculation * $hollow_screw_cost;

        // Corners
        $corners_cost = $this->material_costs['Corners'];
        $corners_mat_cost = $product_info['corners'] * $corners_cost;

        // Rivets
        $rivets_cost = $this->material_costs['Rivets'];
        $rivets_calculation = ceil((((($step_profile_calculation / 0.1) * 2) + ($product_info['corners'] * 3) + ($product_info['fixed_hand_strap'] * 3) + 20) / 10)) * 10;
        $rivets_mat_cost = $rivets_calculation * $rivets_cost;

        // Wheels
        $wheels_cost = $this->material_costs['wheels'];
        $wheels_mat_cost =(int)$product_info['wheels'] * $wheels_cost;

        // Wheels 360
        $wheels_360_cost = $this->material_costs['wheels 360'];
        $wheels_360_mat_cost = (int)$product_info['wheels_360'] * $wheels_360_cost;
        // Embroidery
        $embroidery_cost = $this->material_costs['Embroidery'];
        $embroidery_mat_cost = $product_info['embroidery'] * $embroidery_cost;

        // Staples
        $staples_cost = $this->material_costs['Staples'];
        $staples_calculation = ceil((($product_info['length'] + $product_info['width'] + $dim_1 + $dim_2) * 400 + ($ext_l + $ext_w) * 600) / 100) * 100;
        $staples_mat_cost = $staples_calculation * $staples_cost;

        // Silicone for plush
        $silicone_cost = $this->material_costs['Silicone for plush'];
        $silicone_calculation = (($step_profile_calculation + $rubber_straps_calculation) > 5) ? 2 : 1;
        $silicone_mat_cost = $silicone_calculation * $silicone_cost;

        // Telescopic Handle
        $telescopic_handle_cost = $this->material_costs['Telescopic Handle'];
        $telescopic_handle_mat_cost = $product_info['telescopic_handle'] * $telescopic_handle_cost;

        // Extra Wood
        $extra_wood_cost = $this->material_costs['Extra Wood'];
        $extra_wood_mat_cost = $product_info['extra_wood'] * $extra_wood_cost;

        // Third Party Sewing backpack
        $third_party_sewing_backpack_cost = $this->material_costs['Sewing backpack'];
        $third_party_sewing_backpack_mat_cost = ($product_info['backpack'] > 0) ? 1 * $third_party_sewing_backpack_cost : null;

        // Third Party Sewing Pocket, No backpack
        $third_party_sewing_pocket_cost = $this->material_costs['Sewing Pocket No backpack'];
        $third_party_sewing_pocket_mat_cost = ($product_info['backpack'] == 0 && $product_info['nylon_pocket_large_qty']> 0) ? 1 * $third_party_sewing_pocket_cost : null;

        // Third Party Sewing No pocket/backpack
        $third_party_sewing_no_pocket_cost = $this->material_costs['Sewing no pocket no backpack'];
        $third_party_sewing_no_pocket_mat_cost = ($product_info['backpack'] == 0 && $product_info['nylon_pocket_large_qty']== 0) ? 1 * $third_party_sewing_no_pocket_cost : null;

        // Wheel screw kit for 360
        $wheel_screw_kit_360_cost = $this->material_costs['Wheel Screw Kit 360'];
        $wheel_screw_kit_360_calculation = ($product_info['wheels_360'] > 0) ? $product_info['wheels_360'] * 4 : 0;
        $wheel_screw_kit_360_mat_cost = $wheel_screw_kit_360_calculation * $wheel_screw_kit_360_cost;

        // Wheel screw kit for normal
        $wheel_screw_kit_normal_cost = $this->material_costs['Wheel Screw Kit Normal'];
        $wheel_screw_kit_normal_calculation = ($product_info['wheels'] > 0) ? $product_info['wheels'] * 5 : 0;
        $wheel_screw_kit_normal_mat_cost = $wheel_screw_kit_normal_calculation * $wheel_screw_kit_normal_cost;

        // Internal compartment vault trip only
        $internal_cost = $this->material_costs['Internal compartment Vault Trip'];

        // Check if product type is vault trip
        if ($product_type == 'Vault Trip') {
            $internal_calculation = 1;
        } else {
            $internal_calculation = 0;
        }

        $internal_mat_cost = $internal_calculation * $internal_cost;


        $cost_material = $nylon_mat_cost +
                        $nylon_acc_backpack_mat_cost +
                        $plush_mat_cost +
                        $foam_mat_cost +
                        $eva_mat_cost +
                        $zipper_mat_cost +
                        $zipper_pull_tab_mat_cost +
                        $strap_mat_cost +
                        $gorilla_glue_mat_cost +
                        $step_profile_mat_cost +
                        $rubber_straps_mat_cost +
                        $velcro_mat_cost +
                        $velcro_bp_mat_cost +
                        $finish_straps_mat_cost +
                        $strap_rectangle_mat_cost +
                        $strap_rectangle_regulator_mat_cost +
                        $hollow_screw_mat_cost +
                        $corners_mat_cost +
                        $rivets_mat_cost +
                        $wheels_mat_cost +
                        $wheels_360_mat_cost +
                        $embroidery_mat_cost +
                        $staples_mat_cost +
                        $silicone_mat_cost +
                        $telescopic_handle_mat_cost +
                        $extra_wood_mat_cost +
                        $third_party_sewing_backpack_mat_cost +
                        $third_party_sewing_pocket_mat_cost +
                        $third_party_sewing_no_pocket_mat_cost +
                        $wheel_screw_kit_360_mat_cost +
                        $wheel_screw_kit_normal_mat_cost + 
                    $internal_mat_cost;
       
                    // Material Weight Calculations
        $nylon_weight_total = $nylon_final * $this->material_weights['Nylon'];
        $nylon_acc_backpack_weight_total = $combined_nylon_acc_final * $this->material_weights['Nylon'];
        $eva_weight_total = $eva_final * $this->material_weights['EVA'];
        $plush_weight_total = $plush_final * $this->material_weights['Plush'];
        $foam_weight_total = $foam_final * $this->material_weights['Foam for all accessories + backpack'];
        $zipper_weight_total = $zipper_calculation * $this->material_weights['Zipper'];
        $zipper_pull_tab_weight_total =  $zipper_pull_tab_calculation * $this->material_weights['Zipper Pull Tab'];
        $strap_weight_total = $strap_final * $this->material_weights['Straps CA 40mm'];
        $step_profile_weight_total =  $step_profile_calculation * $this->material_weights['Step Profile'];
        $rubber_straps_weight_total = $rubber_straps_calculation * $this->material_weights['Rubber Straps 1.6mm'];
        $strap_rectangle_weight_total = $strap_rectangle_calculation * $this->material_weights['Strap Rectangle'];
        $strap_rectangle_regulator_weight_total = $strap_rectangle_regulator_calculation * $this->material_weights['Strap Rectangle Regulator'];
        $wheels_weight_total = (int)$product_info['wheels'] * $this->material_weights['wheels'];
        $wheels_360_weight_total = (int)$product_info['wheels_360'] * $this->material_weights['wheels 360'];

        // Calculate Total Weight
        $total_weight = $nylon_weight_total + 
                        $nylon_acc_backpack_weight_total + 
                        $plush_weight_total +
                        $foam_weight_total +
                        $eva_weight_total +
                        $zipper_weight_total +
                        $zipper_pull_tab_weight_total +
                        $strap_weight_total +
                        $step_profile_weight_total +
                        $rubber_straps_weight_total +
                        $strap_rectangle_weight_total +
                        $strap_rectangle_regulator_weight_total +
                        $wheels_weight_total +
                        $wheels_360_weight_total;
    
        // Compile all calculated values
        $calculated_values = [
            'ext_l' => $ext_l,
            'ext_w' => $ext_w,
            'ext_h' => $ext_h,
            'ext_h_d' => $ext_h_d,
            'ship_l' => $ship_l,
            'ship_w' => $ship_w,
            'ship_h' => $ship_h,
            'nylon_final' => $nylon_final,
            'combined_nylon_acc_final' => $combined_nylon_acc_final,
            'plush_final' => $plush_final,
            'foam_final' => $foam_final,
            'strap_final' => $strap_final,
            'nylon_calc' => $nylon_calc,
            'nylon_back_calc' => $nylon_back_calc,
            'eva_final' => $eva_final,
            'eva_divider_velcro_total'=> $eva_divider_velcro_total,
            'cost_final' => $cost_material,
            'zipper_calculation' => $zipper_calculation,
            'zipper_pull_tab_calculation' => $zipper_pull_tab_calculation,
            'gorilla_glue_calculation' => $gorilla_glue_calculation,
            'step_profile_calculation' => $step_profile_calculation,
            'rubber_straps_calculation' => $rubber_straps_calculation,
            'velcro_calculation' => $velcro_calculation,
            'velcro_bp_calculation' => $velcro_bp_calculation,
            'finish_straps_calculation' => $finish_straps_calculation,
            'strap_rectangle_calculation' => $strap_rectangle_calculation,
            'strap_rectangle_regulator_calculation' => $strap_rectangle_regulator_calculation,
            'hollow_screw_calculation' => $hollow_screw_calculation,
            'rivets_calculation' => $rivets_calculation,
            'staples_calculation' => $staples_calculation,
            'silicone_calculation' => $silicone_calculation,
            'wheel_screw_kit_360_calculation' => $wheel_screw_kit_360_calculation,
            'wheel_screw_kit_normal_calculation' => $wheel_screw_kit_normal_calculation,
            'nylon_weight_total' => $nylon_weight_total,
            'nylon_acc_backpack_weight_total' => $nylon_acc_backpack_weight_total,
            'plush_weight_total' => $plush_weight_total,
            'foam_weight_total' => $foam_weight_total,
            'eva_weight_total' => $eva_weight_total,
            'zipper_weight_total' => $zipper_weight_total,
            'zipper_pull_tab_weight_total' => $zipper_pull_tab_weight_total,
            'strap_weight_total' => $strap_weight_total,
            'step_profile_weight_total' => $step_profile_weight_total,
            'rubber_straps_weight_total' => $rubber_straps_weight_total,
            'strap_rectangle_weight_total' => $strap_rectangle_weight_total,
            'strap_rectangle_regulator_weight_total' => $strap_rectangle_regulator_weight_total,
            'wheels_weight_total' => $wheels_weight_total,
            'wheels_360_weight_total' => $wheels_360_weight_total,
            'eva_divider_velcro_total' => $eva_divider_velcro_total,
            'third_party_sering_backpack_mat_cost'=> $third_party_sewing_backpack_mat_cost,
            'third_party_sewing_pocket_mat_cost' => $third_party_sewing_pocket_mat_cost,
            'third_party_sewing_no_pocket_mat_cost' => $third_party_sewing_no_pocket_mat_cost,
            'total_weight' => $total_weight
        ];
        return $calculated_values;
    }
    // post these values to the database

    private function postCalculatedValues($product, $calculated_values)
    {
        // Update the 'bypass' table
        $result_bypass = $this->wpdb->update('bypass', array(
            'nylon_calc' => $calculated_values['nylon_calc'],
            'nylon_back_calc' => $calculated_values['nylon_back_calc']
        ), array('id' => $product->id));

        // Update the 'meas' table
        $result_meas = $this->wpdb->update('meas', array(
            'nylon' => $calculated_values['nylon_final'],
            'nylon_acc_backpack' => $calculated_values['combined_nylon_acc_final'],
            'plush' => $calculated_values['plush_final'],
            'foam_acc_backpack' => $calculated_values['foam_final'],
            'straps_ca_40mm_total' => $calculated_values['strap_final'],
            'eva' => $calculated_values['eva_final'],
            'eva_divider_velcro_total' => $dimensions['eva_divider_velcro_total'],
            'zipper' => $calculated_values['zipper_calculation'],
            'zipper_pull_tab' => $calculated_values['zipper_pull_tab_calculation'],
            'gorilla_glue_793' => $calculated_values['gorilla_glue_calculation'],
            'step_profile_aluminum' => $calculated_values['step_profile_calculation'],
            'rubber_straps_1_6mm' => $calculated_values['rubber_straps_calculation'],
            'velcro_50mm' => $calculated_values['velcro_calculation'],
            'velcro_50mm_backpack_center_hand_strap' => $calculated_values['velcro_bp_calculation'],
            'finish_straps_case_accessories' => $calculated_values['finish_straps_calculation'],
            'strap_rectangle' => $calculated_values['strap_rectangle_calculation'],
            'strap_rectangle_regulator' => $calculated_values['strap_rectangle_regulator_calculation'],
            'hollow_screw' => $calculated_values['hollow_screw_calculation'],
            'rivets' => $calculated_values['rivets_calculation'],
            'staples' => $calculated_values['staples_calculation'],
            'silicone_plush' => $calculated_values['silicone_calculation'],
            'wheel_screw_kit_360' => $calculated_values['wheel_screw_kit_360_calculation'],
            'wheel_screw_kit_normal' => $calculated_values['wheel_screw_kit_normal_calculation']
            ), array('id' => $product->id));

        // Update the 'products' table
        $result_products = $this->wpdb->update('products', array(
            'ext_l' => $calculated_values['ext_l'],
            'ext_w' => $calculated_values['ext_w'],
            'ext_h' => $calculated_values['ext_h'],
            'ext_h_d' => $calculated_values['ext_h_d'],
            'ship_l' => $calculated_values['ship_l'],
            'ship_w' => $calculated_values['ship_w'],
            'ship_h' => $calculated_values['ship_h']
        ), array('id' => $product->id));

        // Update costs and weights table
        $result_costs_and_weights = $this->wpdb->update('costs_and_weights', array(
            'material_weight' => $calculated_values['total_weight'],
            'cost_material' => $calculated_values['cost_final']           
        ), array('id' => $product->id));

        // Update 3rd party sewing
        if ($calculated_values['third_party_sewing_backpack_mat_cost'] > 0)
        {
            $third_party_sewing_backpack=1;
        }
        else
        {
            $third_party_sewing_backpack=0;
        }
        
        if($calculated_values['third_party_sewing_pocket_mat_cost']>0){
            $third_party_sewing_pocket_no_backpack=1;
        }else{
            $third_party_sewing_pocket_no_backpack=0;
        } 

        if($calculated_values['third_party_sewing_no_pocket_mat_cost']>0){
            $third_party_sewing_no_pocket_backpack=1;
        }else{
            $third_party_sewing_no_pocket_backpack=0;
        } 
        $rows_updated_meas = $this->wpdb->update('meas', array(
            'third_party_sewing_backpack' => $third_party_sewing_backpack,
            'third_party_sewing_pocket_no_backpack' => $third_party_sewing_pocket_no_backpack,
            'third_party_sewing_no_pocket_backpack' => $third_party_sewing_no_pocket_backpack
        ), array('id' => $product->id));

        // Check results and handle errors
        if ($result_bypass === false || $result_meas === false || $result_products === false) {
            // Handle error (e.g., log it, display a message, etc.)
        } else {
            // Handle successful update (e.g., confirmation message)
        }
            //return $calculated_values;
    }
}
?>

