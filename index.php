<?php

$_SESSION['Team']=Array();

session_start();
$pokemon=getPokemon("AllPokemon.csv");
array_shift($pokemon); 
$Usage0=getUsage("UsageRegD.csv");
$_SESSION['Usage0'] = $Usage0;
// $Usage0=getUsage("Usage0.csv");
// $_SESSION['Usage0'] = $Usage0;
// $Usage1500=getUsage("Usage1500.csv");
// $_SESSION['Usage1500'] = $Usage1500;
// $Usage1630=getUsage("Usage1630.csv");
// $_SESSION['Usage1630'] = $Usage1630;
// $Usage1760=getUsage("Usage1760.csv");
// $_SESSION['Usage1760'] = $Usage1760;

$Moveset0=getMovesetRigth("MoveSetRegD.csv");
$_SESSION['Moveset0'] = $Moveset0;
// $Moveset0=getMovesetRigth("Moveset0.csv");
// $_SESSION['Moveset0'] = $Moveset0;
// $Moveset1500=getMovesetRigth("Moveset1500.csv");
// $_SESSION['Moveset1500'] = $Moveset1500;
// $Moveset1630=getMovesetRigth("Moveset1630.csv");
// $_SESSION['Moveset1630'] = $Moveset1630;
// $Moveset1760=getMovesetRigth("Moveset1760.csv");
// $_SESSION['Moveset1760'] = $Moveset1760;





$_SESSION['highestStat'] = 0;
$_SESSION['lowestStat'] = 0;
$_SESSION['highestType'] = 0;
$_SESSION['lowestType'] = 0;
$_SESSION['highestUsage'] = 0;
$_SESSION['lowestUsage'] = 0;
$_SESSION['highestTeammates'] = 0;
$_SESSION['lowestTeammates'] = 101;




$Pokemon0=eliminatePokemonNotLegalInTheFormat($pokemon,$Moveset0);
$_SESSION['Pokemon0'] = $Pokemon0;
// $Pokemon0=eliminatePokemonNotLegalInTheFormat($pokemon,$Moveset0);
// $_SESSION['Pokemon0'] = $Pokemon0;
// $Pokemon1500=eliminatePokemonNotLegalInTheFormat($pokemon,$Moveset1500);
// $_SESSION['Pokemon1500'] = $Pokemon1500;
// $Pokemon1630=eliminatePokemonNotLegalInTheFormat($pokemon,$Moveset1630);
// $_SESSION['Pokemon1630'] = $Pokemon1630;
// $Pokemon1760=eliminatePokemonNotLegalInTheFormat($pokemon,$Moveset1760);
// $_SESSION['Pokemon1760'] = $Pokemon1760;
$_SESSION['Team']=Array();
$typeChart=getTypeChart("typeChart.csv");
$_SESSION['TypeChart'] = $typeChart;


echo '<html>
<body>

<h2>Write a Pokemon To Select a Team</h2>

<form action="/TFG/selectPokemon.php">
  <label for="name">Pokemon:</label><br>
  <input type="text" id="name" name="name" value="PokemonName"><br>
  <label for="team">Choose a team type:</label>
  <select id="mStats" name="mStats">
    <option value="HyperOfense">HyperOfense</option>
    <option value="Balanced">Balanced</option>
    <option value="Defensive">Defensive</option>
  </select>
    <input type="submit">
</form> 
</body>
</html>';







function getOptionsIntoArrays($nameFile){
    $nameArray= Array();
    $fileHandler = fopen($nameFile, 'r');
    while ($line = fgets($fileHandler)){
        if(!str_contains($line, "+")){
            array_push($nameArray, $line);
        }
    }

    fclose($fileHandler);

    return $nameArray;
}

