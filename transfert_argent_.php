<?php
include "db_conn.php";

session_start();

$msg="";
$solde = 0;
$montant=0;
$name_e=$_SESSION['name'];
$name_r="";
$numcp_e=$_SESSION['numcp'];
$numcp_r="";
$solde_e=$_SESSION['solde'];
$solde_r=0;

if ($numcp_e!="") {
        
        $query = "SELECT * FROM compte where numcp = '$numcp_e'";
        
        $res = $conn->query($query);
        $result = $res->fetch_assoc();

        
        $solde_e = $result['solde'];
        $name_e = ucfirst($result['prenom'])." ".ucfirst($result['nom']);
        
    }


if (isset($_POST['verif_compte'])){

    $query = "SELECT * FROM compte where numcp = '".$numcp_e."'";
    $res = $conn->query($query);
    $result = $res->fetch_assoc();

    $prenom = ucfirst($result['prenom']);
    $nom= ucfirst($result['nom']);
    $solde = $result['solde'];
    $name= $prenom." ".$nom;
    $numcp=$_POST['numcp'];
}

if (isset($_POST['chercher'])){

$numcp_r=$_POST['numcp_r'];

    

    if ($numcp_r!="") {
        
        $query = "SELECT * FROM compte where numcp = '$numcp_r'";
        $res = $conn->query($query);
        $result = $res->fetch_assoc();

       
        $solde_r = $result['solde'];
        $name_r = ucfirst($result['prenom'])." ".ucfirst($result['nom']);
        
    }



}


if (isset($_POST['transfert'])){

    
    $numcp_r=$_POST['numcp_r'];


   

    $montant_t=$_POST['montant_t'];

    $query = "SELECT * FROM compte where numcp = '$numcp_e'";
    $res = $conn->query($query);
    $result = $res->fetch_assoc();

    $prenom=$result['prenom'];
    $nom=$result['nom'];
    $solde_e=$result['solde'];
    $name_e = ucfirst($result['prenom'])." ".ucfirst($result['nom']);


    $query = "SELECT * FROM compte where numcp = '$numcp_r'";
    $res = $conn->query($query);
    $result = $res->fetch_assoc();

    $solde_r=$result['solde'];
    $name_r = ucfirst($result['prenom'])." ".ucfirst($result['nom']);

    $solde_e = $solde_e - $montant_t;
    $solde_r = $solde_r + $montant_t;

    $_SESSION['solde']=$solde_e;
    $query_e = "UPDATE compte set solde=$solde_e where numcp = $numcp_e";
    $query_r = "UPDATE compte set solde=$solde_r where numcp = $numcp_r";
    
    if (mysqli_query($conn,$query_e) && mysqli_query($conn,$query_r)){

        $date = date("Y-m-d H:i:s"); 

        $query_operation = "INSERT INTO operation (numcp,prenom,nom,type,mentant,date,numcp2) 
                            VALUE ('$numcp_e','$prenom','$nom','Transfer',$montant_t,'$date','$numcp_r')";
        
        if (mysqli_query($conn,$query_operation)){
            $msg="Operation reussite !";

        }else {
            $msg="";
        }
    }



}

?>

<!DOCTYPE html>
<html> 

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=".\css\style.css">
    <link rel="stylesheet" href=".\css\form.css">

</head>

