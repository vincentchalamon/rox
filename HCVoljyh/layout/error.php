<?php
require_once("Menus_micha.php") ;
function DisplayError($ErrorMessage="No Error Message") {
  global $title,$errcode ;
  $title=ww('ErrorPage') ;

  include "header_micha.php" ;

	Menu1("error.php",ww('MainPage')) ; // Displays the top menu

	Menu2($_SERVER["PHP_SELF"]) ;

echo "\n<div id=\"maincontent\">\n" ;
echo "  <div id=\"topcontent\">" ;
echo "					<h3>",$errcode,"</h3>\n" ;
echo "\n  </div>\n" ;
echo "</div>\n" ;

echo "\n  <div id=\"columns\">\n" ;
//menumember("member.php?cid=".$m->id,$m->id,$NbComment) ;
echo "		<div id=\"columns-low\">\n" ;

ShowActions() ; // Show the Actions
ShowAds() ; // Show the Ads


echo "		<div id=\"columns-middle\">\n" ;
echo "			<div id=\"content\">\n" ;
echo "				<div class=\"info\">\n" ;

  echo "<table bgcolor=#ffffcc >" ;
  echo "<TR><td>",$ErrorMessage,"</TD><br>" ;
  echo "</table>" ;


echo "\n         </div>\n"; // Class info 
echo "       </div>\n";  // content
echo "     </div>\n";  // columns-midle
	

echo "   </div>\n";  // columns-low
echo " </div>\n";  // columns


echo "					<div class=\"user-content\">\n" ;
  include "footer.php" ;
echo "					\n</div>\n" ;
}
?>
