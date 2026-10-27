<?php



include_once("../../config.php");









if(isset($_GET['g']))
	{

		echo '<br /><br /><font color="Red"><b>Забрали голд , ебать ты жадный</font></b>';

	}else{

$id = $_POST['id'];



  echo $_POST['gold'];

  $gold=$_POST['gold'];

$database->query("UPDATE users SET gold = gold - " . sql_num($gold) . " WHERE id = ".sql_num($id)."");



$database->query($q);

       header("Location: ../../../panel/index.php?p=player&uid=$id");

    echo '<br /><br /><font color="Red"><b>Забрали голд , ебать ты жадный</font></b>';

}



?>