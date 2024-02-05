<?php
/* Template Name: Add New Product Page */
// Set your password here
$predefined_password = 'mx2023';
$message="";

//include  get_stylesheet_directory().'/inc/calculations.php';
include  get_stylesheet_directory().'/inc/calculations_tear_drop.php';
include  get_stylesheet_directory().'/inc/calculations_tear_drop_double.php';
include  get_stylesheet_directory().'/inc/calculations_trunk.php';
include  get_stylesheet_directory().'/inc/calculations_rectangle.php';
include  get_stylesheet_directory().'/inc/calculations_rectangle_double.php';
include  get_stylesheet_directory().'/inc/calculations_round_pocket.php';
include  get_stylesheet_directory().'/inc/calculations_round_no_pocket.php';
//include  get_stylesheet_directory().'/inc/msrp_calculation.php';

$url = $_SERVER['REQUEST_URI'];
$param1=parse_url($url, PHP_URL_QUERY);
$query_value=parse_str($param1, $queryParams);
if(isset($queryParams['message']))
{
    $message = rawurldecode($queryParams['message']);
} 

//var_dump($_POST);
//var_dump($_SESSION);


?>
<!DOCTYPE html>
<html>
<head>
    <script>
        function updateModelNumberPrefix() {
            const productTypeSelect = document.getElementById('product_type');
            const modelNumberInput = document.getElementById('model_number');
            const selectedProductType = productTypeSelect.value;

            if (selectedProductType.includes('double')) {
                modelNumberInput.value = 'SCP2-';
            } else {
                modelNumberInput.value = 'SCP-';
            }

            updateHeightD(selectedProductType);
        }

        function updateHeightD(productType) {
            const heightDInput = document.getElementById('height_d');

            if (productType.includes('double')) {
                heightDInput.value = '';
            } else {
                heightDInput.value = '0';
            }
        }

 function updateCorners() {
    const productionStyleSelect = document.getElementById('production_style');
    const cornersInput = document.getElementById('corners');
    const selectedProductionStyle = productionStyleSelect.value;

    if (['Trunk', 'Rectangle', 'Rectangle Double'].includes(selectedProductionStyle)) {
      cornersInput.value = 8;
      cornersInput.disabled = false;
    } else {
      cornersInput.value = 0;
      cornersInput.disabled = true;
    }
    const cornersHiddenInput = document.getElementById('corners_hidden');
    cornersHiddenInput.value = cornersInput.value;
  }
    </script>
