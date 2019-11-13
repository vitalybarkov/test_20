<?php
require_once './m/Apple.php';

class Tree {
    public $appleColors = ['red', 'orange', 'yellow', 'green', 'darkred'];
    public $appleQuantity = 1;
    public $apples = false;
    public $maxGrowAppleQuantityByRandom = 32;

    public function __construct ($quantity = 0) {
        $this->apples = $this->growApples($quantity);
    }

    private function growApples ($quantity) {
        $apples = false;
        
        if ($quantity) {
            $this->appleQuantity = $quantity;
        } else {
            $this->appleQuantity = rand(1, $this->maxGrowAppleQuantityByRandom);
        }
        for ($i = 0; $i < $this->appleQuantity; $i++) {
            $apples[$i] = new Apple(0, $this->appleColors[rand(0, sizeof($this->appleColors) - 1)], 0, 0, 0, 0, 0, 0);
        }

        return $apples;
    }
}
