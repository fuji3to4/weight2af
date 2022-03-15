<!DOCTYPE html>
<html lang="en" >
<head>
  <title>Weight Visualization on AlfaFold model</title>
  <meta charset="UTF-8">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
    }
  </style>
</head>
<body>
<?php
header('Content-Type:text/html; charset=UTF-8');

//print_r($_POST);

$uniprot=$_POST["id"];
$ih=$_POST["ih"];
$weight =$_POST["weight"];

$weight=str_replace(["\r\n", "\r", "\n"], '', $weight);
$weight_list=explode(',',$weight);
$weight_ave=array_sum($weight_list)/count($weight_list);
$weight_json=json_encode($weight_list);
//print_r($weight_json);
//var_dump(count($weight_list));
//print"</br>";


?>

<div id="form">
<form action="./weight2af.php" method="post">

<p>UniprotID: </br>
        <input type="text" name="id" value="<?=$uniprot?>"/>
</p>

<p>Weight: (*comma-delimited format only)</br>
        <textarea name="weight" rows="5" cols="50"><?=$weight?></textarea>
</p>


<p>Colored by: </br>
<select name="ih">
        <option value="1" selected>Input weight</option>
        <option value="0">AlphaFold confidence score</option>
</select>
</p>
<input type="submit" values="submit">
<form>
</div>


<h3>UniprotID: <?=$uniprot?></h3>
<p>Weight ave: <?=$weight_ave?></p>
<div id="viewport_h" style="width:60%; height:60%;"></div>

<script src="https://cdn.jsdelivr.net/npm/ngl@0.10.4/dist/ngl.min.js"></script>

<script>
// Setup to load data from rawgit


NGL.DatasourceRegistry.add("data", new NGL.StaticDatasource( "https://alphafold.ebi.ac.uk/files/"));

let weight = new Float32Array(<?=$weight_json?>);

let ih=<?=$ih?>
//let ih=0
let stage_h

let file ="AF-<?=$uniprot?>-F1-model_v2.pdb"


document.addEventListener("DOMContentLoaded", function () {
        // Create NGL Stage object
        stage_h = new NGL.Stage( "viewport_h" );

        // Handle window resizing
        window.addEventListener( "resize", function( event ){
                stage_h.handleResize();
        }, false );
        

	Promise.all([

  		stage_h.loadFile("data://"+file),
  		stage_h.loadFile("data://"+file)

	]).then(function (o) {                
		o[1].structure.eachAtom(function(atom) {
			atom.bfactor=weight[atom.residueIndex]
		})
		o[ih].addRepresentation("cartoon", { color: "bfactor" })
		//o[1].addRepresentation("ball+stick", { color: "bfactor" })
		
		stage_h.autoView()

                console.log(o.structure)
        })
})


</script>
</body>
</html>
