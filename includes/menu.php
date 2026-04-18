<?php
//create a mysql connection 
$con=get_connection();
// Check connection

?>

<?php

function get_menu_tree($parent_id,$web_path) 
{
  global $con;
  $role_id="";
  if(isset($_SESSION['ROLEID'])){
    $role_id=$_SESSION['ROLEID'];
  }
  $menu = "";
  
    $sqlquery = " SELECT * FROM menu where status='1' and parent_id=? and user_role=? ";
  // echo $sqlquery;
	$res=db_query($sqlquery, [$parent_id, $role_id]);
    while($row=db_fetch($res)) 
	{
           $menu .="<li><a href='".$web_path.$row['link']."?menu_id=".$row['menu_id']."'>".$row['menu_name']."</a>";
		   
		   $menu .= "<ul>".get_menu_tree($row['menu_id'],$web_path)."</ul>"; //call  recursively
		   
 		   $menu .= "</li>";
 
    }
    
    return $menu;
} 
?>

<ul class="main-navigation">
<?php echo get_menu_tree(0,$web_path);//start from root menus having parent id 0 ?>

</ul> 

