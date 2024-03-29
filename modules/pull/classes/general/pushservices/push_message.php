<?php

abstract class CPushMessage
{
    protected $deviceTokens = array();
    protected $text;
    protected $category;
    protected $badge;
    protected $sound = "default";
    protected $expiryValue = 7200;

    protected $customIdentifier;
    protected $title;
    public $customProperties = array();

    public function addRecipient($sDeviceToken)
    {
        $this->deviceTokens[] = $sDeviceToken;
    }

    public function getRecipient($nRecipient = 0)
    {
        if (!isset($this->deviceTokens[$nRecipient])) {
            throw new Exception(
                "No recipient at index '{$nRecipient}'"
            );
        }

        return $this->deviceTokens[$nRecipient];
    }

    public function getRecipients()
    {
        return $this->deviceTokens;
    }

    public function setText($sText)
    {
        $this->text = str_replace("\n", " ", $sText);
    }

    public function getText()
    {
        return $this->text;
    }

    public function setTitle($sTitle)
    {
        $this->title = $sTitle;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBadge($nBadge)
    {
        if (!is_int($nBadge)) {
            throw new Exception(
                "Invalid badge number '{$nBadge}'"
            );
        }
        $this->badge = $nBadge;
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function setSound($sSound = 'default')
    {
        $this->sound = $sSound;
    }

    public function getSound()
    {
        return $this->sound;
    }

    public function setCustomProperty($sName, $mValue)
    {
        $this->customProperties[trim($sName)] = $mValue;
    }

    public function getCustomProperty($sName)
    {
        if (!array_key_exists($sName, $this->customProperties)) {
            throw new Exception(
                "No property exists with the specified name '{$sName}'."
            );
        }

        return $this->customProperties[$sName];
    }

    public function setExpiry($nExpiryValue)
    {
        if (is_int($nExpiryValue)) {
            $this->expiryValue = $nExpiryValue;
        }
    }

    public function getExpiry()
    {
        return $this->expiryValue;
    }

    public function setCustomIdentifier($mCustomIdentifier)
    {
        $this->customIdentifier = $mCustomIdentifier;
    }

    public function getCustomIdentifier()
    {
        return $this->customIdentifier;
    }

    abstract function getBatch();

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setFromArray(array $messageArray)
    {
        $text = \Bitrix\Main\Text\Encoding::convertEncoding($messageArray["MESSAGE"], SITE_CHARSET, "utf-8");
        $title = \Bitrix\Main\Text\Encoding::convertEncoding($messageArray["TITLE"], SITE_CHARSET, "utf-8");
        $this->setSound('');
        $this->setText($text);
        $this->setTitle($title);
        if ($text <> '') {
            $this->setSound(
                ($messageArray["SOUND"] <> '')
                    ? $messageArray["SOUND"]
                    : "default"
            );
        }

        if ($messageArray["CATEGORY"]) {
            $this->setCategory($messageArray["CATEGORY"]);
        }

        if (array_key_exists("EXPIRY", $messageArray)) {
            $expiry = (int)$messageArray["EXPIRY"];
            $this->setExpiry($expiry >= 0 ? $expiry : CPushService::DEFAULT_EXPIRY);
        }

        if ($messageArray["PARAMS"]) {
            $this->setCustomProperty(
                'params',
                is_array($messageArray["PARAMS"])
                    ? json_encode($messageArray["PARAMS"])
                    : $messageArray["PARAMS"]
            );
        }

        if (is_array($messageArray["ADVANCED_PARAMS"])) {
            $messageArray["ADVANCED_PARAMS"] = \Bitrix\Main\Text\Encoding::convertEncoding(
                $messageArray["ADVANCED_PARAMS"],
                SITE_CHARSET,
                "UTF-8"
            );
            if (array_key_exists("senderMessage", $messageArray["ADVANCED_PARAMS"])) {
                $this->setText("");
            }

            foreach ($messageArray["ADVANCED_PARAMS"] as $param => $value) {
                $this->setCustomProperty($param, $value);
            }
        }

        $badge = (int)$messageArray["BADGE"];
        if (array_key_exists("BADGE", $messageArray) && $badge >= 0) {
            $this->setBadge($badge);
        }
        return $this;
    }

}

