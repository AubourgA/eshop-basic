<?php

namespace App\Twig\Components;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class OrdersMonthChartComponent
{
    use DefaultActionTrait;
  
    public function __construct(
       private ChartBuilderInterface $chartBuilder,
    ) {    
    }

    #[LiveProp]
    public array $data = [];

    #[ExposeInTemplate]
    public function getChart(): Chart
       {
        $normalized = $this->normalizeData();

       
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_keys($normalized),
            'datasets' => [
                [
                    'data' => array_values($normalized),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false, 
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false, 
        ]);

        return $chart;
    }

 

    private function normalizeData(): array
    {
        $result = [];

        foreach ($this->data as $row) {
            $yearMonth = $row['yearMonth']; // ex: "2024-08"
            $count = $row['orderCount'];

            $date = \DateTimeImmutable::createFromFormat('Y-m', $yearMonth);

            if (!$date) {
                // En cas d'erreur de parsing, on utilise la valeur brute
                $label = $yearMonth;
            } else {
                $formatter = new \IntlDateFormatter(
                    'fr_FR',
                    \IntlDateFormatter::FULL,
                    \IntlDateFormatter::NONE,
                    null,
                    null,
                    'LLLL yy' // ex: "août 24"
                );

                $label = ucfirst($formatter->format($date));
            }

            $result[$label] = $count;
        }

        return $result;
    }
    
}