function getPokemon($namefile){
    $pokemon = getOptionsIntoArrays($namefile);
    $pokemonExplode= Array();
    for($i=0; $i<count($pokemon); $i++){
    
        $pokemonExplode[$i]=explode(",",$pokemon[$i]);
        // echo "<p><br></p>";
        // var_dump($pokemonExplode[$i]);
        if($pokemonExplode[$i][2]!=""){
            if(str_contains($pokemonExplode[$i][2],"Alolan")){
                $pokemonExplode[$i][2]="Alola";
            }
            if(str_contains($pokemonExplode[$i][2],"Galarian ")){
                $pokemonExplode[$i][2]="Galar";
            }
            if(str_contains($pokemonExplode[$i][2],"Hisuian")){
                $pokemonExplode[$i][2]="Hisui";
            }
            if(str_contains($pokemonExplode[$i][2],"Paldean")){
                $pokemonExplode[$i][2]="Paldea";
            }
            if(str_contains($pokemonExplode[$i][2],"Incarnate")){
                $pokemonExplode[$i][2]="";
            }
            if(str_contains($pokemonExplode[$i][2],"Therian")){
                $pokemonExplode[$i][2]="Therian";
            }
            if($pokemonExplode[$i][2]=="Male"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Female"){
                $pokemonExplode[$i][2]="F";
            }
            if($pokemonExplode[$i][2]=="Midday Form"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Midnight Form"){
                $pokemonExplode[$i][2]="Midnight";
            }
            if($pokemonExplode[$i][2]=="Dusk Form"){
                $pokemonExplode[$i][2]="Dusk";
            }
            if($pokemonExplode[$i][2]=="Family of Four"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Baile Style"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Pom-Pom Style"){
                $pokemonExplode[$i][2]="Pom-Pom";
            }
            if($pokemonExplode[$i][2]=="Pa'u Style"){
                $pokemonExplode[$i][2]="Pa'u";
            }
            if($pokemonExplode[$i][2]=="Sensu Style"){
                $pokemonExplode[$i][2]="Sensu";
            }
            if($pokemonExplode[$i][2]=="Full Belly Mode"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Amped Form"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Single Strike Style"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Rapid Strike Style"){
                $pokemonExplode[$i][2]="Rapid-Strike";
            }
            if($pokemonExplode[$i][2]=="Teal Mask"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Wellspring Mask"){
                $pokemonExplode[$i][2]="Wellspring";
            }
            if($pokemonExplode[$i][2]=="Hearthflame Mask"){
                $pokemonExplode[$i][2]="Hearthflame";
            }
            if($pokemonExplode[$i][2]=="Cornerstone Mask"){
                $pokemonExplode[$i][2]="Cornerstone";
            }
            if($pokemonExplode[$i][2]=="Aqua Breed"){
                $pokemonExplode[$i][2]="Paldea-Aqua";
            }
            if($pokemonExplode[$i][2]=="Blaze Breed"){
                $pokemonExplode[$i][2]="Paldea-Blaze";
            }
            if($pokemonExplode[$i][2]=="Heat Rotom"){
                $pokemonExplode[$i][2]="Heat";
            }
            if($pokemonExplode[$i][2]=="Wash Rotom"){
                $pokemonExplode[$i][2]="Wash";
            }
            if($pokemonExplode[$i][2]=="Frost Rotom"){
                $pokemonExplode[$i][2]="Frost";
            }
            if($pokemonExplode[$i][2]=="Fan Rotom"){
                $pokemonExplode[$i][2]="Fan";
            }
            if($pokemonExplode[$i][2]=="Mow Rotom"){
                $pokemonExplode[$i][2]="Mow";
            }
            if($pokemonExplode[$i][2]=="Meteor Form"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Ice Face"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Zero Form"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Curly Form"){
                $pokemonExplode[$i][2]="";
            }
            if($pokemonExplode[$i][2]=="Two-Segment Form"){
                $pokemonExplode[$i][2]="";
            }
            if(!$pokemonExplode[$i][2]=="")
            {
                $pokemonExplode[$i][1]=$pokemonExplode[$i][1]."-".$pokemonExplode[$i][2];
            }

        }
        // echo "<p><br></p>";
        // var_dump($pokemonExplode[$i][1]);
        
    }
    return $pokemonExplode;
}

function getUsage($nameFile){

    $Usage = getOptionsIntoArrays($nameFile);
    $UsageExplode=Array(); 
    for( $i=0; $i<count($Usage); $i++){

        $UsageExplode[$i]=explode("|",$Usage[$i]);
        array_shift($UsageExplode[$i]);

        for($j=0; $j<count($UsageExplode[$i]); $j++){
            $UsageExplode[$i][$j]=rtrim($UsageExplode[$i][$j]);   
            $UsageExplode[$i][$j]=ltrim($UsageExplode[$i][$j]); 
        }
        // echo  "<p><br></p>usage: ";
        // var_dump($UsageExplode[$i]);
    }
    
    return $UsageExplode;
}