</head>
<body onload="updateModelNumberPrefix(); updateCorners();">
<style>
      @import url('https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Roboto+Slab:wght@400;700&display=swap');

      html {
      height: 100%;
      min-height:800px;
      }
      body {
      /*background: url('https://i.pinimg.com/originals/48/79/86/487986c17560a8ed1afdc55e480e5be2.png');*/
      background-size:cover;
      background-repeat:no-repeat;
      text-align: center;
      font-family: 'Noto Sans', sans-serif;
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      -khtml-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      }

      h1{
      font-weight:400;
      padding-top:0;
      margin-top:0;
      font-family: 'Roboto Slab', serif;
      }

      #svg_form_time {
      height: 15px;
      max-width: 80%;
      margin: 40px auto 20px;
      display: block;
      }

      #svg_form_time circle,
      #svg_form_time rect {
      fill: white;
      }

      .button {
      background: rgb(237, 40, 70);
      border-radius: 5px;
      padding: 15px 25px;
      display: inline-block;
      margin: 10px;
      font-weight: bold;
      color: white;
      cursor: pointer;
      box-shadow:0px 2px 5px rgb(0,0,0,0.5);
      }

      .disabled {
      display:none;
      }

      section {
      padding: 50px ;
      max-width: 300px;
      margin: 30px auto;
      background:white;
      background:rgba(255,255,255,0.9);
      backdrop-filter:blur(10px);
      box-shadow:0px 2px 10px rgba(0,0,0,0.3);
      border-radius:5px;
      transition:transform 0.2s ease-in-out;
      }


      input {
      width: 100%;
      margin: 7px 0px;
      display: inline-block;
      padding: 12px 25px;
      box-sizing: border-box;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 1em;
      font-family:inherit;
      background:white;
      }

      select {
      width: 100%;
      margin: 7px 0px;
      display: inline-block;
      padding: 12px 25px;
      box-sizing: border-box;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 1em;
      font-family:inherit;
      background:white;
      }

      textarea{
      width: 100%;
      margin: 7px 0px;
      display: inline-block;
      padding: 12px 25px;
      box-sizing: border-box;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 1em;
      font-family:inherit;
      background:white;
      }
      p{
      text-align:justify;
      margin-top:0;
      }

      .column {
      float: left;
      width: 50%;
      margin: 2px: 
      }

      /* Clear floats after the columns */
      .row:after {
      content: "";
      display: table;
      clear: both;
      }
      .column input {
      width:90%;
      }
      @media screen and (max-width: 600px) {
      .column {
      width: 100%;
      margin: 5px: 
      }
      .column input {
      width:100%;
      }
      }

      .checkbox-row {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      margin-bottom: 10px;
      }

      .checkbox-column {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      }

      .checkbox-column label {
      display: inline-block;
      width: 60%; /* Adjust this value as needed */
      text-align: left;
      margin-right: 10px;
      }

      .checkbox-column input[type="checkbox"] {
      width: 20px; /* Adjust if necessary */
      height: 20px; /* Adjust if necessary */
      vertical-align: middle;
      }

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
</style>
<?php 
if (isset($_POST['add_product']) && isset( $_POST['model_number']) && isset($_SESSION['password_status']) && $_SESSION['password_status'] === "True") {
		
  //var_dump($_POST);
  
      global $wpdb;

     //check if the model number already exists.
      $model_number = $_POST['model_number'];
      $query = $wpdb->prepare( "SELECT * FROM products WHERE model_number = '$model_number'" );
      $cID = $wpdb->get_var( $query );
      if ($cID > 0) {
        $message="Model number already exists, please change it";
        echo '<p style="margin-top:20px;font-size:18px;color:rgb(237, 40, 70); text-align:center">'.$message.'</p>';
		    //wp_redirect( home_url().'/add-product?message='. rawurlencode($message) );
        //exit();
      } else {
        $data = array(
            'model_number' => $model_number,
            'product_type' => $_POST['product_type'],
            'production_style' => $_POST['production_style'],
            'description' => $_POST['description'],
            'length' => $_POST['length'],
            'width' => $_POST['width'],
            'height' => $_POST['height'],
            'height_d' => $_POST['height_d'],
            
         // Numeric fields
      'center_hand_strap' => $_POST['center_hand_strap'],
      'shoulder_strap' => $_POST['shoulder_strap'],
      'fixed_hand_strap' => $_POST['fixed_hand_strap'],
      'divider_horizontal' => $_POST['divider_horizontal'],
      'divider_vertical' => $_POST['divider_vertical'],
      'wheels' => $_POST['wheels'],
      'wheels_360' => $_POST['wheels_360'],
     'corners' => $_POST['corners_hidden'],
         
         // Checkbox fields
      'backpack' => isset($_POST['backpack']) ? 1 : 0,
      'nylon_pocket_large_qty' => isset($_POST['nylon_pocket_large_qty']) ? 1 : 0,
      'extra_wood' => isset($_POST['extra_wood']) ? 1 : 0,
      'telescopic_handle' => isset($_POST['telescopic_handle']) ? 1 : 0,
      'embroidery' => isset($_POST['embroidery']) ? 1 : 0,
      'custom' => isset($_POST['custom']) ? 1 : 0,

            
        );
        $message="";
        $wpdb->insert('products', $data);
        $product_id = $wpdb->insert_id;  // Get the ID of the newly inserted product
		    //echo "product id = $product_id"; exit;
        // Prepare the data to be inserted into the other tables
        // You only want to insert id and model number
        $additional_data = array(
            'id' => $product_id,
            'model_number' => $model_number
        );

        // Insert into 'meas' table
        $wpdb->insert('meas', $additional_data);

        // Insert into 'costs_and_weights' table
        $wpdb->insert('costs_and_weights', $additional_data);

        // Insert into 'bypass' table
        $wpdb->insert('bypass', $additional_data);

        //-- calculation section start --
        //check for calculations
       
        $calculations_tear_drop = new calculations_tear_drop();
        $calculations_tear_drop_double = new calculations_tear_drop_double();
        $calculations_trunk = new Calculations_trunk();
        $calculations_rectangle = new Calculations_rectangle();
        $calculations_rectangle_double = new Calculations_rectangle_double();
        $calculations_round_pocket = new Calculations_round_pocket();
        $calculations_round_no_pocket = new Calculations_round_no_pocket();
           
        $products = $wpdb->get_results("SELECT * FROM products WHERE id=$product_id");
        foreach ($products as $product) {
          //calculations_tear_drop
          if($product->production_style == "Tear Drop")
          {
            $calculations_tear_drop->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);

          }
          if($product->production_style == "Tear Drop Double")
          {
            $calculations_tear_drop_double->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);
          }
          if($product->production_style == "Trunk")
          {
            
            $calculations_trunk->fetchMaterialData($product, $_POST);
            //calculate MSRP
            //$update_status=true;
            //$Msrp = new Msrp();
            //$Msrp->update_msrp($product_id, $update_status);
          }
          if($product->production_style == "Rectangle")
          {
            
            $calculations_rectangle->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);
          }
          if($product->production_style == "Rectangle Double")
          {
            
            $calculations_rectangle_double->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);
          }
          if($product->production_style == "Round Pocket")
          {
            
            $calculations_round_pocket->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);
          }
          if($product->production_style == "Round No Pocket")
          {
            
            $calculations_round_no_pocket->fetch_product_data($product);
            //calculate MSRP
            $update_status=true;
            $Msrp = new Msrp();
            $Msrp->update_msrp($product_id, $update_status);
          }

        }
        //-- calculation section end --

        $message = "Product added successfully!";
        $_SESSION['password_status'] = "False";
        //wp_redirect( home_url().'/add-product?message='. rawurlencode($message) );
        echo '<p style="margin-top:20px;font-size:18px;color:rgb(237, 40, 70)">'.$message.'</p>';
		    echo '<div style="margin-top:10px;"><a href="/add-product" > Add New Product </a>';
        exit();
      }
} 
// Check if the form was submitted and the entered password matches the predefined password
elseif (isset($_POST['password']) && $_POST['password'] === $predefined_password) {
       $_SESSION['password_status'] = "True";
} elseif (isset($_POST['password']) && $_SESSION['password_status'] === "False") {
     // If the entered password is incorrect or not submitted yet, show the password form
        // Show a 404 error page when the entered password is incorrect
        // global $wp_query;
        // $wp_query->set_404();
        // status_header(404);
        // get_template_part(404);
        $message="Wrong Password";
        $_SESSION['password_status'] = "False";
        wp_redirect( home_url().'/add-product?message='. rawurlencode($message) );
        exit();
      
} else  {
  ?>
  <main id="site-content" role="main">
            <div style="width: 20%; max-width: 600px; padding-left: 20px;">
                <h2>Please enter the password to view the content:</h2>
                <form method="post" action="">
                    <input type="password" name="password" required>
                    <button type="submit">Submit</button>
                </form>
                <p style="margin-top:20px;font-size:18px;color:rgb(237, 40, 70)"><?php echo $message ?></p>
                <?php $message=""; ?>
            </div>

        </main>

<?php 
exit();
}
?>
<form action="" method="post">
<div id="svg_wrap"></div>

 <h1>Add Product</h1>
