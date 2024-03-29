<?php

namespace Bitrix\Landing\Hook\Page;

use Bitrix\Landing\Field;
use Bitrix\Landing\Hook\Page;
use Bitrix\Main\Application;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use Bitrix\Landing\Restriction;
use Bitrix\Main\Text\HtmlFilter;

Loc::loadMessages(__FILE__);

class Theme extends Page
{
    protected const DEFAULT_COLOR = '#34bcf2';

    /**
     * Map of the field.
     * @return array
     */
    protected function getMap()
    {
        return [
            'CODE' => new Field\Select(
                'CODE', [
                'title' => Loc::getMessage('LANDING_HOOK_THEMECODE_NEW'),
                'options' => array_merge(
                    [
                        '' => [
                            'color' => '#f0f0f0',
                        ]
                    ],
                    self::getColorCodes()
                ),
            ]
            ),
            'USE' => new Field\Checkbox(
                'USE', [
                'title' => Loc::getMessage('LANDING_HOOK_THEME_CUSTOM_USE')
            ]
            ),
            'COLOR' => new Field\Text(
                'COLOR', [
                'title' => Loc::getMessage('LANDING_HOOK_THEME_CUSTOM_COLOR')
            ]
            ),
        ];
    }

    /**
     * Get all themes colors.
     * @return array
     */
    public static function getColorCodes(): array
    {
        static $colors = [
            '2business' => [
                'color' => '#3949a0',
                'main' => '#333333',
                'base' => true,
            ],
            'gym' => [
                'color' => '#6b7de0',
                'main' => '#444444',
            ],
            '3corporate' => [
                'color' => '#6ab8ee',
                'main' => '#12222d',
                'secondary' => '#fafbfc',
                'base' => true,
            ],
            'wiki-dark' => [
                'color' => '#60e7f5',
            ],
            'app' => [
                'color' => '#4fd2c2',
                'main' => '#999999',
                'colorTitle' => '#111111',
                'base' => true,
            ],
            'consulting' => [
                'color' => '#21a79b',
                'main' => '#464c5e',
                'secondary' => '#f5fafa',
            ],
            'courses' => [
                'color' => '#6bda95',
                'main' => '#999999',
                'colorTitle' => '#000000',
            ],
            'accounting' => [
                'color' => '#a5c33c',
                'main' => '#999999',
                'base' => true,
            ],
            'spa' => [
                'color' => '#9dba04',
                'main' => '#999999',
                'colorTitle' => '#000000',
            ],
            'charity' => [
                'color' => '#f5f219',
                'main' => '#999999',
                'colorTitle' => '#111111',
            ],
            '1construction' => [
                'color' => '#f7b70b',
                'main' => '#a7a7a7',
                'base' => true,
            ],
            'travel' => [
                'color' => '#ee4136',
                'main' => '#333333',
            ],
            'architecture' => [
                'color' => '#c94645',
                'main' => '#7d7d8f',
                'colorTitle' => '#383339',
            ],
            'event' => [
                'color' => '#f73859',
                'main' => '#979aa7',
                'secondary' => '#1a2e39',
                'colorTitle' => '#151826',
            ],
            'lawyer' => [
                'color' => '#e74c3c',
                'main' => '#444444',
                'colorTitle' => '#4e4353',
            ],
            'real-estate' => [
                'color' => '#f74c3c',
                'main' => '#1a2e39',
                'secondary' => '#1a2e39',
                'base' => true,
            ],
            'restaurant' => [
                'color' => '#e6125d',
                'main' => '#444444',
                'colorTitle' => '#222222',
            ],
            'shipping' => [
                'color' => '#ff0000',
                'main' => '#444444',
                'colorTitle' => '#2c2c2c',
            ],
            'agency' => [
                'color' => '#fe6466',
                'main' => '#a49da6',
                'colorTitle' => '#383339',
            ],
            'music' => [
                'color' => '#fe6476',
                'main' => '#999999',
                'colorTitle' => '#2f2f2f',
            ],
            'wedding' => [
                'color' => '#d65779',
                'main' => '#444444',
                'colorTitle' => '#222222',
            ],
            'photography' => [
                'color' => '#333333',
                'main' => '#444444',
                'colorTitle' => '#333333',
                'base' => true,
            ],
        ];

        $event = new Event(
            'landing', 'onGetThemeColors', [
            'colors' => $colors
        ]
        );
        $event->send();
        foreach ($event->getResults() as $result) {
            if ($result->getType() != \Bitrix\Main\EventResult::ERROR) {
                if (($modified = $result->getModified())) {
                    if (isset($modified['colors'])) {
                        $colors = $modified['colors'];
                    }
                }
            }
        }

        if (
            !is_array($colors) ||
            empty($colors)
        ) {
            $colors = [
                '1construction' => [
                    'color' => '#f7b70b',
                    'base' => true
                ]
            ];
        }

        return $colors;
    }

