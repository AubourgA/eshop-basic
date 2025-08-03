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
final class HstoricPriceChartComponent
{
    use DefaultActionTrait;

    public function __construct(private ChartBuilderInterface $chartBuilder,
                                 private ChartDataFormater $dataFormater)
     { }

    #[LiveProp()]
    public array $data = [];

    #[ExposeInTemplate()]
    public function getChart(): Chart
    {
        $labels= array_map( fn($item) => $item['label'], $this->data);
        $dataValues = array_map( fn($item) => $item['value'], $this->data);


        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        
        $chart->setData([
                'labels' => $labels,
                'datasets' => [
                                [
                                    'data' => $dataValues,
                                    'backgroundColor' => 'rgba(96,165,250, 0.6)',
                                    'borderColor' => 'rgba(59,130,246, 1)',
                                    'borderWidth' => 1,
                                    'pointRadius' => 3,
                                    'tension' => 0.4, // Pour une ligne lissée
                                ],
                            ]
                ],
            );
            
            $chart->setOptions([
                'plugins' => [
                    'legend' => [
                        'display' => false, 
                    ],
                ], 
                'responsive' => true,
                'maintainAspectRatio' => false, 
            ]);
            
           
            return $chart;
    }
}