<section>
  <p><h2>Step 1</h2></p>
  <?php
  $list = array("Accordion", "Av Mixers", "Av Speakers","Cymbals","Cymbals Square","Dj","Dj Trunk","Drums","Drums Accessories","Drums Accessories Rectangle","Drums Digital Rectangle","Heads And Amps","Heads And Amps Rack","Heads And Amps Rectangle","Keyboard","Keyboard Double","Keyboard Trunk", "Multimedia Rectangle", "Pedalboard", "Pedalboard Double", "Peripheral", "Peripheral Double","Rack","Stage Accessory Rectangle","Stage Accessory Trunk","Strings","Strings Double",  "Strings Square","Vault Trip");
  echo ' <label for="product_type">Product Type:</label>';
  echo '<select id="product_type" name="product_type" required onchange="updateModelNumberPrefix()">';
  foreach($list as $select => $row){
    if($_POST['product_type']===$row){
      $selected = "selected"; 
    } else {
      $selected="";
    }
    echo '<option value="' . $row . '" '. $selected . '>' . $row . '</option>';
  }
  echo '</select>';
  ?>
        <!-- Commented out old code for product type selection -->
		    <!-- <label for="product_type">Product Type:</label>
				<select id="product_type" name="product_type" required onchange="updateModelNumberPrefix()">
					<option value="Accordion">Accordion</option> 
          <option value="Av Mixers">Av Mixers</option>
          <option value="Av Speakers">Av Speakers</option>  
          <option value="Cymbals">Cymbals</option>
          <option value="Cymbals Square">Cymbals Square</option>
          <option value="Dj">Dj</option>	
          <option value="Dj Trunk">Dj Trunk</option>	
          <option value="Drums">Drums</option>
          <option value="Drums Accessories">Drums Accessories</option>
          <option value="Drums Accessories Rectangle">Drums Accessories Rectangle</option>
          <option value="Drums Digital Rectangle">Drums Digital Rectangle</option>
          <option value="Heads And Amps">Heads And Amps</option>
          <option value="Heads And Amps Rack">Heads And Amps Rack</option>
          <option value="Heads And Amps Rectangle">Heads And Amps Rectangle</option>
          <option value="Keyboard">Keyboard</option>
          <option value="Keyboard Double">Keyboard Double</option>
          <option value="Keyboard Trunk">Keyboard Trunk</option>
          <option value="Multimedia Rectangle">Multimedia Rectangle</option>
          <option value="Pedalboards">Pedalboards</option>
          <option value="Pedalboard Double">Pedalboard Double</option>
          <option value="Peripheral">Peripheral</option>
          <option value="Peripheral Double">Peripheral Double</option>
          <option value="Rack">Rack</option>
          <option value="Stage Accessory Rectangle">Stage Accessory Rectangle</option>
          <option value="Stage Accessory Trunk">Stage Accessory Trunk</option>
          <option value="Strings">Strings</option>
          <option value="Strings Double">Strings Double</option>
          <option value="Strings Square">Strings Square</option>
          <option value="Vault Trip">Vault Trip</option>
				</select>
        -->
        <br>
        <?php
  $list2 = array("Rack", "Rectangle","Rectangle Double","Round Pocket","Round No Pocket","Trunk", "Tear Drop","Tear Drop Double");
  echo '<label for="production_style">Production Style:</label>';
  echo '<select id="production_style" name="production_style" required onchange="updateCorners()">';
  foreach($list2 as $select => $row){
    if($_POST['production_style']===$row){
      $selected = "selected"; 
    } else {
      $selected="";
    }
    echo '<option value="' . $row . '" '. $selected . '>' . $row . '</option>';
  }
  echo '</select>';
  ?>
		<!-- <label for="production_style">Production Style:</label>
		<select id="production_style" name="production_style" required onchange="updateCorners()">
			
      <option value="Rack">Rack</option>
      <option value="Rectangle">Rectangle</option>
   <option value="Rectangle Double">Rectangle Double</option>
      <option value="Round Pocket">Round Pocket</option>
      <option value="Round No Pocket">Round No Pocket</option>
   <option value="Trunk">Trunk</option>
      <option value="Tear Drop">Tear Drop</option>
      <option value="Tear Drop Double">Tear Drop Double</option>
		</select>-->


