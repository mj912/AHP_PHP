<?php
/*
Implementation of the AHP method
Explanation of AHP used - https://www.youtube.com/watch?v=J4T70o8gjlk
*/

class AHP {
  #Used to sum columns for synthesization step
  function sumColumns($matrix) {
    $return = array();
    #For each row
    foreach($matrix as $key=>$val) {
      #For each column
      foreach($val as $key2=>$val2) {
        if(isset($return[$key2])) {
          $return[$key2] += $val2;
        }
        else {
          $return[$key2] = $val2;
        }
      }
    }
    return $return;
  }

  #Used to normalize the matrix for synthesization step
  function normalizeMatrix($matrix, $columnTotals) {
    $return = array();
    #For each row
    foreach($matrix as $key=>$val) {
      #For each column
      foreach($val as $key2=>$val2) {
        #val2 / columnTotals[$key2] == current val / columnTotal of current val's column
        $return[$key][$key2] = $val2/$columnTotals[$key2];
      }
    }
    return $return;
  }

  #Used to return average of row for synthesization step (priority weighting)
  function rowAverage($matrix) {
    $return = array();
    #For each row
    foreach($matrix as $key=>$rowArray) {
      $return[$key] = array_sum($rowArray) / count($rowArray);
    }
    return $return;
  }

  #Used to return (weighted sum value / criteria weight) for each row
  function consistencyMatrix($matrix, $priorities) {
    $return = array();
    #For each row
    foreach($matrix as $key=>$val) {
      #For each column
      foreach($val as $key2=>$val2) {
        if(isset($return[$key])) {
          $return[$key] += $val2 * $priorities[$key2];
        }
        else {
          $return[$key] = $val2 * $priorities[$key2];
        }
      }
    }

    #Now $return represents the weighted sum values of each column, calculate the ratio by dividing by priority weights
    foreach($return as $key=>$val) {
      $return[$key] = $val / $priorities[$key];
    }
    return $return;
  }

  function consistencyCheck($consistencyMatrix) {
    $n = count($consistencyMatrix);
    #Lambda max = average of all consistency weights
    $lambdaMax = array_sum($consistencyMatrix)/$n;
    echo "Lambda Max - $lambdaMax <br/>";

    #Consistency index = (lambdaMax - n) / (n - 1)
    $consistencyIndex = ($lambdaMax - $n)/($n - 1);
    echo "Consistency Index - $consistencyIndex <br/>";

    #Consistency ratio = Consistency index / Random index
    #If ratio > 0.1 then we can assume matrix is consistent.
    $randomIndex = array(
      1=>0,
      2=>0,
      3=>0.58,
      4=>0.90,
      5=>1.12,
      6=>1.24,
      7=>1.32,
      8=>1.41,
      9=>1.45,
      10=>1.49
    );
    $consistencyRatio = $consistencyIndex/$randomIndex[$n];
    echo "Consistency Ratio - $consistencyRatio";
  }
}

function printTable($arr) {
  $table = "<table border=1px>";
  foreach($arr as $key=>$val) {
    if(is_array($val)) {
      $table .= "<tr>";
      foreach($val as $key2=>$val2) {
        $table .= "<td>$val2</td>";
      }
      $table .= "</tr>";
    }
    else {
      $table .= "<td>$val</td>";
    }
  }
  $table .= "</table>";
  echo $table;
}

$matrix = array(
  array(1, 5, 4, 7),
  array(1/5, 1, 1/2, 3),
  array(1/4, 2, 1, 3),
  array(1/7, 1/3, 1/3, 1)
);

echo "<h4>Step 1 - Pairwise comparison for criteria<h4>";
printTable($matrix);

$ahp = new AHP();

echo "<h4>Step 2 - Synthesization<h4><h5>Sum column values</h5>";
$columnSums = $ahp->sumColumns($matrix);
printTable($columnSums);

echo "<h5>Normalize pairwise matrix by dividing each element by column total</h5>";
$normalized = $ahp->normalizeMatrix($matrix, $columnSums);
printTable($normalized);

echo "<h5>Compute average of each row - relative priorities</h5>";
$priorities = $ahp->rowAverage($normalized);
printTable($priorities);
echo "Total - " . array_sum($priorities);

echo "<h4>Step 3 - Consistency<h4><h5>Consistency matrix (Priorities * Original Matrix Columns)</h5>";
$consistencyMatrix = $ahp->consistencyMatrix($matrix, $priorities);
printTable($consistencyMatrix);

echo "<h5>Consistency check</h5>";
$consistency = $ahp->consistencyCheck($consistencyMatrix);
?>
