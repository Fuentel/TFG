<?php
session_start();
if(isset($_GET["name"])){
    $pokemonSelected=$_GET["name"];
}
$CustomStatsArray=Array(
    "HP" => 1/6,
    "Atk" => 1/6,
    "Def" => 1/6,
    "SAtk" => 1/6,
    "SDef" => 1/6,
    "Spe" => 1/6
);
if(isset($_GET["mStats"])){
    $mStats=$_GET["mStats"];
    switch ($mStats) {
        case 'HyperOfense':
            $CustomStatsArray=Array(
                "HP" => 1/30,
                "Atk" => 9/30,
                "Def" => 1/30,
                "SAtk" => 9/30,
                "SDef" => 1/30,
                "Spe" => 9/30
            );
            break;
    
        case 'Defensive':
            $CustomStatsArray=Array(
                "HP" => 9/30,
                "Atk" => 1/30,
                "Def" => 9/30,
                "SAtk" => 1/30,
                "SDef" => 9/30,
                "Spe" => 1/30
            );
            break;

        case 'Balanced':
            $CustomStatsArray=Array(
                "HP" => 1/6,
                "Atk" => 1/6,
                "Def" => 1/6,
                "SAtk" => 1/6,
                "SDef" => 1/6,
                "Spe" => 1/6
            );
            break;
                    
        default:
            # code...
            break;
    }
    if($mStats="HyperOfense"){
        
    }
}
else{
    $mStats=1;
}
$needToGoBack=true;
for ($j=0; $j <count($_SESSION['Pokemon0']) ; $j++) {
    if(($_SESSION['Pokemon0'][$j][1]==$pokemonSelected))
    {
        $needToGoBack=false;
        //echo "Elegiste Bien";
        if(count($_SESSION['Team'])<6){   
            echo "<p><br></p>Your Team Right Now: ";
            array_push($_SESSION['Team'],$_SESSION['Pokemon0'][$j]);
            array_splice($_SESSION['Pokemon0'], $j, 1);
            for ($j=0; $j < count($_SESSION['Team']); $j++) { 
                echo "<br>".($j+1).": ".$_SESSION['Team'][$j][1];
            }
        }
        break;
    }
}
if($needToGoBack){
    header("Location: index.php");
}
$nextPokemon=Array();
$nextPokemon=calculateNextPosiblesPokemon();

if(count($_SESSION['Team'])<6){        

    echo "<p><br></p> choose between these pokemon";
    echo "<p><br></p> option1: ".$nextPokemon[0]['name'];
    echo "<p><br></p> option2: ".$nextPokemon[1]['name'];
    echo "<p><br></p> option3: ".$nextPokemon[2]['name'];
    echo "<p><br></p> option4: ".$nextPokemon[3]['name'];
    echo "<p><br></p> option5: ".$nextPokemon[4]['name'];
    echo "<p><br></p> option6: ".$nextPokemon[5]['name'];

    echo '<html>
    <body>

    <h2>Select Next Pokemon</h2>

    <form action="/TFG/selectPokemon.php">
    <label for="name">Pokemon:</label><br>
    <input type="text" id="name" name="name" value="PokemonName"><br>
    <input type="submit" value="Submit">
    </form> 
    </body>
    </html>';
}
function calculateWeightedScore($highest,$lowest,$scoreArray){
    
    $aux=0;
    $weightedScore=$scoreArray;
    for($i=0; $i < count($scoreArray); $i++){
        if($scoreArray[$i]['num']==0){
            $weightedScore[$i]['num']=0;
        }
        else{
            $weightedScore[$i]['num']=($scoreArray[$i]['num'] - $lowest)*(10/($highest-$lowest));
        };
        
        // echo "<p><br></p>Weighted Score:";
        // var_dump($weightedScore[$i]['name']);
        // var_dump($weightedScore[$i]['num']);
    }
    return $weightedScore;
}

