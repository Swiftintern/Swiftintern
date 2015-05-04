<?php

namespace PHPChart;

/**
 * Main Class to Draw Chart and Graphs
 *
 * @author Faizan Ayubi
 */
class Chart {

    public $data;
    public $barWidth = 20;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Method to Draw Bar Graph
     * 
     * @param type $img_width
     * @param type $img_height
     * @param type $margins
     */
    public function drawBar($img_width = 450, $img_height = 300, $margins = 20) {
        //Finding the size of graph by substracting the size of borders
        $graph_width = $this->automargin($img_width, $margins);
        $graph_height = $this->automargin($img_height, $margins);

        $img = imagecreate($img_width, $img_height);

        $total_bars = count($this->data);
        $gap = ($graph_width - $total_bars * $this->barWidth ) / ($total_bars + 1);

        //Defining Colors
        $bar_color = imagecolorallocate($img, 0, 64, 128);
        $background_color = imagecolorallocate($img, 240, 240, 255);
        $border_color = imagecolorallocate($img, 200, 200, 200);
        $line_color = imagecolorallocate($img, 220, 220, 220);

        //Create the border around the graph
        imagefilledrectangle($img, 1, 1, $img_width - 2, $img_height - 2, $border_color);
        imagefilledrectangle($img, $margins, $margins, $img_width - 1 - $margins, $img_height - 1 - $margins, $background_color);

        //Max value is required to adjust the scale
        $max_value = max($this->data);
        $ratio = $graph_height / $max_value;

        //Creating scale and draw horizontal lines
        $horizontal_lines = 20;
        $horizontal_gap = $graph_height / $horizontal_lines;

        for ($i = 1; $i <= $horizontal_lines; $i++) {
            $y = $img_height - $margins - $horizontal_gap * $i;
            imageline($img, $margins, $y, $img_width - $margins, $y, $line_color);
            $v = intval($horizontal_gap * $i / $ratio);
            imagestring($img, 0, 5, $y - 5, $v, $bar_color);
        }

        //Drawing the bars here
        for ($i = 0; $i < $total_bars; $i++) {
            # ------ Extract key and value pair from the current pointer position
            list($key, $value) = each($this->data);
            $x1 = $margins + $gap + $i * ($gap + $this->barWidth);
            $x2 = $x1 + $this->barWidth;
            $y1 = $margins + $graph_height - intval($value * $ratio);
            $y2 = $img_height - $margins;
            imagestring($img, 0, $x1 + 3, $y1 - 10, $value, $bar_color);
            imagestring($img, 0, $x1 + 3, $img_height - 15, $key, $bar_color);
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $bar_color);
        }
        
        header("Content-type:image/png");
        imagepng($img);
    }

    /**
     * Adjust Margin for graphs
     * 
     * @param type $length
     * @param type $margin
     * @return type
     */
    public function automargin($length, $margin) {
        return $length - $margin * 2;
    }
    
    public function save() {
        $path = APP_PATH . "/public/assets/uploads/images/";
        $filename = '';
        if(!file_exists($path.'/'.$filename)){
            file_put_contents($path.'/'.$filename, $this->show());
        }
    }
    
    
}