    /**
     * Get hex for all colors.
     * @return array
     */
    public static function getAllColorCodes(): array
    {
        $colors = self::getColorCodes();
        $allColors = [];
        foreach ($colors as $colorItem) {
            if (isset($colorItem['color'])) {
                $allColors[] = $colorItem['color'];
            }
        }
        return $allColors;
    }

    /**
     * Get hex for start colors.
     * @return array
     */
    public static function getStartColorCodes(): array
    {
        $colors = self::getColorCodes();
        $startColors = [];
        foreach ($colors as $colorItem) {
            if (isset($colorItem['base']) && $colorItem['base'] === true) {
                $startColors[] = $colorItem['color'];
            }
        }
        return $startColors;
    }

    /**
     * Find theme name (old format) by hex color
     * @param string $hexColor (with lead #)
     * @return string|null
     */
    protected static function getThemeCodeByColor(string $hexColor): ?string
    {
        $colors = self::getColorCodes();
        foreach ($colors as $code => $color) {
            if ($color['color'] === $hexColor) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Enable or not the hook.
     * @return boolean
     */
    public function enabled()
    {
        if ($this->issetCustomExec()) {
            return true;
        }

        if ($this->isPage()) {
            if (
                $this->fields['CODE']->getValue()
                && !$this->fields['COLOR']->getValue()
            ) {
                return true;
            }
            return $this->fields['USE']->getValue() === 'Y';
        }

        return true;
    }

    /**
     * @param string $hexColor
     * @return array
     */
    protected static function convertHexToRgb(string $hexColor): array
    {
        if ($hexColor[0] !== '#') {
            $hexColor = '#' . $hexColor;
        }
        if (strlen($hexColor) === 4) {
            $hexColor =
                $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2] . $hexColor[3] . $hexColor[3];
        }
        if (strlen($hexColor) !== 7) {
            $hexColor = self::DEFAULT_COLOR;
        }

        return [
            hexdec(substr($hexColor, 1, 2)),
            hexdec(substr($hexColor, 3, 2)),
            hexdec(substr($hexColor, 5, 2)),
        ];
    }

    /**
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     * @return array [hue, saturation, lightness]
     */
    protected static function convertRgbToHsl(int $red, int $green, int $blue): array
    {
        $red /= 255;
        $green /= 255;
        $blue /= 255;
        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);
        $lightness = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            $hue = $saturation = 0;
        } else {
            $hue = 0;
            $saturation = $d / (1 - abs(2 * $lightness - 1));
            switch ($max) {
                case $red:
                    $hue = 60 * fmod((($green - $blue) / $d), 6);
                    if ($blue > $green) {
                        $hue += 360;
                    }
                    break;

                case $green:
                    $hue = 60 * (($blue - $red) / $d + 2);
                    break;

                case $blue:
                    $hue = 60 * (($red - $green) / $d + 4);
                    break;

                default:
                    break;
            }
        }