</section> 



<section>
  <p><h2>Step 2</h2></p>
			<label for="model_number">Model Number:</label>
        <input type="text" name="model_number" id="model_number" value="<?php echo $_POST['model_number'] ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo $_POST['description'] ?></textarea>
</section>

<section>
<p><h2>Step 3</h2><h4 style="margin-top:-10px;">Internal Measurements in meters</h4></p>


		<label for="length">Length:</label>
    <input type="number" name="length" id="length" max="5" value="<?php echo $_POST['length'] ?>" step="0.01">

    <label for="width">Width:</label>
    <input type="number" name="width" id="width" max="5" value="<?php echo $_POST['width'] ?>" step="0.01">

    <label for="height">Height:</label>
    <input type="number" name="height" id="height" max="5" value="<?php echo $_POST['height'] ?>" step="0.01">

    <label for="height_d">Height D:</label>
    <input type="number" name="height_d" id="height_d" max="5" value="<?php echo $_POST['height_d'] ?>" step="0.01">
</section>

<section>
  <p><h2>Step 4</h2></p>
  <div class="row">
    <div class="column">
      <label for="center_hand_strap">Center Hand Strap:</label>
      <input type="number" name="center_hand_strap" id="center_hand_strap" value="<?php echo isset($_POST['center_hand_strap']) ? $_POST['center_hand_strap'] : '' ?>" min="0" max="9" step="1">

      <label for="shoulder_strap">Shoulder Strap:</label>
      <input type="number" name="shoulder_strap" id="shoulder_strap" value="<?php echo isset($_POST['shoulder_strap']) ? $_POST['shoulder_strap'] : '' ?>" min="0" max="9" step="1">
    </div> 
    <div class="column">    
      <label for="fixed_hand_strap">Fixed Hand Strap:</label>
      <input type="number" name="fixed_hand_strap" id="fixed_hand_strap" value="<?php echo isset($_POST['fixed_hand_strap']) ? $_POST['fixed_hand_strap'] : '' ?>"  min="0" max="9" step="1">

      <label for="divider_horizontal">Divider Horizontal:</label>
      <input type="number" name="divider_horizontal" id="divider_horizontal" value="<?php echo isset($_POST['divider_horizontal']) ? $_POST['divider_horizontal'] : '' ?>" min="0" max="9" step="1">
    </div>
  </div>
  <div class="row">
    <div class="column">
      <label for="divider_vertical">Divider Vertical:</label>
      <input type="number" name="divider_vertical" id="divider_vertical" value="<?php echo isset($_POST['divider_vertical']) ? $_POST['divider_vertical'] : '' ?>" min="0" max="9" step="1">
     
     
      <label for="corners">Corners:</label>
      <input type="number" name="corners" id="corners" value="<?php echo isset($_POST['corners']) ? $_POST['corners'] : '0' ?>" min="0" max="8" step="1" disabled>
