<?php
    @include("konekcija.php");
?>

<html>
    <head>
        <title>Social Network</title>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    </head>
    <body>
        <div id="wrapper">
            <div id="list">
       <?php
            if(isset($_GET['id'])){
                
                $niz = array();
                
                $getname = "SELECT * FROM users WHERE id=".$_GET['id'];
                $res = mysql_query($getname);
                $name = mysql_fetch_array($res);
                
                echo "<h1>".$name['firstname']." ".$name['surname']."</h1>";
                
                echo "<div class=\"heading\">
                        <h3>Friends</h3>
                    </div>";
                
                // DIRECT FRIENDS
                
                $upit = "SELECT * FROM users WHERE id IN (SELECT n.num_id FROM users u INNER JOIN relationships r ON u.id=r.id INNER JOIN num_of_friends n ON n.num_id = r.num_id WHERE r.id =".$_GET['id'].")";
                $rez = mysql_query($upit);
                echo "<ul>";
                while($red = mysql_fetch_array($rez)){
                    
                    echo "<li>".$red['firstname']." ".$red['surname']."</li>";   
                    $niz[] = $red['id'];
                }
                echo "</ul>";
                
                // END
                
                echo "<div class=\"heading\">
                        <h3>Friends of friends</h3>
                    </div>";
                
                //FRIENDS OF FRIENDS
                
                $upit1 = "SELECT * FROM users WHERE id IN (SELECT n.num_id FROM users u INNER JOIN relationships r ON u.id=r.id INNER JOIN num_of_friends n ON n.num_id = r.num_id WHERE r.id IN (".implode(",",$niz)."))";
                $rez1 = mysql_query($upit1);
                echo "<ul>";
                while($red1 = mysql_fetch_array($rez1)){
                    $x=1;
                    if($red1['id'] != $_GET['id']){
                        for($i=0; $i<count($niz); $i++){
                            if($red1['id']!=$niz[$i]){
                                $x*=1;
                            }
                            else{
                                $x*=0;
                            }
                        }
                        if($x==1){
                            echo "<li>".$red1['firstname']." ".$red1['surname']."</li>";   
                        }
                    }
                }
                echo "</ul>";
                
                // END
                
                echo "<div class=\"heading\">
                        <h3>Suggested friends</h3>
                    </div>";
                
                // PUT FRIENDS OF FRIENDS IN ARRAYS
                
                $array_ofarrays = array();
                for($i=0; $i<count($niz); $i++){
                    $upit2 = "SELECT * FROM users WHERE id IN (SELECT n.num_id FROM users u INNER JOIN relationships r ON u.id=r.id INNER JOIN num_of_friends n ON n.num_id = r.num_id WHERE r.id=".$niz[$i].")";
                    $rez2 = mysql_query($upit2);
                    $array = array();
                    while($red2 = mysql_fetch_array($rez2)){
                        $x=1;
                        if($red2['id']!=$_GET['id']){
                            for($j=0; $j<count($niz); $j++){
                                if($red2['id']!=$niz[$j]){
                                    $x*=1;
                                }
                                else{
                                    $x*=0;
                                }
                            }
                            if($x==1){
                                $array[] = $red2['id'];   
                            }
                            
                        }
                        
                    }
                    // ARRAY OF ARRAYS
                    $array_of_arrays[] = $array;
                }
                
                // SUGGESTED FRIENDS IN ONE ARRAY
                
                $suggested = array();
                for($i=0; $i<count($array_of_arrays)-1; $i++){
                    for($j=$i+1;$j<count($array_of_arrays);$j++){
                        
                        $result = array_intersect($array_of_arrays[$i], $array_of_arrays[$j]);
                        foreach($result as $value){
                            $suggested[] = $value;
                        } 
                    }
                }

                // LIST OF SUGGESTED FRIENDS
                
                $upit3 = "SELECT * FROM users WHERE id IN (".implode(",",$suggested).")";
                $rez3 = mysql_query($upit3);
                echo "<ul>";
                while($red3 = @mysql_fetch_array($rez3)){
                    echo "<li>".$red3['firstname']." ".$red3['surname']."</li>";
                }
                echo "</ul>";  
                
                // END
            }
            else{
                
                //THE HOME PAGE, WHERE NO ONE IS CHOSEN
                
                echo "<h1>Social Network</h1>";
                
                $sql = "SELECT * FROM users";
                $res = mysql_query($sql, $conn);
                if(mysql_num_rows($res)>0){
                    echo "<ul>";
                    while($row = mysql_fetch_array($res)){
                        echo "<li><a href='index.php?id=".$row['id']."'>".$row['firstname']." ".$row['surname']."</a></li>";
                    }
                    echo "</ul>";
                }
            }
        ?> 
            </div>
        </div>   
    </body>
</html>