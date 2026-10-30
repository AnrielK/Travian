<?php

namespace resources\View;

/**
 * Server-side SVG charts of Statistics → General in the Travian Legends look, drawn like the recharts charts of the
 * Legends client (same sizes, colours and label placement) so legends_ltr.css lays them out as they are.
 * Every value shown on hover is a <title> of its shape.
 */
class StatisticsCharts
{
    public static function e($s): string
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }

    private static function n(float $x): string
    {
        return rtrim(rtrim(number_format($x, 3, '.', ''), '0'), '.');
    }

    private static function wrap(int $w, int $h, string $body, string $extraClass = ''): string
    {
        return '<div class="recharts-wrapper' . ($extraClass ? ' ' . $extraClass : '') . '" style="position: relative; cursor: default; width: ' . $w . 'px; height: ' . $h . 'px;">'
            . '<svg class="recharts-surface" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '" style="width: 100%; height: 100%; display: block;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
            . $body . '</svg></div>';
    }

    /**
     * Pie chart (320×270, radius 78, white separators), first slice from 12 o'clock clockwise.
     * $slices: [['value' => float, 'color' => '#hex', 'title' => tooltip, 'label' => text, 'icon' => url, 'iconW' => int,
     *           'iconH' => int, 'class' => css class of the label]]
     * $labels: 'side' = icon next to the text (tribes), 'stack' = text above / below the icon (resources), '' = none.
     */
    public static function pie(array $slices, string $labels = 'side', int $w = 320, int $h = 270, int $r = 78): string
    {
        $cx = $w / 2;
        $cy = $h / 2;
        $total = 0;
        foreach ($slices as $slice) {
            $total += max(0, (float)$slice['value']);
        }
        $sectors = '';
        $texts = '';
        if ($total <= 0) {
            $sectors = '<circle cx="' . self::n($cx) . '" cy="' . self::n($cy) . '" r="' . $r . '" fill="#e6e6e6" stroke="#fff"></circle>';
        } else {
            $angle = 0.0; // degrees clockwise from 12 o'clock
            $nonZero = count(array_filter($slices, function ($s) {
                return (float)$s['value'] > 0;
            }));
            foreach ($slices as $slice) {
                $value = max(0, (float)$slice['value']);
                if ($value <= 0) {
                    continue;
                }
                $sweep = $value / $total * 360;
                $title = isset($slice['title']) ? '<title>' . self::e($slice['title']) . '</title>' : '';
                if ($nonZero == 1) {
                    $sectors .= '<circle class="recharts-sector" cx="' . self::n($cx) . '" cy="' . self::n($cy) . '" r="' . $r . '" fill="' . $slice['color'] . '" stroke="#fff">' . $title . '</circle>';
                } else {
                    $a0 = deg2rad($angle);
                    $a1 = deg2rad($angle + $sweep);
                    $x0 = $cx + $r * sin($a0);
                    $y0 = $cy - $r * cos($a0);
                    $x1 = $cx + $r * sin($a1);
                    $y1 = $cy - $r * cos($a1);
                    $large = $sweep > 180 ? 1 : 0;
                    $sectors .= '<path class="recharts-sector" fill="' . $slice['color'] . '" stroke="#fff" d="M ' . self::n($cx) . ',' . self::n($cy)
                        . ' L ' . self::n($x0) . ',' . self::n($y0) . ' A ' . $r . ',' . $r . ',0,' . $large . ',1,' . self::n($x1) . ',' . self::n($y1) . ' Z">' . $title . '</path>';
                }
                if ($labels !== '' && (isset($slice['label']) || isset($slice['icon']))) {
                    $mid = deg2rad($angle + $sweep / 2);
                    $texts .= self::pieLabel($slice, $labels, $cx, $cy, $r, $mid);
                }
                $angle += $sweep;
            }
        }
        return self::wrap($w, $h, '<g class="recharts-layer recharts-pie">' . $sectors . '</g><g class="recharts-layer">' . $texts . '</g>');
    }

    private static function pieLabel(array $slice, string $mode, float $cx, float $cy, int $r, float $mid): string
    {
        $iconW = $slice['iconW'] ?? 26;
        $iconH = $slice['iconH'] ?? 26;
        $class = isset($slice['class']) ? ' class="' . self::e($slice['class']) . '"' : '';
        $label = self::e($slice['label'] ?? '');
        $out = '<g>';
        if ($mode === 'stack') {
            $px = $cx + ($r + 22) * sin($mid);
            $py = $cy - ($r + 22) * cos($mid);
            if (!empty($slice['icon'])) {
                $out .= '<image xlink:href="' . self::e($slice['icon']) . '" href="' . self::e($slice['icon']) . '" x="' . self::n($px - $iconW / 2) . '" y="' . self::n($py - $iconH / 2) . '" width="' . $iconW . '" height="' . $iconH . '"></image>';
            }
            $ty = cos($mid) >= 0 ? $py - $iconH / 2 - 10 : $py + $iconH / 2 + 12;
            $out .= '<text x="' . self::n($px) . '" y="' . self::n($ty) . '" fill="black" text-anchor="middle" dominant-baseline="central"' . $class . '>' . $label . '</text>';
        } else {
            $px = $cx + ($r + 30) * sin($mid);
            $py = $cy - ($r + 30) * cos($mid);
            $right = sin($mid) >= 0;
            if (!empty($slice['icon'])) {
                $ix = $right ? $px - $iconW - 3 : $px + 3;
                $out .= '<image xlink:href="' . self::e($slice['icon']) . '" href="' . self::e($slice['icon']) . '" x="' . self::n($ix) . '" y="' . self::n($py - $iconH / 2) . '" width="' . $iconW . '" height="' . $iconH . '"></image>';
            }
            $out .= '<text x="' . self::n($px) . '" y="' . self::n($py) . '" fill="black" text-anchor="' . ($right ? 'start' : 'end') . '" dominant-baseline="central"' . $class . '>' . $label . '</text>';
        }
        return $out . '</g>';
    }

    /** Round tick values between $min and $max (about $count of them), always ending with $max. */
    private static function ticks(float $min, float $max, int $count = 4): array
    {
        if ($max <= $min) {
            return [$min];
        }
        $raw = ($max - $min) / $count;
        $magnitude = pow(10, floor(log10($raw)));
        $step = $magnitude;
        foreach ([1, 2, 2.5, 5, 10] as $m) {
            if ($m * $magnitude >= $raw) {
                $step = $m * $magnitude;
                break;
            }
        }
        $ticks = [];
        for ($v = $min; $v < $max - $step * 0.3; $v += $step) {
            $ticks[] = $v;
        }
        $ticks[] = $max;
        return $ticks;
    }

    /**
     * Area chart over the days of the round (342×228 like the population rank chart): dashed grid, y axis from $yMin
     * to the highest value, x axis with the day numbers. $points: [day => value].
     */
    public static function area(array $points, array $options = []): string
    {
        $w = $options['width'] ?? 342;
        $h = $options['height'] ?? 228;
        $fill = $options['fill'] ?? '#EA866E';
        $stroke = $options['stroke'] ?? '#bf6060';
        $format = $options['format'] ?? function ($v) {
            return (string)$v;
        };
        $tooltip = $options['tooltip'] ?? function ($day, $v) use ($format) {
            return $day . ': ' . $format($v);
        };
        $left = 65;
        $top = 5;
        $right = $w - 5;
        $bottom = $h - 35;
        if (!$points) {
            $points = [0 => 0];
        }
        ksort($points);
        $days = array_keys($points);
        $dMin = min($days);
        $dMax = max($days);
        if ($dMax == $dMin) {
            $dMax = $dMin + 1;
        }
        $yMin = $options['yMin'] ?? 0;
        $yMax = max(max($points), $yMin + 1);
        $x = function ($d) use ($left, $right, $dMin, $dMax) {
            return $left + ($d - $dMin) / ($dMax - $dMin) * ($right - $left);
        };
        $y = function ($v) use ($top, $bottom, $yMin, $yMax) {
            return $bottom - ($v - $yMin) / ($yMax - $yMin) * ($bottom - $top);
        };
        $yTicks = self::ticks($yMin, $yMax);
        $xTicks = self::ticks($dMin, $dMax);
        $xTicks = array_values(array_unique(array_map('intval', array_map('round', $xTicks))));

        $grid = '<g class="recharts-cartesian-grid">';
        foreach ($yTicks as $t) {
            $grid .= '<line stroke-dasharray="3 3" stroke="#ccc" fill="none" x1="' . $left . '" y1="' . self::n($y($t)) . '" x2="' . $right . '" y2="' . self::n($y($t)) . '"></line>';
        }
        foreach ($xTicks as $t) {
            $grid .= '<line stroke-dasharray="3 3" stroke="#ccc" fill="none" x1="' . self::n($x($t)) . '" y1="' . $top . '" x2="' . self::n($x($t)) . '" y2="' . $bottom . '"></line>';
        }
        $grid .= '</g>';

        $line = [];
        foreach ($points as $d => $v) {
            $line[] = self::n($x($d)) . ',' . self::n($y($v));
        }
        $curve = 'M' . implode('L', $line);
        if (count($points) == 1) {
            // a single day: a flat area over the whole width
            $only = reset($points);
            $curve = 'M' . $left . ',' . self::n($y($only)) . 'L' . $right . ',' . self::n($y($only));
        }
        $areaPath = count($points) == 1
            ? $curve . 'L' . $right . ',' . $bottom . 'L' . $left . ',' . $bottom . 'Z'
            : $curve . 'L' . self::n($x(max($days))) . ',' . $bottom . 'L' . self::n($x(min($days))) . ',' . $bottom . 'Z';
        $area = '<g class="recharts-layer recharts-area"><path class="recharts-curve recharts-area-area" fill="' . $fill . '" fill-opacity="1" stroke="none" d="' . $areaPath . '"></path>'
            . '<path class="recharts-curve recharts-area-curve" fill="none" stroke="' . $stroke . '" d="' . $curve . '"></path></g>';

        $dots = '<g class="statisticsDots">';
        foreach ($points as $d => $v) {
            $dots .= '<circle cx="' . self::n($x($d)) . '" cy="' . self::n($y($v)) . '" r="5" fill="' . $stroke . '" fill-opacity="0" stroke="none"><title>' . self::e($tooltip($d, $v)) . '</title></circle>';
        }
        $dots .= '</g>';

        $axes = '<g class="recharts-cartesian-axis recharts-yAxis yAxis"><line stroke="#666" fill="none" x1="' . $left . '" y1="' . $top . '" x2="' . $left . '" y2="' . $bottom . '"></line>';
        foreach ($yTicks as $t) {
            $ty = $y($t);
            $axes .= '<line stroke="#666" fill="none" x1="' . ($left - 6) . '" y1="' . self::n($ty) . '" x2="' . $left . '" y2="' . self::n($ty) . '"></line>'
                . '<text class="recharts-text recharts-cartesian-axis-tick-value" x="' . ($left - 8) . '" y="' . self::n(max($ty, $top + 3)) . '" text-anchor="end" fill="#666" dominant-baseline="central">' . self::e($format($t)) . '</text>';
        }
        $axes .= '</g><g class="recharts-cartesian-axis recharts-xAxis xAxis"><line stroke="#666" fill="none" x1="' . $left . '" y1="' . $bottom . '" x2="' . $right . '" y2="' . $bottom . '"></line>';
        foreach ($xTicks as $t) {
            $tx = $x($t);
            $axes .= '<line stroke="#666" fill="none" x1="' . self::n($tx) . '" y1="' . ($bottom + 6) . '" x2="' . self::n($tx) . '" y2="' . $bottom . '"></line>'
                . '<text class="recharts-text recharts-cartesian-axis-tick-value" x="' . self::n(min($tx, $right - 2)) . '" y="' . ($bottom + 16) . '" text-anchor="middle" fill="#666">' . (int)$t . '</text>';
        }
        $axes .= '</g>';
        return self::wrap($w, $h, $grid . $area . $axes . $dots);
    }

    /**
     * Bars per day: for each day a pair of bars (gained above the axis, lost below it). $points: [day => ['in' => n, 'out' => n]].
     */
    public static function bars(array $points, array $options = []): string
    {
        $w = $options['width'] ?? 320;
        $h = $options['height'] ?? 270;
        $inColor = $options['inColor'] ?? '#eda932';
        $outColor = $options['outColor'] ?? '#c35742';
        $format = $options['format'] ?? function ($v) {
            return (string)$v;
        };
        $inTitle = $options['inTitle'] ?? '';
        $outTitle = $options['outTitle'] ?? '';
        $left = 55;
        $top = 30;
        $right = $w - 5;
        $bottom = $h - 30;
        ksort($points);
        if (!$points) {
            $points = [0 => ['in' => 0, 'out' => 0]];
        }
        $maxIn = max(1, max(array_column($points, 'in')));
        $maxOut = max(array_column($points, 'out'));
        // losses below the axis get at least a fifth of the height so the labels of both sides stay readable
        $outShare = $maxOut > 0 ? max(0.2, $maxOut / ($maxIn + $maxOut)) : 0;
        $zero = $bottom - ($bottom - $top) * $outShare;
        $unitIn = ($zero - $top) / $maxIn;
        $unitOut = $maxOut > 0 ? ($bottom - $zero) / $maxOut : 0;
        $days = array_keys($points);
        $slot = ($right - $left) / count($days);
        $barW = max(2, min(18, $slot * 0.7));
        $body = '<g class="recharts-cartesian-grid">';
        foreach ([$top, $zero, $bottom] as $gy) {
            $body .= '<line stroke-dasharray="3 3" stroke="#ccc" x1="' . $left . '" y1="' . self::n($gy) . '" x2="' . $right . '" y2="' . self::n($gy) . '"></line>';
        }
        $body .= '</g><g class="recharts-layer recharts-bar">';
        foreach ($days as $i => $d) {
            $bx = $left + $slot * $i + ($slot - $barW) / 2;
            $in = (int)$points[$d]['in'];
            $out = (int)$points[$d]['out'];
            if ($in > 0) {
                $bh = $in * $unitIn;
                $body .= '<rect x="' . self::n($bx) . '" y="' . self::n($zero - $bh) . '" width="' . self::n($barW) . '" height="' . self::n($bh) . '" fill="' . $inColor . '"><title>' . self::e($d . ': ' . $inTitle . ' ' . $format($in)) . '</title></rect>';
            }
            if ($out > 0) {
                $bh = $out * $unitOut;
                $body .= '<rect x="' . self::n($bx) . '" y="' . self::n($zero) . '" width="' . self::n($barW) . '" height="' . self::n($bh) . '" fill="' . $outColor . '"><title>' . self::e($d . ': ' . $outTitle . ' −' . $format($out)) . '</title></rect>';
            }
        }
        $body .= '</g><g class="recharts-cartesian-axis">'
            . '<line stroke="#666" x1="' . $left . '" y1="' . $top . '" x2="' . $left . '" y2="' . $bottom . '"></line>'
            . '<line stroke="#666" x1="' . $left . '" y1="' . self::n($zero) . '" x2="' . $right . '" y2="' . self::n($zero) . '"></line>'
            . '<text x="' . ($left - 6) . '" y="' . ($top + 4) . '" text-anchor="end" fill="#666">' . self::e($format($maxIn)) . '</text>'
            . '<text x="' . ($left - 6) . '" y="' . self::n($zero + 4) . '" text-anchor="end" fill="#666">0</text>';
        if ($maxOut > 0) {
            $body .= '<text x="' . ($left - 6) . '" y="' . $bottom . '" text-anchor="end" fill="#666">−' . self::e($format($maxOut)) . '</text>';
        }
        $step = max(1, (int)ceil(count($days) / 6));
        foreach ($days as $i => $d) {
            if ($i % $step == 0 || $i == count($days) - 1) {
                $body .= '<text x="' . self::n($left + $slot * $i + $slot / 2) . '" y="' . ($bottom + 16) . '" text-anchor="middle" fill="#666">' . (int)$d . '</text>';
            }
        }
        $body .= '</g>';
        return self::wrap($w, $h, $body);
    }
}
