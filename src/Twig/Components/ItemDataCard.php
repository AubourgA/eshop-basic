<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ItemDataCard
{
    public string $title;

    public string|int|float $value;

    public ?string $iconPath = null;

    public ?string $colorIcon = null;
}
