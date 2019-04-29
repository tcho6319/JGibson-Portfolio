<?php
include("includes/init.php");

// Search
const SEARCH_FIELDS = [
  "All" => "By All",
  "Available" => "By Available",
  "Outdoor" => "By Ourdoor",
  "Portrait" => "By Portrait",
  "Illustration" => "By Illustration",
  "Personal" => "By Personal",
];

if (isset($_GET['search']) && isset($_GET['category']) ) {

  $do_search = TRUE;
  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);

  if (in_array($category, array_keys (SEARCH_FIELDS) )){
    $search_field = $category;

  } else {
    $do_search = FALSE;
    array_push($messages, "Error in Selecting Search Category");
  }

  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
  $search = trim($search);


} else {
  $do_search = FALSE;
  $category = NULL;
  $search = NULL;
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/head.php");?>



<body>
  <?php $gallery="current_page"; ?>

  <?php include("includes/header.php");?>
  <div id="content-wrap">

  <?php
 foreach($messages as $message){
   echo "<strong>" . htmlspecialchars($message) . "</strong>\n";
 }
 ?>


  <form id="search_form" action="gallery.php" method="get" style="text-align:center">
      <select name="category">
        <option value="" selected disabled>Search By</option>
        <option value="All">All</option>
        <option value="Available">Available</option>
        <option value="Outdoor">Outdoor</option>
        <option value="Portrait">Portrait</option>
        <option value="Illustration">Illustration</option>
        <option value="Personal">Personal</option>
      </select>
      <input type="text" name="search"/>
      <button type="submit">Search</button>
    </form>


    <?php
  if ($do_search) {
    ?>
    <h3>Search Results</h3>
    <?php

    $sql = "SELECT * FROM gallery WHERE " . $search_field . " LIKE '%' || :search || '%'";
    $params = array(
      ':search' => $search );
  }else{
    ?>
    <h3>All Arts</h3>
    <?php

//  code
}
?>

  <h3 class="subtitle2">━━━━━ Edit Gallery ━━━━━</h3>

  <form action="gallery.php" method="post">
  <input class="center" type="submit" name="submit" value="Delete Painting">
  </form>

  <p>Add a new painting:</p>


  </div>
  <?php include("includes/footer.php");?>
</body>
</html>
