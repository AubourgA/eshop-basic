<?php

namespace App\Twig\Components;

use App\Services\Product\ChartDataFormater;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class CategorySalesChartComponent
{
    use DefaultActionTrait;

      public function __construct(
       private ChartBuilderInterface $chartBuilder,
       private ChartDataFormater $chartDataFormater,
    ) 
    {  }
    
    #[LiveProp()]
    public array $data = [];

    #[ExposeInTemplate()]
    public function CategorySales(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
       

        $labels = array_column($this->data, 'category');
        $values = array_column($this->data, 'totalSales');

         $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
            ],
            'borderColor' => [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
            ],
            'borderWidth' => 1,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => true, 
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false, 
        ]);
      
       
        return $chart;
    }
}
