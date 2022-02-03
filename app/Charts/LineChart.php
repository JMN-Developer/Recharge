<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class LineChart
{
    protected $chart;
    protected $sales;
    protected $profits;
    protected $date;
   
    public function __construct($sales,$profits,$date)
    {
        $chart = new LarapexChart();
        $this->chart = $chart;
        $this->sales = $sales;
        $this->profits = $profits;
        $this->date = $date;
     
    }

    public function build()
    {
        //file_put_contents('test.txt',json_encode($this->sales)." ".json_encode( $this->profits)." ".json_encode($this->date));
        return $this->chart->lineChart()
            ->setTitle('Sales Vs Profit')
            ->addData('Sales',  $this->sales)
            ->addData('Profit', $this->profits)
            ->setXAxis($this->date)
            ->setGrid()          
            ->setMarkers(['#FF5722', '#E040FB'], 7, 10)
            ->setDataLabels();
    }
}
