<?php
class foglalasok
{
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "ardaimark";
	private $conn = NULL;
	private $sql = NULL;
	private $result = NULL;
	private $row = NULL;  	public function csatlakozas()
	{
		$this->conn =new mysqli($this->servername,
								$this->username,
								$this->password,
								$this->dbname);
		if($this->conn->connect_error){
			
			die("Connection failed: ".$this->conn->connect_error);
		}
		$this->conn->set_charset("utf8");
		
	}
	public function listazas(){
		
		$this->csatlakozas();
		$this->sql = "SELECT * FROM foglalasok;";
		
		$this->result = $this->conn->query($this->sql);
		if($this->result->num_rows > 0){
			while($this->row = $this->result->fetch_assoc()){
				echo "<h3>".$this->row["vnev"]."</h3>"
				."<p>".$this->row["sznev"]."</p>"
				."<p> időpont:".$this->row["erk"]
				." - " .$this->row["tav"]."</p>"
				."<p>".$this->row["fo"]." fő</p>"
				."<p>fizetve: ".$this->row["fizetve"]." </p>";
			}
			
		}
		else{
			echo "Nincs foglalás";
		}
		$this->kapcsolatbontas();
		
	}
	
	public function modositas(){
		$this->csatlakozas();
		if (isset($_GET['fizetve'])){
			$this->sql = "UPDATE foglalasok
						 SET fizetve = 'igen'
						 WHERE fsorsz = '".$_GET["fizetve"]."';";
						 
			if($this->conn->query($this->sql)){
				
				echo "Sikeres módosítás!";
				
			}else{
				
				echo "Hiba: " .$this->conn->error;
			}
			
		}
		
		if (isset($_GET['nincsfizetve'])){
			$this->sql = "UPDATE foglalasok
						  SET fizetve = 'nem'
						  WHERE fsorsz = '".$_GET["nincsfizetve"]."';";
			if($this->conn->query($this->sql)){
				echo "Sikeres módosítás!";
				
			}
				else{
					echo "Hiba: " .$this->conn->error;
				}
			
		}
		
		$this->sql = "SELECT fsorsz, vnev, sznev, erk, tav, fo, fizetve FROM foglalasok;";
		
		$this->result = $this->conn->query($this->sql);
		if($this->result->num_rows > 0){
			while($this->row = $this->result->fetch_assoc()){
				echo '<div class = "col-sm-3" style = "border: 3px solid lightgray; margin: 2px;">';
				echo "<h3>".$this->row["vnev"]."</h3>"
				."<p>".$this->row["sznev"]."</p>"
				."<p> időpont:".$this->row["erk"]
				." - " .$this->row["tav"]."</p>"
				."<p>".$this->row["fo"]." fő</p>"
				."<p>fizetve: ".$this->row["fizetve"]." </p>";
				echo '<a class= "btn btn-success" href = "index.php?oldal=modositas&fizetve="'.
				$this->row["fsorsz"].'">Fizetve</a>&nbsp;&nbsp;';
				echo '<a class= "btn btn-warning" href = "index.php?oldal=modositas&nincsfizetve="'.
				$this->row["fsorsz"].'"> Nincs Fizetve</a>';
				echo "</div>";
			}
			
		}
		else{
			echo "Nincs foglalás!";
			
		}
		$this->kapcsolatbontas();
		
	}
	
	public function felvetel(){
		$this->csatlakozas();
		
		if(isset($_POST["submit"])){
			$fizetve ='nem';
			if(isset($_POST["fizetve"])) $fizetve = 'igen';
			$this->sql = "INSERT INTO foglalasok (fsorsz,vnev, sznev, erk, tav, fo, fizetve)
				VALUES (NULL,'".$_POST["vnev"]."',
				'".$_POST["sznev"]."',
				'".$_POST["erk"]."',
				'".$_POST["tav"]."',
				'".$_POST["fo"]."',
				'".$fizetve."')";
			if($this->conn->query($this->sql)){
				
				echo"Sikeres Mentés!";
				
			}
			else{
				echo"Hiba: ".$this->conn->error;
			}
			$this->kapcsolatbontas();
		}
		?>
			<script>
				function Ellenorzes(){
					if(documents.getElementById("vnev").value == "" ||
						documents.getElementById("sznev").value == "" ||
						documents.getElementById("erk").value == "" ||
						documents.getElementById("tav").value == "" ||
						documents.getElementById("fo").value == "" 
						)
						{
							documents.getElementById("uzenet").innerHTML ="Tölts ki minden mezőt!";
								return false;
						}
							return true;
				}
			</script>
			<form method = "POST" onsubmit = "return Ellenoriz();">
				<div class="form-group">
				  <label for="vnev">Vendég neve:</label>
				  <input type="text" class="form-control" name = "vnev" id="vnev">
				</div>
				<div class="form-group">
				  <label for="sznev">Szoba neve:</label>
				  <select class="form-control" name = "sznev" id="sznev">
					<option value = "Szende">Szende</option>
					<option value = "Szundi">Szundi</option>
					<option value = "Morgó">Morgó</option>
					<option value = "Hapci">Hapci</option>
					<option value = "Tudor">Tudor</option>
					<option value = "Vidor">Vidor</option>
					<option value = "Kuka">Kuka</option>
				  </select>
				</div>
				<div class="form-group">
				  <label for="erk">Érkezés:</label>
				  <input type="date" class="form-control" name = "erk" id="erk">
				</div>
				<div class="form-group">
				  <label for="tav">Távozás:</label>
				  <input type="date" class="form-control" name = "tav" id="tav">
				</div>
				<div class="form-group">
				  <label for="fo">Vendégek száma:</label>
				  <input type="number" class="form-control" name = "fo" id="fo">
				</div>
				<div class="form-group">
				  <label for="fizetveigen">Fizetve:</label>
				  <input type = "checkbox" class="form-control" name = "fizetve" id="fizetveigen" value = 'igen'>
				</div>
				<div class="form-group">
				  <input type="submit" name = "submit">
				</div>
				<p id = "uzenet"></p>
			</form>
			
		<?php
	}
	
	public function kapcsolatbontas(){
	
	$this->conn->close();
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Családi Fogadó</title>
	<meta charset="utf-8">
			
	
</head>
<body>
	<div class = "container">
		<div class "row">
			<div class ="col-sm-12">
			<h3>Családi Fogadó</h3>
			</div>
		</div>
		<div class="row">
		  <div class="col-sm-12">
		  	<a href="index.php?oldal=listazas">Foglalások</a> 
		  	<a href="index.php?oldal=felvetel">Foglalás Felvétel</a>
		  	<a href="index.php?oldal=modositas">Foglalás Módosítás</a>
		  </div>
		</div>
		<div class="row">
			<?php
			$run = new foglalasok();
			if (isset($_GET["oldal"])) {
				if ($_GET["oldal"]=="listazas") {$run->listazas();}
				else if($_GET["oldal"] =="felvetel"){$run->felvetel();}
				else{$run->modositas();}
				
			}
			else {
				$run->listazas();
			}
			?>
		</div>
	</div>
</body>
</html>