<input type="hidden" name="corners_hidden" id="corners_hidden" value="<?php echo isset($_POST['corners']) ? $_POST['corners'] : '0' ?>">

    </div> 
    <div class="column">
      <label for="wheels">Wheels:</label>
      <input type="number" name="wheels" id="wheels" value="<?php echo isset($_POST['wheels']) ? $_POST['wheels'] : '' ?>" min="0" max="8" step="1">
  
      <label for="wheels_360">Wheels 360:</label>
      <input type="number" name="wheels_360" id="wheels_360" value="<?php echo isset($_POST['wheels_360']) ? $_POST['wheels_360'] : '' ?>" min="0" max="8" step="1">
    </div>
  </div> 

  <!-- Checkboxes in rows -->
  <div class="checkbox-row">
    <div class="checkbox-column">
      <label for="backpack" title="agrega mochila">Backpack:</label>
      <input type="checkbox" name="backpack" id="backpack" value="1" <?php echo isset($_POST['backpack']) ? "checked" : ""; ?>>
    </div>
    <div class="checkbox-column">
      <label for="pocket" title="el modelo tiene bolsillo?">Pocket:</label>
      <input type="checkbox" name="nylon_pocket_large_qty" id="pocket" value="1" <?php echo isset($_POST['nylon_pocket_large_qty']) ? "checked" : ""; ?>>
    </div>
  </div> 

  <div class="checkbox-row">
    <div class="checkbox-column">
      <label for="extra_wood" title="para parlantes">Extra Wood:</label>
      <input type="checkbox" name="extra_wood" id="extra_wood" value="1" <?php echo isset($_POST['extra_wood']) ? "checked" : ""; ?> >
    </div>
    <div class="checkbox-column">
      <label for="telescopic_handle" title="tiene telescopica?">Telescopic Handle:</label>
      <input type="checkbox" name="telescopic_handle" id="telescopic_handle" value="1" <?php echo isset($_POST['telescopic_handle']) ? "checked" : ""; ?>>
    </div>
  </div>

  <div class="checkbox-row">
    <div class="checkbox-column">
      <label for="embroidery" title="lleva bordado?">Embroidery:</label>
      <input type="checkbox" name="embroidery" id="embroidery" value="1" <?php echo isset($_POST['embroidery']) ? "checked" : ""; ?>>
    </div>
    <div class="checkbox-column">
      <label for="custom" title="agrega 20% de lucro al MSRP">Custom:</label>
      <input type="checkbox" name="custom" id="custom" value="1"  <?php echo isset($_POST['custom']) ? "checked" : ""; ?>>
    </div>
  </div>
  

</section>


  <div class="button" id="prev">&larr; Previous</div>
<div class="button" id="next">Next &rarr;</div>
<input style="width:10%" type="submit" id="submit" class="button" name="add_product" value="Add Product">
<!--<div class="button" id="submit">Agree and send application</div> -->

