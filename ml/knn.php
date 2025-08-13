<?php 
    require_once __DIR__ . '/vendor/autoload.php';

    use Phpml\Classification\KNearestNeighbors;

    $samples = [
        [2500, 30, 10],
        [3000, 20, 10],
        [3000, 19, 12],
        [5000, 19, 44],
        [6500, 40, 50],
        [3500, 37, 18],
        [2500, 35, 16],
        [1600, 25, 14]
    ];

    $labels = [
        'Munk', 
        'Munk', 
        'Munk', 
        'Guindaste 8T', 
        'Guindaste 8T', 
        'Guindaste 8T', 
        'Guindaste 2T', 
        'Guindaste 2T'
    ];

    $classifier = new KNearestNeighbors($k = 5);
    $classifier->train($samples, $labels);

    echo $classifier->predict([6000, 55, 50]);