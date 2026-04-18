<?php
//create a mysql connection 
$con=get_connection();
// Check connection

if (!function_exists('get_menu_tree')) {
    function get_menu_tree($parent_id,$web_path) 
    {
      global $con;
      $role_id="";
      if(isset($_SESSION['ROLEID'])){
        $role_id=$_SESSION['ROLEID'];
      }
      $menu = "";
      
        $sqlquery = " SELECT * FROM menu where status='1' and parent_id='$parent_id' and user_role='$role_id' ";
      // echo $sqlquery;
        $res=mysqli_query($con,$sqlquery);
        while($row=mysqli_fetch_array($res,MYSQLI_ASSOC)) 
        {
               $menu .="<li><a href='".$web_path.$row['link']."?menu_id=".$row['menu_id']."'>".$row['menu_name']."</a>";
               
               $submenu = get_menu_tree($row['menu_id'],$web_path);
               if($submenu != "") {
                   $menu .= "<ul>".$submenu."</ul>";
               }
               
               $menu .= "</li>";
     
        }
        
        return $menu;
    } 
}
?>

<ul class="main-navigation">
<?php echo get_menu_tree(0, $web_path); // Show dynamic menu items from DB ?>

<!-- Offer Letter menu -->
<?php if (isset($_SESSION['ROLEID']) && ($_SESSION['ROLEID'] === 'admin' || $_SESSION['ROLEID'] === 'Admin')): ?>
<li>
    <a href="#">Offer Letter</a>
    <ul>
        <li>
            <a href="<?php echo $web_path; ?>offer_letter/view_offer_letters.php">View Offer Letter</a>
            <ul>
                <li><a href="<?php echo $web_path; ?>offer_letter/add_offer_letter.php">Add Offer Letter</a></li>
            </ul>
        </li>
        <li><a href="<?php echo $web_path; ?>offer_letter/search_offer_letter.php">Search Offer Letter</a></li>
        <li><a href="<?php echo $web_path; ?>offer_letter/order_confirmation.php">Order Confirmation</a></li>
        <li><a href="<?php echo $web_path; ?>offer_letter/order_confirmation_report.php">Order Confirmation Report</a></li>
        </ul>
</li>
<?php endif; ?>

</ul>