function getMovesetRigth($nameFile){

    $newSection=false;
    $newSectionNum=0;
    $movesetArray=Array();
    $arrayToPush=Array();
    $aux=0;
    $fileHandler = fopen($nameFile, 'r');
    while ($line = fgets($fileHandler)){
        if(str_contains($line, "+")){
            // echo "<p><br></p>SectionNum: ".$newSectionNum;
            switch ($newSectionNum) {
                case '0':
                    //name
                    $name="";

                    $pokemonArray=Array(
                    "Name" => "",
                    "Usage" => Array(),
                    "Abilities" => Array(),
                    "Items" => Array(),
                    "Spreads" => Array(),
                    "Moves" => Array(),
                    "Teammates" => Array(),
                    "Checks" => Array()
                    );
                    $pokemonArray["Name"]=$arrayToPush;
                    // echo  "<p><br></p>name: ";
                    // var_dump($pokemonArray["Name"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '1':
                    //usage
                    $pokemonArray["Usage"]=$arrayToPush;  
                    // echo  "<p><br></p>usage: ";
                    // var_dump($pokemonArray["Usage"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '2':
                    //abilities
                    array_shift($arrayToPush);
                    $pokemonArray["Abilities"]=$arrayToPush;   
                    // echo  "<p><br></p>abilities: ";
                    // var_dump($pokemonArray["Abilities"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '3':
                    //Items
                    array_shift($arrayToPush); 
                    $pokemonArray["Items"]=$arrayToPush;   
                    // echo  "<p><br></p>items: ";
                    // var_dump($pokemonArray["Items"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '4':
                    //Spread
                    array_shift($arrayToPush);
                    $pokemonArray["Spreads"]=$arrayToPush;  
                    // echo  "<p><br></p>spreads: ";
                    // var_dump($pokemonArray["Spreads"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '5':
                    //moves
                    array_shift($arrayToPush);
                    $pokemonArray["Moves"]=$arrayToPush;  
                    // echo  "<p><br></p>moves: ";
                    // var_dump($pokemonArray["Moves"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '6':
                    //Teammates
                    array_shift($arrayToPush);
                    $pokemonArray["Teammates"]=$arrayToPush; 
                    // echo  "<p><br></p>teammates: ";
                    // var_dump($pokemonArray["Teammates"]);
                    $newSectionNum++;
                    $arrayToPush=Array(); 
                    break;
                case '7':
                    //checks
                    array_shift($arrayToPush);
                    $pokemonArray["Checks"]=$arrayToPush;
                    // echo  "<p><br></p>checks: ";
                    // var_dump($pokemonArray["Checks"]);
                    $arrayToPush=Array();
                    array_push($movesetArray, $pokemonArray);
                    // echo  "<p><br></p>pokemon: ";
                    // var_dump($pokemonArray); 
                    $newSectionNum++;
                    break;
                case '8':
                    $pokemonArray=Array();
                    $newSectionNum=0;   
                    break;
                default:
                    
                    break;
                
                } 
        }
        else{
            $line=str_replace("|","",$line);
            $line=rtrim($line);   
            $line=ltrim($line);   
            array_push($arrayToPush, $line);     
            // echo  "<p><br></p>linea: ";
            // var_dump($line);
           
        }
    }
    // echo  "<p><br></p>linea: ";
    // var_dump($movesetArray);
    return $movesetArray;
}

function getTypeChart($nameFile){
    $Types = getOptionsIntoArrays($nameFile);
    $TypesExplode=Array(); 
    for( $i=0; $i<count($Types); $i++){

        $TypesExplode[$i]=explode(",",$Types[$i]);
    }

    return $TypesExplode;
}

function eliminatePokemonNotLegalInTheFormat($allPokemon,$legalMovesetPokemon){
    $auxAllPokemon=$allPokemon;
    for ($i=0; $i <count($auxAllPokemon); $i++) { 
        $needToDelete=true;
       for ($j=0; $j <count($legalMovesetPokemon) ; $j++) {
            if(($auxAllPokemon[$i][1]==$legalMovesetPokemon[$j]["Name"][0]))
            {
                $needToDelete=false;
            }
       }
       if($needToDelete)
        {
            // echo "<p><br></p>antiguo: ";
            // var_dump($auxAllPokemon[$i]);
            array_splice($auxAllPokemon, $i, 1);
            // echo "<p><br></p>nuevo: ";
            // var_dump($auxAllPokemon[$i]);
            $i--;
        }
    }
    return $auxAllPokemon;
}
?>