function calculateFinalScore($statsArray,$typeArray,$usageArray,$teammatesArray){//need to add the teammates
    $aux=0;
    $finalScore=$statsArray;
    // echo "<p><br></p>comproba";
    // var_dump($finalScore);
    for($i=0; $i < count($statsArray); $i++){
        // echo "<p><br></p>nombre";
        // var_dump($statsArray[$i]['name']);
        // echo "<p><br></p>comproba stats";
        // var_dump($statsArray[$i]['num']);
        // echo "<p><br></p>comproba type";
        // var_dump($typeArray[$i]['num']);
        // echo "<p><br></p>comproba usage";
        // var_dump($usageArray[$i]['num']);
        // echo "<p><br></p>comproba mate";
        // var_dump($teammatesArray[$i]['num']);
        $finalScore[$i]['num']=($statsArray[$i]['num']*(1/8)+$typeArray[$i]['num']*(3/8)+$usageArray[$i]['num']*(1/8)+$teammatesArray[$i]['num']*(3/8))/4;//4 despues
        $finalScore[$i]['num']=round($finalScore[$i]['num'],3);//since the highest number of pokemon in the final dataset is 373 3 decimals is enough for having different socres
    }
    rsort($finalScore);
    // echo "<p><br></p>Ordenau";
    // var_dump($finalScore);
    return $finalScore;
}

function calculateNextPosiblesPokemon(){
    $numberInTeam=count($_SESSION['Team']);
    $arrayStats=Array();
    $arrayStats=calculateStatsNumbers($numberInTeam);//de highestStat a lowestStat
    // echo "<p><br></p>Stats Weighted Score:";
    $arrayStats=calculateWeightedScore($_SESSION['highestStat'],$_SESSION['lowestStat'],$arrayStats);
    // var_dump(count($arrayStats));
    $arrayType=Array();
    $arrayType=calculateTypingNumbers($numberInTeam);//de "" ""
    // echo "<p><br></p>Type Weighted Score:";
    $arrayType=calculateWeightedScore($_SESSION['highestType'],$_SESSION['lowestType'],$arrayType);
    // var_dump(count($arrayType));
    $arrayMeta=Array();
    $arrayMeta=calculateMetaNumbers($numberInTeam);//de "" ""
    // echo "<p><br></p>Usage Weighted Score:";
    $arrayMeta=calculateWeightedScore($_SESSION['highestUsage'],$_SESSION['lowestUsage'],$arrayMeta);
    //var_dump(count($arrayMeta));
    $arrayTeammates=Array();
    $arrayTeammates=calculateTeammatesNumbers($numberInTeam);//de "" ""
    $arrayTeammates=calculateWeightedScore($_SESSION['highestTeammates'],$_SESSION['lowestTeammates'],$arrayTeammates);
    return calculateFinalScore($arrayStats,$arrayType,$arrayMeta,$arrayTeammates);
}



