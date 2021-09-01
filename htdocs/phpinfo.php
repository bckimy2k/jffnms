<?php 

   $host = "localhost";
   $db = "jffnms";
   $user= 'jffnms';
   $password = 'tps123!@#';
   $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
	$pdo = new PDO($dsn, $user, $password);

	if ($pdo) {
		echo "Connected to the $db database successfully!";
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
?>
<p>
<?php
    print_r(get_loaded_extensions());
?>
</p>