        return [
            round($hue, 2),
            round($saturation, 2) * 100,
            round($lightness, 2) * 100,
        ];
    }

    /**
     * Exec hook.
     * @return void
     */
    public function exec(): void
    {
        $defaultColors = self::getColorCodes();

        // get color from request or from settings
        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->get('color')) {
            $colorHex = $request->get('color');
        } elseif (
            ($themeCodeFromRequest = $request->get('theme'))
            && array_key_exists($themeCodeFromRequest, $defaultColors)
        ) {
            $themeCode = $themeCodeFromRequest;
            $colorHex = $defaultColors[$themeCodeFromRequest]['color'];
        } else {
            $colorHex = HtmlFilter::encode(trim($this->fields['COLOR']->getValue()));
            if (!$colorHex) {
                $themeCode = HtmlFilter::encode(trim($this->fields['CODE']->getValue()));
                $colorHex = $themeCode ? $defaultColors[$themeCode]['color'] : self::DEFAULT_COLOR;
            }
        }

        $colorHex = self::prepareColor($colorHex);

        $restrictionCode = Restriction\Hook::getRestrictionCodeByHookCode('THEME');
        if (
            !Restriction\Manager::isAllowed($restrictionCode)
            && !self::getThemeCodeByColor($colorHex)
        ) {
            $colorHex = self::DEFAULT_COLOR;
        }

        // print
        $rgbColor = self::convertHexToRgb($colorHex);
        $rgbTemplate = $rgbColor[0] . ', ' . $rgbColor[1] . ', ' . $rgbColor[2];
        $hslColor = self::convertRgbToHsl($rgbColor[0], $rgbColor[1], $rgbColor[2]);

        if (
            isset($themeCode)
            || ($themeCode = self::getThemeCodeByColor($colorHex))
        ) {
            $colorMain = $defaultColors[$themeCode]['main'];
            if ($defaultColors[$themeCode]['secondary']) {
                $colorSecondary = $defaultColors[$themeCode]['secondary'];
            }
            if ($defaultColors[$themeCode]['colorTitle']) {
                $colorTitle = $defaultColors[$themeCode]['colorTitle'];
            }
        }
        $colorMain = $colorMain ?? 'hsl(' . $hslColor[0] . ', 20%, 20%)';
        $colorSecondary = $colorSecondary ?? 'hsl(' . $hslColor[0] . ', 20%, 80%)';
        $colorTitle = $colorTitle ?? $colorMain;

        if ($hslColor[2] > 60) {
            $colorStrictInverseFromPrimary = '#000000';
        } else {
            $colorStrictInverseFromPrimary = '#ffffff';
        }

        Asset::getInstance()->addString(
            '<style type="text/css">
				:root {
					--theme-color-primary: ' . $colorHex . ';
					--theme-color-primary-darken-1: hsl(' . $hslColor[0] . ', ' . $hslColor[1] . '%, ' . min(
                $hslColor[2] - 2,
                100
            ) . '%)' . ';
					--theme-color-primary-darken-2: hsl(' . $hslColor[0] . ', ' . $hslColor[1] . '%, ' . min(
                $hslColor[2] - 5,
                100
            ) . '%)' . ';
					--theme-color-primary-darken-3: hsl(' . $hslColor[0] . ', ' . $hslColor[1] . '%, ' . min(
                $hslColor[2] - 10,
                100
            ) . '%)' . ';
					--theme-color-primary-lighten-1: hsl(' . $hslColor[0] . ', ' . $hslColor[1] . '%, ' . max(
                $hslColor[2] + 10,
                0
            ) . '%)' . ';
					--theme-color-primary-opacity-0_1: rgba(' . $rgbTemplate . ', 0.1);
					--theme-color-primary-opacity-0_2: rgba(' . $rgbTemplate . ', 0.2);
					--theme-color-primary-opacity-0_3: rgba(' . $rgbTemplate . ', 0.3);
					--theme-color-primary-opacity-0_4: rgba(' . $rgbTemplate . ', 0.4);
					--theme-color-primary-opacity-0_6: rgba(' . $rgbTemplate . ', 0.6);
					--theme-color-primary-opacity-0_8: rgba(' . $rgbTemplate . ', 0.8);
					--theme-color-primary-opacity-0_9: rgba(' . $rgbTemplate . ', 0.9);
					--theme-color-main: ' . $colorMain . ';
					--theme-color-secondary: ' . $colorSecondary . ';
					--theme-color-title: ' . $colorTitle . ';
					--theme-color-strict-inverse: ' . $colorStrictInverseFromPrimary . ';
				}
			</style>',
            false,
            AssetLocation::BEFORE_CSS
        );
    }

    public static function prepareColor(string $color): string
    {
        $color = HtmlFilter::encode(trim($color));

        if ($color[0] !== '#') {
            $color = '#' . $color;
        }
        if (!self::isHex($color)) {
            return self::DEFAULT_COLOR;
        }
        if (mb_strlen($color) === 4) {
            $color = $color[0] . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
        }

        return $color;
    }

    public static function isHex(string $color): bool
    {
        return (bool)preg_match('/^#([\da-f]{3}){1,2}$/i', $color);
    }
}
