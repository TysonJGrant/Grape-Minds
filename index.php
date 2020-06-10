<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!--<script src = "scripts.js"></script>-->
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <title>Grape Minds</title>
</head>

<?php 
  require_once 'db_connect.php';
  include 'class-wine.php';
  include 'navigation_bar.php';
?>

<body>
<div id='message'></div>
<h1>Grape Wines Drink Alike</h1>

<form method="post" onsubmit="add_wine()">
  <table>
    <tr><td>
      <input type="text" placeholder="Label Description" name="name" id="name" required>
    </tr></td>
    <tr><td>
      <label for="brand">Brand:</label>
      <select style="width: 36%; float: none" name="brand" id="brand"><?php 
        if ($results = $mysqli-> query("SELECT DISTINCT brand FROM wines")) {
          foreach($results as $result){
            echo "<option>".$result['brand']."</option>";
          }
        }?>
      </select>
      <input type="text" style="width: 36%" placeholder="Or enter your own" name="new_brand" id="new_brand">
    </tr></td>
    <tr><td>
      <label for="original_picture">Picture:</label>
      <input type="button" value="Add Image" onclick="take_image()">
      
      <canvas type="file" id="picture" width="720" height="200" style="border:1px solid #d3d3d3;">      
    </tr></td>  
    <tr><td>
      <label for="strength">Alc/Vol (%):</label>
      <input type="number" name="strength" id="strength" value="14" step="0.5" required>
    </tr></td>
    <tr><td>
      <label for="volume">Size (mL):</label>
      <input type="number" name="volume" id="volume" value="750" step="125" required>
    </tr></td>  
    <tr><td>
      <label for="type">Type:</label>
      <select name="type" id="type">
        <option>White</option>
        <option>Red</option>
        <option>Rosé</option>
        <option>Other</option>
      </select>
    </tr></td>
    <tr><td>
    <tr><td>
      <label for="brand">Subtype:</label>
      <select style="width: 36%; float: none" name="subtype" id="subtype"><?php 
        if ($results = $mysqli-> query("SELECT DISTINCT subtype FROM wines")) {
          foreach($results as $result){
            echo "<option>".$result['subtype']."</option>";
          }
        }?>
      </select>
      <input type="text" style="width: 36%" placeholder="Or enter your own" name="new_subtype" id="new_subtype">
    </tr></td>
    <tr><td>
      <label for="price">Price ($):</label>
      <input type="number" name="price" id="price" required>
    </tr></td>
    <tr><td><br/>
      <input type="button" class="submit" value="Add Wine" onclick="add_wine()">
    </tr></td>
    
    
  </table>
</form>
<br/><hr><br/>
<?php 

echo "<h2>The Goodest Wines</h2>";
$query = "SELECT wine_id FROM wines";
if ($results = $mysqli-> query($query)) {
  echo "<table>";
  $name = NULL;
  foreach($results as $result){
    $wine = new Wine($result['wine_id']);
    if($wine->get_wine_info() != NULL){
      $wine_info = $wine->get_wine_info();
      echo "<tr><th colspan='2'>".$wine_info['wine_id'].": ".$wine_info['brand']."</th></tr><tr>";
      $rowspan = ($wine->ratings_count() >= 1) ? $wine->ratings_count() : 1;
      echo "<td rowspan='".$rowspan."'><img src='img.png'></td>";//format 300x300
    }
    if($wine->get_ratings() != NULL){
      $ratings = $wine->get_ratings();
      $i = 0;
      foreach($ratings as $rating){
        if($i>=1){echo "<tr>";}//start new row
        echo "<td>".$rating['ranker']." gave this wine ".$rating['rating']."/10.";
        if(isset($rating['comments'])){
          echo "<br/><i>'".$rating['comments']."'</i>";
        }
        echo "</td></tr>";
      }
    }else{
      echo "<td>This wine is missing a rating! Add your thoughts here:</td></tr>";
    }    
  }
  echo "</table>";
} 

?>

<script>
    var dataURL;
    var c = document.getElementById("picture");  //Canvas
    var ctx = c.getContext("2d");
    c.style.display="none";
    
    function take_image(){  //Creates a file upload button and clicks it. that way can style our own button onstead of custom file type button
        var input = document.createElement("input");
        input.type = "file";
        input.addEventListener('change', add_to_canvas);
        input.click();
    }
    
    function add_to_canvas(e){
        var img = new Image();
        img.src = URL.createObjectURL(e.target.files[0]);
        img.onload = function() {
            console.log(img.width);
            console.log(img.height);
            ratio = img.height/img.width;
            c.height = c.width*ratio;
            console.log(ratio);
            console.log(c.height);
            ctx.drawImage(img, 0, 0, c.width, c.height);
            dataURL = c.toDataURL();
            c.style.display="";
        }
    }

    function add_wine(){    //Sends all form information to php file to save
      //add form fill checks before calling php page
        brand = document.getElementById('new_brand').value;
        if(brand == ""){
          brand = document.getElementById("brand").value;
        }
        subtype = document.getElementById('new_subtype').value;
        if(subtype == ""){
          subtype = document.getElementById("subtype").value;
        }
        ajaxurl = 'add_wine.php',
        data =  {
          'name': document.getElementById("name").value,
          'brand': brand,
          'picture': dataURL,
          'strength': document.getElementById("strength").value,
          'volume': document.getElementById("volume").value,
          'type': document.getElementById("type").value,
          'subtype': subtype,
          'price': document.getElementById("price").value
        };
        $.post(ajaxurl, data, function (response) {
            console.log(response);
            console.log("done");
            span = document.createElement('span');
            span.innerHTML = "Wine Saved";
            document.getElementById('message').appendChild(span);
            window.scrollTo(0, 0);
        });
    }
</script>

</body>