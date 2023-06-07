<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TruncateTextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate_text', [$this, 'truncateText']),
        ];
    }

    public function truncateText($string, $maxLength = 30): array|string
    {
        $result = strip_tags($string);
        $result = str_replace("\r", "", $result);
        $result = str_replace("\n", "", $result);

        if(mb_strlen($string) > $maxLength){
            $finalLength = $maxLength - 3;
            return mb_substr($string,0, $finalLength).'...';
        }

        return $result;
    }
}