function calculateStatsNumbers($numberInTeam){
    $CustomStatsArray=$GLOBALS["CustomStatsArray"];
    $stats=Array();
    $maxStats=Array(
        "HP" => 0,
        "Atk" => 0,
        "Def" => 0,
        "SAtk" => 0,
        "SDef" => 0,
        "Spe" => 0
    );
    $minStats=Array(
        "HP" => 10000,
        "Atk" => 10000,
        "Def" => 10000,
        "SAtk" => 10000,
        "SDef" => 10000,
        "Spe" => 10000
    );
    $sumTeam=Array(
        "HP" => 0,
        "Atk" => 0,
        "Def" => 0,
        "SAtk" => 0,
        "SDef" => 0,
        "Spe" => 0
    );
    for ($i=0; $i < $numberInTeam ; $i++) { 
        $sumTeam["HP"]+=$_SESSION['Team'][$i][5];
        $sumTeam["Atk"]+=$_SESSION['Team'][$i][6];
        $sumTeam["Def"]+=$_SESSION['Team'][$i][7];
        $sumTeam["SAtk"]+=$_SESSION['Team'][$i][8];
        $sumTeam["SDef"]+=$_SESSION['Team'][$i][9];
        $sumTeam["Spe"]+=$_SESSION['Team'][$i][10];
    }
    $TMaxStats=Array(
        "HP" => 0,
        "Atk" => 0,
        "Def" => 0,
        "SAtk" => 0,
        "SDef" => 0,
        "Spe" => 0
    );
    for ($i=0; $i < count($_SESSION['Pokemon0']); $i++) { 
        $pokemonCheckinStats=Array(
            "HP" => 0,
            "Atk" => 0,
            "Def" => 0,
            "SAtk" => 0,
            "SDef" => 0,
            "Spe" => 0
        );
        $finalAvarage=Array(
            "HP" => 0,
            "Atk" => 0,
            "Def" => 0,
            "SAtk" => 0,
            "SDef" => 0,
            "Spe" => 0
        );
        $pokemonCheckinStats["HP"]=$_SESSION['Pokemon0'][$i][5];
        $pokemonCheckinStats["Atk"]=$_SESSION['Pokemon0'][$i][6];
        $pokemonCheckinStats["Def"]=$_SESSION['Pokemon0'][$i][7];
        $pokemonCheckinStats["SAtk"]=$_SESSION['Pokemon0'][$i][8];
        $pokemonCheckinStats["SDef"]=$_SESSION['Pokemon0'][$i][9];
        $pokemonCheckinStats["Spe"]=$_SESSION['Pokemon0'][$i][10];
        $finalAvarage["HP"]=($sumTeam["HP"]+$pokemonCheckinStats["HP"])/($numberInTeam+1);
        $finalAvarage["Atk"]=($sumTeam["Atk"]+$pokemonCheckinStats["Atk"])/($numberInTeam+1);
        $finalAvarage["Def"]=($sumTeam["Def"]+$pokemonCheckinStats["Def"])/($numberInTeam+1);
        $finalAvarage["SAtk"]=($sumTeam["SAtk"]+$pokemonCheckinStats["SAtk"])/($numberInTeam+1);
        $finalAvarage["SDef"]=($sumTeam["SDef"]+$pokemonCheckinStats["SDef"])/($numberInTeam+1);
        $finalAvarage["Spe"]=($sumTeam["Spe"]+$pokemonCheckinStats["Spe"])/($numberInTeam+1);

        $finalStat=dotProductStats($finalAvarage,$CustomStatsArray);
        $aux=Array(
            "num"=>0,
            "name"=>""
        );
        if($finalStat>$_SESSION['highestStat']){
            $_SESSION['highestStat']=$finalStat;
        }
        if($finalStat<$_SESSION['lowestStat'] || $_SESSION['lowestStat']==0){
            $_SESSION['lowestStat']=$finalStat;
        }
        $aux["num"]=$finalStat;
        $aux["name"]=$_SESSION['Pokemon0'][$i][1];
        array_push($stats,$aux);
    }
    // echo "<p><br></p>sin ordenar";
    // var_dump($stats);
    //rsort($stats);
    // echo "<p><br></p>ordenau";
    // var_dump($stats);
    return $stats;
}
function dotProductStats($array1,$array2){

    $HP=$array1["HP"]*$array2["HP"];
    $Atk=$array1["Atk"]*$array2["Atk"];
    $Def=$array1["Def"]*$array2["Def"];
    $SAtk=$array1["SAtk"]*$array2["SAtk"];
    $SDef=$array1["SDef"]*$array2["SDef"];
    $Spe=$array1["Spe"]*$array2["Spe"];

    $result=$HP+$Atk+$Def+$SAtk+$SDef+$Spe;
    
    return $result;
}
function calculateTypingNumbers($numberInTeam){
    $typeFinalNum=Array();
    $teamTyping=Array(
        "Normal" => 0,	
        "Fire" => 0,	
        "Water" => 0,	
        "Electric" => 0,
        "Grass" => 0,
        "Ice" => 0,
        "Fighting" => 0,
        "Poison" => 0,
        "Ground" => 0,
        "Flying" => 0,
        "Psychic" => 0,
        "Bug" => 0,
        "Rock" => 0,
        "Ghost"  => 0,
        "Dragon" => 0,
        "Dark"  => 0,
        "Steel"  => 0,
        "Fairy"  => 0
    );
    $mType=Array(
        "Normal" => 1,	
        "Fire" => 1,	
        "Water" => 1,	
        "Electric" => 1,
        "Grass" => 1,
        "Ice" => 1,
        "Fighting" => 1,
        "Poison" => 1,
        "Ground" => 1,
        "Flying" => 1,
        "Psychic" => 1,
        "Bug" => 1,
        "Rock" => 1,
        "Ghost"  => 1,
        "Dragon" => 1,
        "Dark"  => 1,
        "Steel"  => 1,
        "Fairy"  => 1
    );
    for ($i=0; $i < $numberInTeam; $i++) { 
        // echo "<p><br></p>Pokemon to check in team: ";
        // var_dump($_SESSION['Team'][$i][1]);
        $auxToAdd=Array(
            "Normal" => 0,	
            "Fire" => 0,	
            "Water" => 0,	
            "Electric" => 0,
            "Grass" => 0,
            "Ice" => 0,
            "Fighting" => 0,
            "Poison" => 0,
            "Ground" => 0,
            "Flying" => 0,
            "Psychic" => 0,
            "Bug" => 0,
            "Rock" => 0,
            "Ghost"  => 0,
            "Dragon" => 0,
            "Dark"  => 0,
            "Steel"  => 0,
            "Fairy"  => 0
        );
        $auxToAdd=checkTyping($_SESSION['Team'][$i][3],$_SESSION['Team'][$i][4]);
        // echo "<p><br></p>after checking type";
        // var_dump($auxToAdd);
        if($i!=0){
            // echo "<p><br></p>otros";
            $teamTyping=calculateNewTypesForTeam($teamTyping,$auxToAdd);
        }else{ 
            // echo "<p><br></p>primero";
            $teamTyping=$auxToAdd;
        }
        // echo "<p><br></p>after everything";
        // var_dump($teamTyping);
        // echo "<p><br></p>team finish";

        //$teamTyping=addTypes($teamTyping,$auxToAdd);
    }
    $finalType=Array(
        "Normal" => 0,	
        "Fire" => 0,	
        "Water" => 0,	
        "Electric" => 0,
        "Grass" => 0,
        "Ice" => 0,
        "Fighting" => 0,
        "Poison" => 0,
        "Ground" => 0,
        "Flying" => 0,
        "Psychic" => 0,
        "Bug" => 0,
        "Rock" => 0,
        "Ghost"  => 0,
        "Dragon" => 0,
        "Dark"  => 0,
        "Steel"  => 0,
        "Fairy"  => 0
    );
    for ($i=0; $i < count($_SESSION['Pokemon0']); $i++) { 
        $typeWithNewPokemon=$teamTyping;
        // echo "<p><br></p>Pokemon to check outside: ";
        // var_dump($_SESSION['Pokemon0'][$i][1]);
        // echo "<p><br></p>Type team";
        // var_dump($teamTyping);
        $auxToAdd=Array(
            "Normal" => 0,	
            "Fire" => 0,	
            "Water" => 0,	
            "Electric" => 0,
            "Grass" => 0,
            "Ice" => 0,
            "Fighting" => 0,
            "Poison" => 0,
            "Ground" => 0,
            "Flying" => 0,
            "Psychic" => 0,
            "Bug" => 0,
            "Rock" => 0,
            "Ghost"  => 0,
            "Dragon" => 0,
            "Dark"  => 0,
            "Steel"  => 0,
            "Fairy"  => 0
        );
        $auxToAdd=checkTyping($_SESSION['Pokemon0'][$i][3],$_SESSION['Pokemon0'][$i][4]);
        // echo "<p><br></p>after checking type";
        // var_dump($auxToAdd);
        $typeWithNewPokemon=calculateNewTypesForTeam($typeWithNewPokemon,$auxToAdd);
        // echo "<p><br></p>after everything";
        // var_dump($typeWithNewPokemon);
        
        //cosas de antes de intentar prim y luego cambiarlo
        //$typeWithNewPokemon=addTypes($typeWithNewPokemon,$auxToAdd);
        //echo "<p><br></p>Types after adding to the team ";
        //var_dump($typeWithNewPokemon);
        //$finalType=subtractTypes($typeWithNewPokemon,$teamTyping); //esto lo hace el pive pero pa mi q es inutil ya que tendrias todo el rato T(p) y seria siempre igual en cada iteracion
        // echo "<p><br></p>final types ";
        // var_dump($finalType);
        $finalType=dotProductTypes($typeWithNewPokemon,$mType);
        // echo "<p><br></p>final number ";
        // var_dump($aux);
        $aux=Array(
            "num"=>0,
            "name"=>""
        );
        $aux["num"]=$finalType;
        $aux["name"]=$_SESSION['Pokemon0'][$i][1];
        array_push($typeFinalNum,$aux);
        if($aux['num']>$_SESSION['highestType']){
            $_SESSION['highestType']=$aux['num'];
        }
        if($aux['num']<$_SESSION['lowestType'] || $_SESSION['lowestType']==0){
            $_SESSION['lowestType']=$aux['num'];
        }
    }
    // echo "<p><br></p>Final: ";
    // var_dump($typeFinalNum);
    // rsort($typeFinalNum);
    // echo "<p><br></p>ordenau";
    // var_dump($typeFinalNum);
    return $typeFinalNum;

}
function addTypes($array1,$array2){
    

    $Normal=$array1["Normal"]+$array2["Normal"];
    $Fire=$array1["Fire"]+$array2["Fire"];
    $Water=$array1["Water"]+$array2["Water"];
    $Electric=$array1["Electric"]+$array2["Electric"];
    $Grass=$array1["Grass"]+$array2["Grass"];
    $Ice=$array1["Ice"]+$array2["Ice"];
    $Fighting=$array1["Fighting"]+$array2["Fighting"];
    $Poison=$array1["Poison"]+$array2["Poison"];
    $Ground=$array1["Ground"]+$array2["Ground"];
    $Flying=$array1["Flying"]+$array2["Flying"];
    $Psychic=$array1["Psychic"]+$array2["Psychic"];
    $Bug=$array1["Bug"]+$array2["Bug"];
    $Rock=$array1["Rock"]+$array2["Rock"];
    $Ghost=$array1["Ghost"]+$array2["Ghost"];
    $Dragon=$array1["Dragon"]+$array2["Dragon"];
    $Dark=$array1["Dark"]+$array2["Dark"];
    $Steel=$array1["Steel"]+$array2["Steel"];
    $Fairy=$array1["Fairy"]+$array2["Fairy"];

    $arrayToPass=Array(
        "Normal" => $Normal,	
        "Fire" => $Fire,	
        "Water" => $Water,	
        "Electric" => $Electric,
        "Grass" => $Grass,
        "Ice" => $Ice,
        "Fighting" => $Fighting,
        "Poison" => $Poison,
        "Ground" => $Ground,
        "Flying" => $Flying,
        "Psychic" => $Psychic,
        "Bug" => $Bug,
        "Rock" => $Rock,
        "Ghost"  => $Ghost,
        "Dragon" => $Dragon,
        "Dark"  => $Dark,
        "Steel"  => $Steel,
        "Fairy"  => $Fairy
    );
    // echo "<p><br></p>array after adding: ";
    // var_dump($arrayToPass);
    return $arrayToPass;
}
function subtractTypes($array1,$array2){
    

    $Normal=$array1["Normal"]-$array2["Normal"];
    $Fire=$array1["Fire"]-$array2["Fire"];
    $Water=$array1["Water"]-$array2["Water"];
    $Electric=$array1["Electric"]-$array2["Electric"];
    $Grass=$array1["Grass"]-$array2["Grass"];
    $Ice=$array1["Ice"]-$array2["Ice"];
    $Fighting=$array1["Fighting"]-$array2["Fighting"];
    $Poison=$array1["Poison"]-$array2["Poison"];
    $Ground=$array1["Ground"]-$array2["Ground"];
    $Flying=$array1["Flying"]-$array2["Flying"];
    $Psychic=$array1["Psychic"]-$array2["Psychic"];
    $Bug=$array1["Bug"]-$array2["Bug"];
    $Rock=$array1["Rock"]-$array2["Rock"];
    $Ghost=$array1["Ghost"]-$array2["Ghost"];
    $Dragon=$array1["Dragon"]-$array2["Dragon"];
    $Dark=$array1["Dark"]-$array2["Dark"];
    $Steel=$array1["Steel"]-$array2["Steel"];
    $Fairy=$array1["Fairy"]-$array2["Fairy"];
    
    $arrayToPass=Array(
        "Normal" => $Normal,	
        "Fire" => $Fire,	
        "Water" => $Water,	
        "Electric" => $Electric,
        "Grass" => $Grass,
        "Ice" => $Ice,
        "Fighting" => $Fighting,
        "Poison" => $Poison,
        "Ground" => $Ground,
        "Flying" => $Flying,
        "Psychic" => $Psychic,
        "Bug" => $Bug,
        "Rock" => $Rock,
        "Ghost"  => $Ghost,
        "Dragon" => $Dragon,
        "Dark"  => $Dark,
        "Steel"  => $Steel,
        "Fairy"  => $Fairy
    );
    // echo "<p><br></p>Array after substract: ";
    // var_dump($arrayToPass);
    return $arrayToPass;
}
function calculateNewTypesForTeam($team,$newMember){
  
    $maxVal=6;
    $multiplier=1.3;
    $auxFirst=Array(
        "Normal" => 0,	
        "Fire" => 0,	
        "Water" => 0,	
        "Electric" => 0,
        "Grass" => 0,
        "Ice" => 0,
        "Fighting" => 0,
        "Poison" => 0,
        "Ground" => 0,
        "Flying" => 0,
        "Psychic" => 0,
        "Bug" => 0,
        "Rock" => 0,
        "Ghost"  => 0,
        "Dragon" => 0,
        "Dark"  => 0,
        "Steel"  => 0,
        "Fairy"  => 0
    );
    $auxsecond=Array(
        "Normal" => 0,	
        "Fire" => 0,	
        "Water" => 0,	
        "Electric" => 0,
        "Grass" => 0,
        "Ice" => 0,
        "Fighting" => 0,
        "Poison" => 0,
        "Ground" => 0,
        "Flying" => 0,
        "Psychic" => 0,
        "Bug" => 0,
        "Rock" => 0,
        "Ghost"  => 0,
        "Dragon" => 0,
        "Dark"  => 0,
        "Steel"  => 0,
        "Fairy"  => 0
    );
    foreach($team as $type => $typeResult){
        // echo "<p><br></p>type: ";
        // var_dump($type);
        // echo "<p><br></p>index: ";
        // var_dump($newMember[$type]);
        $num=0;
        
        if($team[$type]<$newMember[$type]){

            $auxFirst[$type]=clamp($newMember[$type],$newMember[$type],$maxVal);
            $auxFirst[$type]=$newMember[$type]*$multiplier;
        }
        $auxSecond[$type]=$newMember[$type];
        $team[$type]+=$auxSecond[$type]+$auxFirst[$type];
        //$team[$type]+=$newMember[$type];
    }
    
    return $team;
}
function addAllTypes($types){
    $result=0;
    for ($i=0; $i < count($types) ; $i++) { 
        $result+=$types[$i];
    }
    return $result;
}
function dotProductTypes($array1,$array2){

    $Normal=$array1["Normal"]*$array2["Normal"];
    $Fire=$array1["Fire"]*$array2["Fire"];
    $Water=$array1["Water"]*$array2["Water"];
    $Electric=$array1["Electric"]*$array2["Electric"];
    $Grass=$array1["Grass"]*$array2["Grass"];
    $Ice=$array1["Ice"]*$array2["Ice"];
    $Fighting=$array1["Fighting"]*$array2["Fighting"];
    $Poison=$array1["Poison"]*$array2["Poison"];
    $Ground=$array1["Ground"]*$array2["Ground"];
    $Flying=$array1["Flying"]*$array2["Flying"];
    $Psychic=$array1["Psychic"]*$array2["Psychic"];
    $Bug=$array1["Bug"]*$array2["Bug"];
    $Rock=$array1["Rock"]*$array2["Rock"];
    $Ghost=$array1["Ghost"]*$array2["Ghost"];
    $Dragon=$array1["Dragon"]*$array2["Dragon"];
    $Dark=$array1["Dark"]*$array2["Dark"];
    $Steel=$array1["Steel"]*$array2["Steel"];
    $Fairy=$array1["Fairy"]*$array2["Fairy"];

    $result=$Normal+$Fire+$Water+$Electric+$Grass+$Ice+$Fighting+$Poison+$Ground+$Flying+$Psychic+$Bug+$Rock+$Ghost+$Dragon+$Dark+$Steel+$Fairy;
    
    return $result;
}

