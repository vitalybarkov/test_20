<?php
class Apple {
    public const TIMETODECAY = 18001;
    public const MAXEATEDPERCENTAGE = 100.0;

    public $id = 0;
    public $color = '';
    public $creationDate = 0;
    public $dropDate = 0;
    public $dropped = false;
    public $eatedPercentage = 0.0;
    public $deleted = false;

    public function __construct ($id = 0, $color = 'green', $creationDate = 0, $currentDropDate = 0, $currentDropped = 0, $drop = 0, $currentEatedPercentage = 0.0, $eat = 0.0) {
        if ($id > 0) {
            $this->id = $id;
        }
        if ($color) {
            $this->color = $color;
        }
        if ($creationDate) {
            $this->creationDate = $creationDate;
        }
        if ($currentDropDate) {
            $this->dropDate = $currentDropDate;
        }
        if ($currentDropped > 0) {
            $this->dropped = true;
        }
        if (!$this->dropped && $drop) {
            $this->drop();
        }
        if ($currentEatedPercentage > 0) {
            $this->eatedPercentage = $currentEatedPercentage;
        }
        if ($eat > 0) {
            $this->eat($eat);
        }
        if (!$id) {
            $this->color = $color;
            $this->creationDate = time();
        }
    } 

    public function drop () {
        if (!$this->dropped) {
            $this->dropped = true;
            $this->dropDate = time();
        }
    }

    public function eat ($percentage = 0) {
        if (
            $this->dropped
            && (time() - $this->dropDate) < constant(self::class . '::TIMETODECAY')
            && $percentage > 0
        ) {
            $this->eatedPercentage += $percentage;
            if (!$this->deleted && $this->eatedPercentage >= constant(self::class . '::MAXEATEDPERCENTAGE')) {
                $this->eatedPercentage = constant(self::class . '::MAXEATEDPERCENTAGE');
                $this->delete();
            }
        }
    }

    private function delete () {
        $this->deleted = true;
    }
}