<?

namespace Bitrix\UI\Toolbar;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\UI\Buttons\BaseButton;
use Bitrix\UI\Buttons\Button;

class Toolbar
{
    private $id;
    private $filter;
    private $titleMinWidth;
    private $titleMaxWidth;
    private $favoriteStar = true;

    /**
     * @param Button[] $buttons
     */
    private $afterTitleButtons = [];
    /**
     * @param Button[] $buttons
     */
    private $buttons = [];
    private $filterButtons = [];
    private $options;

    /**
     * Toolbar constructor.
     *
     * @param string $id
     * @param array $options
     */
    public function __construct($id, $options)
    {
        $this->id = $id;
        $this->options = $options;

        if (isset($this->options['filter'])) {
            $this->addFilter($this->options['filter']);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array|Button $button
     * @param string $location
     * @throws ArgumentTypeException
     * @see ButtonLocation
     *
     */
    public function addButton($button, $location = ButtonLocation::RIGHT)
    {
        if (is_array($button)) {
            $button = new Button($button);
        }

        if (!($button instanceof Button)) {
            throw new ArgumentTypeException("button", Button::class);
        }

        if ($location === ButtonLocation::AFTER_FILTER) {
            $this->filterButtons[] = $button;
        } elseif ($location === ButtonLocation::AFTER_TITLE) {
            $this->afterTitleButtons[] = $button;
        } else {
            $this->buttons[] = $button;
        }
    }

    public function deleteButtons(\Closure $closure)
    {
        foreach ($this->buttons as $i => $button) {
            if ($closure($button, ButtonLocation::RIGHT) === true) {
                unset($this->buttons[$i]);
            }
        }

        foreach ($this->filterButtons as $i => $button) {
            if ($closure($button, ButtonLocation::AFTER_FILTER) === true) {
                unset($this->filterButtons[$i]);
            }
        }
    }

    public function shuffleButtons(\Closure $closure, $buttonLocation)
    {
        $buttonList = null;
        switch ($buttonLocation) {
            case ButtonLocation::RIGHT:
                $buttonList = $this->buttons;
                break;
            case ButtonLocation::AFTER_FILTER:
                $buttonList = $this->filterButtons;
                break;
        }

        if ($buttonList) {
            $buttonList = $closure($buttonList);
            if (!is_array($buttonList)) {
                throw new ArgumentTypeException('buttonList', 'array');
            }

            switch ($buttonLocation) {
                case ButtonLocation::RIGHT:
                    $this->buttons = $buttonList;
                    break;
                case ButtonLocation::AFTER_FILTER:
                    $this->filterButtons = $buttonList;
                    break;
            }
        }
    }

    public function hasFavoriteStar()
    {
        return (bool)$this->favoriteStar;
    }

    public function addFavoriteStar()
    {
        $this->favoriteStar = true;

        return $this;
    }

    public function deleteFavoriteStar()
    {
        $this->favoriteStar = false;

        return $this;
    }

    public function addFilter(array $filterOptions = [])
    {
        ob_start();
        $GLOBALS['APPLICATION']->includeComponent('bitrix:main.ui.filter', '', $filterOptions);
        $this->filter = ob_get_clean();
    }

    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return BaseButton[]
     */
    public function getButtons()
    {
        return array_merge($this->afterTitleButtons, $this->filterButtons, $this->buttons);
    }

    public function renderAfterTitleButtons()
    {
        return implode(
            array_map(
                function (Button $button) {
                    return self::processButtonRender($button);
                },
                $this->afterTitleButtons
            )
        );
    }

    public function renderRightButtons()
    {
        return implode(
            array_map(
                function (Button $button) {
                    return self::processButtonRender($button);
                },
                $this->buttons
            )
        );
    }

    public function renderAfterFilterButtons()
    {
        return implode(
            array_map(
                function (Button $button) {
                    return self::processButtonRender($button);
                },
                $this->filterButtons
            )
        );
    }

    /**
     * @return string
     * @deprecated
     */
    public function renderFilterRightButtons()
    {
        return $this->renderAfterFilterButtons();
    }

    protected function processButtonRender(Button $button)
    {
        $shouldAddThemeModifier = (bool)array_intersect(
            $button->getClassList(),
            [
                'ui-btn-light-border',
                'ui-btn-light',
                'ui-btn-link',
            ]
        );

        if ($shouldAddThemeModifier) {
            $button->addClass('ui-btn-themes');
        }

        return $button->render(false);
    }

    public function setTitleMinWidth($width)
    {
        if (is_int($width) && $width > 0) {
            $this->titleMinWidth = $width;
        }
    }

    public function getTitleMinWidth()
    {
        return $this->titleMinWidth;
    }

    public function setTitleMaxWidth($width)
    {
        if (is_int($width) && $width > 0) {
            $this->titleMaxWidth = $width;
        }
    }

    public function getTitleMaxWidth()
    {
        return $this->titleMaxWidth;
    }
}