function checkTyping($type1,$type2){

    $auxType=Array(
        "Normal" => 0,	
        "Fire" => 0,	
        "Water" => 0,	
        "Electric" => 0,
        "Grass" => 0,
        "Ice" => 0,
        "Fighting" => 0,
        "Poison" => 0,
        "Ground" => 0,
        "Flying" => 0,
        "Psychic" => 0,
        "Bug" => 0,
        "Rock" => 0,
        "Ghost"  => 0,
        "Dragon" => 0,
        "Dark"  => 0,
        "Steel"  => 0,
        "Fairy"  => 0
    );
    $typeToIndex1=0;
    $typeToIndex2=0;
    for ($i=1; $i < count($_SESSION['TypeChart']); $i++) { 
        
        if($_SESSION['TypeChart'][0][$i]==$type1){
            $typeToIndex1=$i;
        } 
        if($_SESSION['TypeChart'][0][$i]==$type2){
            $typeToIndex2=$i;
        }

    }
    if($type2==""){
        $typeToIndex2=-1;
    }
    // echo "<p><br></p>index: ";
    // var_dump($typeToIndex1);
    // var_dump($typeToIndex2);
    // echo "<p><br></p>checking: ";
    for ($j=1; $j < count($_SESSION['TypeChart']); $j++) {
        
        // echo "<p><br></p>checking: ";
        // var_dump($_SESSION['TypeChart'][$j][0]);
        if($typeToIndex2!=-1){
            $auxToSum=twoTypesSpecificWeaksAndResists($typeToIndex1,$typeToIndex2,$j);
        }else{
            $auxToSum=oneTypesSpecificWeaksAndResists($typeToIndex1,$j);
        }
        $auxType[$_SESSION['TypeChart'][0][$j]]=$auxToSum;
    }
    
    return $auxType;
}
function twoTypesSpecificWeaksAndResists($num1,$num2,$j){

    $numToReturn=0;

    //neutro= neutro+neutro=2 o debil+resistente= 2.5
    //debil= debil+neutro=3 o  debil+debil=4 
    //resiste =neutro+resistente=1.5 o resistente+resistente=1
    //inmune va aparte
    $auxType=$_SESSION['TypeChart'][$j][$num1]+$_SESSION['TypeChart'][$j][$num2];

    if($_SESSION['TypeChart'][$j][$num1]==0 || $_SESSION['TypeChart'][$j][$num2]==0){
        //echo "inmune";
        $numToReturn=3;
    }
    else if($auxType>=3)
    {
        //echo "debil";
        $numToReturn=0;
    }
    else if($auxType>=2){
        //echo "neutro";
        $numToReturn=1;
    }
    else 
    {
        //echo "resiste";
        $numToReturn=2;
    }
    return $numToReturn;
}
function oneTypesSpecificWeaksAndResists($num1,$j){

    $numToReturn=0;
    if($_SESSION['TypeChart'][$j][$num1]==0 )
    {
        // echo "inmune";
        $numToReturn=3;
    }
    else if($_SESSION['TypeChart'][$j][$num1]==1)
    {
        // echo "neutro";
        $numToReturn=1;
    }
    else if(($_SESSION['TypeChart'][$j][$num1]==0.5))
    {
        // echo "resiste";
        $numToReturn=2;
    }
    else 
    {
        // echo "debil";
        $numToReturn=0;
    }
    return $numToReturn;
}
function calculateMetaNumbers($numberInTeam){
    $finalMetaNum=Array();
    for ($i=0; $i < count($_SESSION['Pokemon0']); $i++) { 
        
        for ($j=1; $j < count($_SESSION['Usage0']); $j++) { 
            // echo "<p><br> match con 5";
            // var_dump($_SESSION['Usage0'][$j][1]);
            // echo "<p><br> match con 4";
            // var_dump($_SESSION['Pokemon0'][$i][1]);
            if($_SESSION['Pokemon0'][$i][1]==$_SESSION['Usage0'][$j][1])
            {
                // echo "<p><br> match con sexto";
                $aux=Array(
                    "num"=>0,
                    "name"=>""
                );

                $aux["num"]=str_replace("%","",$_SESSION['Usage0'][$j][4]);
                $aux["num"]=round(($aux["num"]),2);
                $aux["name"]=$_SESSION['Pokemon0'][$i][1];
                array_push($finalMetaNum,$aux);
                // var_dump($aux);
                if($aux['num']>$_SESSION['highestUsage']){
                    $_SESSION['highestUsage']=$aux['num'];
                }
                if($aux['num']<$_SESSION['lowestUsage'] || $_SESSION['lowestUsage']==0){
                    $_SESSION['lowestUsage']=$aux['num'];
                }
            }

        }

    }
    return $finalMetaNum;

}
function calculateTeammatesNumbers($numberInTeam){
    
    $finalTeammatesNum=Array();

    $allTeammates=Array();
    for ($i=0; $i < count($_SESSION['Moveset0']); $i++) { 
        for ($j=0; $j < $numberInTeam; $j++) {
            // echo "<p><br> movest".$i.": ";
            // var_dump($_SESSION['Moveset0'][$i]["Name"]);
            // echo "<p><br> checking: ";
            // var_dump($_SESSION['Team'][$j][1]);
            if ($_SESSION['Moveset0'][$i]['Name'][0]==$_SESSION['Team'][$j][1]){
                // echo "<p><br> dentro";
                $thisPokeTeammates=Array(
                        "name"=>$_SESSION['Team'][$j][1],
                        "teammates"=>Array()
                );
                for ($w=0; $w < count($_SESSION['Moveset0'][$i]["Teammates"]); $w++) { 
                    $aux=Array(
                        "num"=>0,
                        "name"=>""
                    );  
                    $wholeTeammtes=explode(" ", $_SESSION['Moveset0'][$i]["Teammates"][$w]);
                    // echo "<p><br> toda la explotada";
                    // var_dump($wholeTeammtes);
                    if(count($wholeTeammtes)>2){
                        // echo "<p><br>rarete: ";
                        if(strlen($wholeTeammtes[1])>0){
                            $wholeTeammtes[0]=$wholeTeammtes[0]." ".$wholeTeammtes[1];
                        }
                        $wholeTeammtes[1]=$wholeTeammtes[2];
                        // var_dump($wholeTeammtes);
                    }
                    $wholeTeammtes[1]=str_replace("%","",$wholeTeammtes[1]);
                    $aux["num"]=floatval($wholeTeammtes[1]);
                    $aux["num"]=round(($aux["num"]),2);
                    $aux["name"]=$wholeTeammtes[0];
                    array_push($thisPokeTeammates["teammates"],$aux);
                }
                // echo "</p><br> no puec mas";
                // var_dump($thisPokeTeammates);
                array_push($allTeammates,$thisPokeTeammates);
            }
        }
    }
    
    for ($j=0; $j < count($_SESSION['Pokemon0']); $j++) { 
        $aux=Array(
            "num"=>0,
            "name"=>$_SESSION['Pokemon0'][$j][1]
        ); 
        for ($i=0; $i < count($allTeammates); $i++) { 
            for ($w=0; $w < count($allTeammates[$i]['teammates']); $w++) { 
                if($_SESSION['Pokemon0'][$j][1]==$allTeammates[$i]['teammates'][$w]['name']){
                    // echo "<p><br> match con septimo";
                    // var_dump($_SESSION['Pokemon0'][$j][1]);
                    $aux["num"]+=$allTeammates[$i]['teammates'][$w]["num"];
                    $aux["num"]+=round(($aux["num"]),2);
                    if($aux['num']>$_SESSION['highestTeammates']){
                        $_SESSION['highestTeammates']=$aux['num'];
                    }
                    if($aux['num']<$_SESSION['lowestTeammates']){
                        $_SESSION['lowestTeammates']=$aux['num'];
                    }
                }
            }
        }
        // echo "</p><br> mis experanzas se desvanecen";
        // var_dump($aux);
        array_push($finalTeammatesNum,$aux);
    }
    // echo "<p><br>final teammates: ";
    // var_dump($finalTeammatesNum);   
    return $finalTeammatesNum;


}
function clamp($current,$min, $max)
{
	return max($min, min($max, $current));
}
?>