</form>




    
        
    

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
$( document ).ready(function() {
  $("#length").unbind("keyup change ").bind("keyup change ",function(e){
    var $this = $(this);
    var val = $this.val();
    var valLength = val.length;
    var maxCount = $this.attr('max');
    if(valLength>maxCount){
        $this.val($this.val().substring(0,maxCount));
    }
}); 
$("#width").unbind("keyup change ").bind("keyup change ",function(e){
    var $this = $(this);
    var val = $this.val();
    var valLength = val.length;
    var maxCount = $this.attr('max');
    if(valLength>maxCount){
        $this.val($this.val().substring(0,maxCount));
    }
});
$("#height").unbind("keyup change e").bind("keyup change ",function(e){
    var $this = $(this);
    var val = $this.val();
    var valLength = val.length;
    var maxCount = $this.attr('max');
    if(valLength>maxCount){
        $this.val($this.val().substring(0,maxCount));
    }
}); 
$("#internal_measurements").unbind("keyup change ").bind("keyup change ",function(e){
    var $this = $(this);
    var val = $this.val();
    var valLength = val.length;
    var maxCount = $this.attr('max');
    if(valLength>maxCount){
        $this.val($this.val().substring(0,maxCount));
    }
});
var base_color = "rgb(230,230,230)";
var active_color = "rgb(237, 40, 70)";


var child = 1;
var length = $("section").length - 1;
$("#prev").addClass("disabled");
$("#submit").addClass("disabled");

$("section").not("section:nth-of-type(1)").hide();
$("section").not("section:nth-of-type(1)").css('transform','translateX(100px)');

var svgWidth = length * 200 + 24;
$("#svg_wrap").html(
  '<svg version="1.1" id="svg_form_time" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 ' +
    svgWidth +
    ' 24" xml:space="preserve"></svg>'
);

function makeSVG(tag, attrs) {
  var el = document.createElementNS("http://www.w3.org/2000/svg", tag);
  for (var k in attrs) el.setAttribute(k, attrs[k]);
  return el;
}

for (i = 0; i < length; i++) {
  var positionX = 12 + i * 200;
  var rect = makeSVG("rect", { x: positionX, y: 9, width: 200, height: 6 });
  document.getElementById("svg_form_time").appendChild(rect);
  // <g><rect x="12" y="9" width="200" height="6"></rect></g>'
  var circle = makeSVG("circle", {
    cx: positionX,
    cy: 12,
    r: 12,
    width: positionX,
    height: 6
  });
  document.getElementById("svg_form_time").appendChild(circle);
}

var circle = makeSVG("circle", {
  cx: positionX + 200,
  cy: 12,
  r: 12,
  width: positionX,
  height: 6
});
document.getElementById("svg_form_time").appendChild(circle);

$('#svg_form_time rect').css('fill',base_color);
$('#svg_form_time circle').css('fill',base_color);
$("circle:nth-of-type(1)").css("fill", active_color);

 
$(".button").click(function () {
  $("#svg_form_time rect").css("fill", active_color);
  $("#svg_form_time circle").css("fill", active_color);
  var id = $(this).attr("id");
  if (id == "next") {
    $("#prev").removeClass("disabled");
    if (child >= length) {
      $(this).addClass("disabled");
      $('#submit').removeClass("disabled");
    }
    if (child <= length) {
      child++;
    }
  } else if (id == "prev") {
    $("#next").removeClass("disabled");
    $('#submit').addClass("disabled");
    if (child <= 2) {
      $(this).addClass("disabled");
    }
    if (child > 1) {
      child--;
    }
  }
  var circle_child = child + 1;
  $("#svg_form_time rect:nth-of-type(n + " + child + ")").css(
    "fill",
    base_color
  );
  $("#svg_form_time circle:nth-of-type(n + " + circle_child + ")").css(
    "fill",
    base_color
  );
  var currentSection = $("section:nth-of-type(" + child + ")");
  currentSection.fadeIn();
  currentSection.css('transform','translateX(0)');
 currentSection.prevAll('section').css('transform','translateX(-100px)');
  currentSection.nextAll('section').css('transform','translateX(100px)');
  $('section').not(currentSection).hide();
});

//make the max digit to 5 in lenght fieldfor all feilds in step 3


});

</script>
</body>
</html>