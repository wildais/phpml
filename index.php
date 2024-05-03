<?php

require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Dataset\CsvDataset;
$dataset = new CsvDataset('StudentsPerformance.csv', 5, true);

use Phpml\CrossValidation\StratifiedRandomSplit;
$dataset = new StratifiedRandomSplit($dataset, 0.2, 1234);
$xTrainData = $dataset->getTrainSamples();
$yTrainData = $dataset->getTrainLabels();
$xTestData = $dataset->getTestSamples();
$yTestData = $dataset->getTestLabels();

use Phpml\Preprocessing\LabelEncoder;
function label_encode($xData){
    $xDataProcessed = [];
    $colNum = count($xData[0]);
    for($i = 0;$i < $colNum;$i++){
        $colData = array_column($xData, $i);
        $labelEncoder = new LabelEncoder();
        $target = [];
        $labelEncoder->fit($colData, $target);
        $labels = $labelEncoder->classes();
        for($j = 0;$j < count($xData);$j++){
            $xDataProcessed[$j][$i] = array_search($xData[$j][$i], $labels);
        }
    }
    return $xDataProcessed;
}
$xTrainEncoded = label_encode($xTrainData);
$xTestEncoded = label_encode($xTestData);

use Phpml\Classification\DecisionTree;
$model = new DecisionTree();
$model->train($xTrainEncoded, $yTrainData);

use Phpml\Metric\Regression;
$prediction = [];
for($i = 0;$i < count($xTestEncoded);$i++){
    $prediction[$i] = $model->predict($xTestEncoded[$i]);
    echo $prediction[$i]."<br>";
}
echo Regression::meanAbsoluteError($yTestData, $prediction);

?>