<body>
     <script type="text/javascript">
    


        function valider_transfert(){
            var name_e = document.getElementById('name_e').value;
            var name_r = document.getElementById('name_r').value;
           

            if (name_e != "" && name_r != "" && name_r!=name_e){

                document.getElementById('montant_t').disabled=false;
                document.getElementById('transfert').disabled=false;

            }else {
                alert("Information incorrect !");
                document.getElementById('montant_t').disabled=true;
                document.getElementById('transfert').disabled=true;
            }
           
        }
        

        function valider_montant(){
            // if (document.getElementById('chercher').clicked){

               
            var name_e = document.getElementById('solde_e').value;
            var montant = document.getElementById('montant_t').value;
            var solde_e = document.getElementById('solde_e').value;

            if (isNaN(montant) || montant =="" || montant < 1){
                alert('information incorrect !');
                return false;

            } 

            else if (solde_e - montant < 0){
                alert('Solde insifisant !');
                return false;

            } 
            else {

            return true;

            }


           


        }
    </script>

    <div class="navbar">

        <a href="index.php"><img src=".\img\log.png"></a>
        <a href="profil_.php"><img src=".\img\profile.png"></a>




       
        <a href="Accueil_.php">Accueil</a>



    </div>

    <div class="sidebar"><br><br><br>
        <a href="operation_.php">Suivie les Operations</a>
        <br>
        <a href="transfert_argent_.php">Transferer d'argent</a>
        <br>
        <a href="demande_carnet_cheque_.php">Demander un carnet de chéque</a>
        <br>
        
        <a href="reclamation_.php">Ajouter une reclamation</a>
        <br>
        

    </div>
    <div class="content">





 <fieldset align=center>
            <legend>Transfet d'argent</legend>
           
            <form class="form-style-1" method="POST" action="transfert_argent_.php"  >
                <table align="left" border="0">

                    <tr>
                        <td colspan=2 align="left">
                           <input type="button" value="Valider"  onclick="valider_transfert()" >
                        </td>
                        <td style="text-align: left;">
                            <input type="submit" value="chercher" name="chercher" id="chercher" >
                        </td>
                        <td style="min-width:195px">

                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 style="text-align: left;"> <br>
                            <input type="text" id="numcp_e" name="numcp_e" disabled value="<?php echo $numcp_e; ?>" placeholder="n° Compte E">
                        </td>
                        <td align="left"> <br>
                            <input type="text" name="name_e" id="name_e" value="<?php echo $name_e; ?>" disabled >
                            <input type="hidden" name="solde_e" id="solde_e" value="<?php echo $solde_e; ?>">
                        </td>
                        <td style="text-align: left;"><br>
                            <input type="text" name="solde_e" id="solde_e" value="<?php echo $solde_e;?>" 
                            style="max-width: 100px; text-align: right;"disabled>
                        </td>
                        <td style="text-align: right"><br>
                            <input type="text" id="montant_t" name="montant_t" placeholder="Montant" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 style="text-align: left;">
                            <input type="text" id="numcp_r" name="numcp_r" value="<?php echo $numcp_r; ?>" placeholder="n° Compte R">
                        </td>
                        <td align="left">
                             <input type="text" id="name_r" name="name_r" value="<?php echo $name_r; ?>" disabled >
                             
                        </td>
                        <td style="text-align: left;">
                            <input type="text" name="solde_r" id="solde_r" value="<?php echo $solde_r;?>" 
                            style="max-width: 100px; text-align: right;" disabled>
                        </td>
                        <td align="right"><br>
                            <input type="submit" name="transfert" id="transfert" value="Transfer" onclick="return valider_montant()" disabled>
                        </td>
                    </tr>

                </table>
            </form>

        </fieldset>
        <br>


                <fieldset><br>
            <center><h2><?php echo $msg; ?></h2></center><br>
            <table class="s" style="padding-left: 0px" width="100%">

                <thead>
                    <td colspan=8>
                        Gestion des Operations
                    </td>
                </thead>


                <tr  style="background-color: #F7A570;">
                    <td  style='max-width: 40px;'>
                        Num Operation
                    </td>

                    <td  style='max-width: 40px;'>
                        Num compte E
                    </td>
                    <td>
                        Prenom
                    </td>
                    <td>
                        Nom
                    </td>
                    <td>
                        Type
                    </td>
                    <td  style='max-width: 40px;'>
                        Num compte R
                    </td>
                    <td>
                        Montant
                    </td>
                    <td style="min-width: 80px; ">
                        Date
                    </td>


                </tr>
<?php

    if (isset($_POST['transfert'])){

                $query = "SELECT * FROM operation  ORDER BY 1 DESC LIMIT 1";
                $res = $conn->query($query);
                $result = $res->fetch_assoc();

                echo "<tr><td>".$result['numop']."</td>";
                echo "<td>".$result['numcp']."</td>";
                echo "<td>".$result['prenom']."</td>";
                echo "<td>".$result['nom']."</td>";
                echo "<td>".$result['type']."</td>";
                echo "<td>".$result['numcp2']."</td>";
                echo "<td>".$result['mentant']."</td>";
                echo "<td>".$result['date']."</td></tr>";

                
               


    }


?>


                





            </table>

            <br><br>


        </fieldset>
















        <br><br>


</body>

</html>