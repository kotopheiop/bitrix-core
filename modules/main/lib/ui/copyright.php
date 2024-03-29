<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2020 Bitrix
 */

namespace Bitrix\Main\UI;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

class Copyright
{
    const LICENCE_MIT = "MIT";
    const LICENCE_COMMERCIAL = "Commercial";
    const LICENCE_PUBLIC_DOMAIN = "Public Domain";
    const LICENCE_BSD2 = "2-Clause BSD";
    const LICENCE_BSD3 = "3-Clause BSD";
    const LICENCE_APACHE2 = "Apache License, Version 2.0";
    const LICENCE_W3C = "W3C License";
    const LICENSE_GPLV2 = "General Public License, version 2";
    const LICENCE_CUSTOM = "License";

    protected
        $productName,
        $productUrl,
        $copyright,
        $copyrightUrl,
        $vendorName,
        $vendorUrl,
        $supportUrl,
        $licence,
        $licenceUrl,
        $licenceText;

    public function __construct($productName)
    {
        $this->productName = $productName;
    }

    /**
     * Returns the product info.
     * @return static
     */
    public static function getBitrixCopyright()
    {
        /*
        if(LANGUAGE_ID == "ru")
            $vendor = "1c_bitrix_portal";
        elseif(LANGUAGE_ID == "ua")
            $vendor = "ua_bitrix_portal";
        else
            $vendor = "bitrix_portal";

        if (LANGUAGE_ID == "ru")
            COption::SetOptionString("main", "vendor", "1c_bitrix");
        else
            COption::SetOptionString("main", "vendor", "bitrix");
        */
        $vendor = Option::get("main", "vendor", "1c_bitrix");

        return (new static(Loc::getMessage("EPILOG_ADMIN_SM_" . $vendor)))
            ->setProductUrl(Loc::getMessage("EPILOG_ADMIN_URL_PRODUCT_" . $vendor))
            ->setCopyright(Loc::getMessage("EPILOG_ADMIN_COPY_" . $vendor))
            ->setVendorName(Loc::getMessage("EPILOG_ADMIN_URL_MAIN_TEXT_" . $vendor))
            ->setVendorUrl(Loc::getMessage("EPILOG_ADMIN_URL_MAIN_" . $vendor))
            ->setSupportUrl(Loc::getMessage("EPILOG_ADMIN_URL_SUPPORT_" . $vendor))
            ->setLicenceUrl("/bitrix/legal/license.php");
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return mixed
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * @return mixed
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @return mixed
     */
    public function getCopyrightUrl()
    {
        return $this->copyrightUrl;
    }

    /**
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * @return mixed
     */
    public function getVendorUrl()
    {
        return $this->vendorUrl;
    }

    /**
     * @return mixed
     */
    public function getSupportUrl()
    {
        return $this->supportUrl;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence ?: static::LICENCE_CUSTOM;
    }

    /**
     * @return mixed
     */
    public function getLicenceUrl()
    {
        static $urls = [
            self::LICENCE_MIT => "https://opensource.org/licenses/MIT",
            self::LICENCE_BSD2 => "https://opensource.org/licenses/BSD-2-Clause",
            self::LICENCE_BSD3 => "https://opensource.org/licenses/BSD-3-Clause",
            self::LICENCE_APACHE2 => "http://www.apache.org/licenses/LICENSE-2.0",
            self::LICENCE_W3C => "https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document",
            self::LICENSE_GPLV2 => "https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt",
        ];

        if ($this->licenceUrl === null && isset($urls[$this->licence])) {
            return $urls[$this->licence];
        }

        return $this->licenceUrl;
    }

    /**
     * @return mixed
     */
    public function getLicenceText()
    {
        return $this->licenceText;
    }

    /**
     * @param mixed $productName
     * @return static
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
        return $this;
    }

    /**
     * @param mixed $productUrl
     * @return static
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;
        return $this;
    }

    /**
     * @param mixed $copyright
     * @return static
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * @param mixed $copyrightUrl
     * @return static
     */
    public function setCopyrightUrl($copyrightUrl)
    {
        $this->copyrightUrl = $copyrightUrl;
        return $this;
    }

    /**
     * @param mixed $vendorName
     * @return static
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
        return $this;
    }

    /**
     * @param mixed $vendorUrl
     * @return static
     */
    public function setVendorUrl($vendorUrl)
    {
        $this->vendorUrl = $vendorUrl;
        return $this;
    }

    /**
     * @param mixed $supportUrl
     * @return static
     */
    public function setSupportUrl($supportUrl)
    {
        $this->supportUrl = $supportUrl;
        return $this;
    }

    /**
     * @param mixed $licence
     * @return static
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
        return $this;
    }

    /**
     * @param mixed $licenceUrl
     * @return Copyright
     */
    public function setLicenceUrl($licenceUrl)
    {
        $this->licenceUrl = $licenceUrl;
        return $this;
    }

    /**
     * @param mixed $licenceText
     * @return static
     */
    public function setLicenceText($licenceText)
    {
        $this->licenceText = $licenceText;
        return $this;
    }

    /**
     * Returns the array of third-party software components, sorted alphabetically.
     * @return static[]
     */
    public static function getThirdPartySoftware()
    {
        $software = static::getMainThirdParty();

        $event = new Main\Event("main", "onGetThirdPartySoftware");
        $event->send();

        foreach ($event->getResults() as $evenResult) {
            if ($evenResult->getType() == Main\EventResult::SUCCESS) {
                $result = $evenResult->getParameters();
                if (is_array($result)) {
                    $software = array_merge($software, $result);
                }
            }
        }

        usort(
            $software,
            function ($a, $b) {
                return strcasecmp($a->getProductName(), $b->getProductName());
            }
        );

        return $software;
    }

    protected static function getMainThirdParty()
    {
        return [
            (new static("jQuery JavaScript Library v1.7"))
                ->setProductUrl("http://jquery.com/")
                ->setCopyright("Copyright 2011, John Resig")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("http://jquery.org/license"),

            (new static("jQuery JavaScript Library v1.8"))
                ->setProductUrl("http://jquery.com/")
                ->setCopyright("Copyright 2012 jQuery Foundation and other contributors")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("http://jquery.org/license"),

            (new static("jQuery JavaScript Library v2.1"))
                ->setProductUrl("http://jquery.com/")
                ->setCopyright("Copyright 2005, 2014 jQuery Foundation, Inc. and other contributors")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("http://jquery.org/license"),

            (new static("jQuery JavaScript Library v3"))
                ->setProductUrl("http://jquery.com/")
                ->setCopyright("Copyright JS Foundation and other contributors")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("http://jquery.org/license"),

            (new static("amCharts"))
                ->setProductUrl("https://www.amcharts.com/")
                ->setCopyright("Copyright (c) 2018 amCharts (Antanas Marcelionis, Martynas Majeris)")
                ->setLicence(static::LICENCE_COMMERCIAL),

            (new static("React"))
                ->setProductUrl("https://reactjs.org/")
                ->setCopyright("Copyright (c) Facebook, Inc. and its affiliates")
                ->setLicence(static::LICENCE_MIT),

            (new static("PhotoEditorSDK"))
                ->setProductUrl("https://photoeditorsdk.com/")
                ->setCopyright("Copyright (C) 2016-2019 img.ly GmbH <contact@img.ly>")
                ->setLicence(static::LICENCE_COMMERCIAL),

            (new static("json2.js"))
                ->setProductUrl("https://github.com/douglascrockford/JSON-js")
                ->setCopyright("Douglas Crockford")
                ->setLicence(static::LICENCE_PUBLIC_DOMAIN),

            (new static("js-md5"))
                ->setProductUrl("https://github.com/emn178/js-md5")
                ->setCopyright("copyright Chen, Yi-Cyuan 2014-2017")
                ->setLicence(static::LICENCE_MIT),

            (new static("js-sha1"))
                ->setProductUrl("https://github.com/emn178/js-sha1")
                ->setCopyright("copyright Chen, Yi-Cyuan 2014-2017")
                ->setLicence(static::LICENCE_MIT),

            (new static("IntersectionObserver"))
                ->setProductUrl("https://github.com/w3c/IntersectionObserver/")
                ->setCopyright("Copyright 2016 Google Inc.")
                ->setLicence(static::LICENCE_W3C),

            (new static("QRCode for JavaScript"))
                ->setProductUrl("https://kazuhikoarase.github.io/qrcode-generator/")
                ->setCopyright("Copyright (c) 2009 Kazuhiko Arase")
                ->setLicence(static::LICENCE_MIT),

            (new static("lamejs"))
                ->setProductUrl("https://github.com/zhuker/lamejs")
                ->setCopyright("Alex Zhukov")
                ->setLicenceUrl("https://raw.githubusercontent.com/zhuker/lamejs/master/LICENSE"),

            (new static("WebRTC adapter"))
                ->setProductUrl("https://github.com/webrtchacks/adapter")
                ->setCopyright("Copyright (c) 2017 The WebRTC project authors")
                ->setLicence(static::LICENCE_BSD3)
                ->setLicenceUrl("https://raw.githubusercontent.com/webrtcHacks/adapter/master/LICENSE.md"),

            (new static("Base64 js"))
                ->setCopyright("Tyler Akins")
                ->setLicence(static::LICENCE_PUBLIC_DOMAIN),

            (new static("BigInt js"))
                ->setCopyright("Copyright 1998-2005 David Shapiro")
                ->setLicenceText(
                    "You may use, re-use, abuse, copy, and modify this code to your liking, but please keep this header.

Thanks!
"
                ),

            (new static("BarrettMu js"))
                ->setCopyright("Copyright 2004-2005 David Shapiro")
                ->setLicenceText(
                    "You may use, re-use, abuse, copy, and modify this code to your liking, but please keep this header.

Thanks!
"
                ),

            (new static("RSA js"))
                ->setCopyright("Copyright 1998-2005 David Shapiro")
                ->setLicenceText(
                    "You may use, re-use, abuse, copy, and modify this code to your liking, but please keep this header.

Thanks!
"
                ),

            (new static("PHP-JWT"))
                ->setProductUrl("https://github.com/fproject/php-jwt")
                ->setCopyright("Bui Sy Nguyen <nguyenbs@gmail.com>")
                ->setLicence(static::LICENCE_BSD3)
                ->setLicenceUrl("https://github.com/fproject/php-jwt/blob/master/LICENSE"),

            (new static("PHP-JWT"))
                ->setProductUrl("https://github.com/firebase/php-jwt")
                ->setCopyright("Neuman Vong <neuman@twilio.com>, Anant Narayanan <anant@php.net>")
                ->setLicence(static::LICENCE_BSD3)
                ->setLicenceUrl("https://github.com/firebase/php-jwt/blob/master/LICENSE"),

            (new static("Fonts \"PT Sans\", \"PT Serif\""))
                ->setProductUrl("http://www.paratype.com/public")
                ->setCopyright("Copyright (c) 2010, ParaType Ltd.")
                ->setLicence("SIL Open Font License, Version 1.1")
                ->setLicenceUrl("http://scripts.sil.org/OFL"),

            (new static("Bootstrap v3.3"))
                ->setProductUrl("https://getbootstrap.com/")
                ->setCopyright("Copyright 2011-2016 Twitter, Inc.")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/twbs/bootstrap/blob/main/LICENSE"),

            (new static("Bootstrap v4"))
                ->setProductUrl("https://getbootstrap.com/")
                ->setCopyright("Copyright 2011-2019 The Bootstrap Authors. Copyright 2011-2019 Twitter, Inc.")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/twbs/bootstrap/blob/main/LICENSE"),

            (new static("Font Awesome 4"))
                ->setProductUrl("http://fontawesome.io")
                ->setCopyright("by @davegandy")
                ->setLicence("Font: SIL OFL 1.1, CSS: MIT License")
                ->setLicenceUrl("http://fontawesome.io/license"),

            (new static("Open Sans Font"))
                ->setProductUrl("https://fonts.google.com/specimen/Open+Sans")
                ->setCopyright("Google Inc.")
                ->setLicence(static::LICENCE_APACHE2),

            (new static("Flags Images"))
                ->setProductUrl("http://www.gosquared.com/")
                ->setCopyright("Copyright (c) 2013 Go Squared Ltd")
                ->setLicenceText(
                    "Copyright (c) 2013 Go Squared Ltd. http://www.gosquared.com/

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the \"Software\"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
"
                ),

            (new static("Babel"))
                ->setProductUrl("https://github.com/babel/babel")
                ->setCopyright("(c) 2018 Babel")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/babel/babel/blob/main/LICENSE"),

            (new static("Babel Regenerator Runtime"))
                ->setCopyright("Copyright (c) 2014-present, Facebook, Inc.")
                ->setLicence(static::LICENCE_MIT),

            (new static("PSR Container"))
                ->setProductUrl("https://github.com/container-interop/fig-standards/")
                ->setCopyright(
                    "Copyright (c) 2013-2016 container-interop. Copyright (c) 2016 PHP Framework Interoperability Group"
                )
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/container-interop/fig-standards/blob/master/LICENSE-MIT.md"),

            (new static("D3.js"))
                ->setProductUrl("https://d3js.org")
                ->setCopyright("Copyright 2020 Mike Bostock")
                ->setLicence(static::LICENCE_BSD3)
                ->setLicenceUrl("https://github.com/d3/d3/blob/master/LICENSE"),

            (new static("Swiffy runtime"))
                ->setCopyright("Copyright 2014 Google Inc.")
                ->setLicenceText(
                    "Copyright 2014 Google Inc.

Swiffy runtime version 7.2.0

In addition to the Google Terms of Service (http://www.google.com/accounts/TOS), Google grants you and the Google Swiffy end users a personal, worldwide, royalty-free, non-assignable and non-exclusive license to use the Google Swiffy runtime to host it for Google Swiffy end users and to use it in connection with the Google Swiffy service.
"
                ),

            (new static("mustache.js"))
                ->setProductUrl("http://github.com/janl/mustache.js")
                ->setCopyright(
                    "Copyright (c) 2009 Chris Wanstrath (Ruby). Copyright (c) 2010-2014 Jan Lehnardt (JavaScript). Copyright (c) 2010-2015 The mustache.js community"
                )
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/janl/mustache.js/blob/master/LICENSE"),

            (new static("PDF.js"))
                ->setProductUrl("https://github.com/mozilla/pdf.js")
                ->setCopyright("Copyright 2018 Mozilla Foundation")
                ->setLicence(static::LICENCE_APACHE2)
                ->setLicenceUrl("http://www.apache.org/licenses/LICENSE-2.0"),

            (new static("Highlight.js"))
                ->setProductUrl("https://github.com/highlightjs/highlight.js")
                ->setCopyright("Copyright (c) 2006, Ivan Sagalaev")
                ->setLicence(static::LICENCE_BSD3)
                ->setLicenceUrl("https://github.com/highlightjs/highlight.js/blob/master/LICENSE"),

            // for landing
            (new static("Animate.css"))
                ->setProductUrl("http://daneden.me/animate")
                ->setCopyright("Copyright (c) 2017 Daniel Eden")
                ->setLicence(static::LICENCE_MIT),

            (new static("jQuery Easing v1.3"))
                ->setProductUrl("http://gsgd.co.uk/sandbox/jquery/easing/")
                ->setCopyright("Copyright (c) 2008 George McGinley Smith")
                ->setLicence(static::LICENCE_BSD3),

            (new static("The Final Countdown for jQuery v2.2.0"))
                ->setProductUrl("http://hilios.github.io/jQuery.countdown/")
                ->setCopyright("Copyright (c) 2016 Edson Hilios")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/hilios/jQuery.countdown/blob/master/LICENSE.md"),

            (new static("Slick carousel"))
                ->setProductUrl("https://github.com/kenwheeler/slick/")
                ->setCopyright("Copyright (c) 2017 Ken Wheeler")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/kenwheeler/slick/blob/master/LICENSE"),

            (new static("FancyBox v3.2.5"))
                ->setProductUrl("http://fancyapps.com/fancybox/")
                ->setCopyright("Copyright 2017 fancyApps")
                ->setLicence(static::LICENCE_COMMERCIAL)

            (
                new static("Simple Line Icons")
            )
				->setProductUrl("https://simplelineicons.github.io/")
        ->setCopyright("Originally brought to you by Sabbir & Contributors")
        ->setLicence(static::LICENCE_MIT),

			(new static("Hamburgers.css"))
                ->setProductUrl("https://jonsuh.com/hamburgers/")
                ->setCopyright("@author Jonathan Suh @jonsuh")
                ->setLicence(static::LICENCE_MIT)
                ->setLicenceUrl("https://github.com/jonsuh/hamburgers/blob/master/LICENSE"),

			(new static("Unify - Responsive Website Template v2.0.0"))
                ->setProductUrl("https://wrapbootstrap.com/theme/unify-responsive-website-template-WB0412697")
                ->setCopyright("Author: Htmlstream")
                ->setLicence(static::LICENCE_COMMERCIAL),

			(new static("Unsplash"))
                ->setProductUrl("https://unsplash.com/")
                ->setLicenceText(
                    "Unsplash grants you an irrevocable, nonexclusive, worldwide copyright license to download, copy, modify, distribute, perform, and use photos from Unsplash for free, including for commercial purposes, without permission from or attributing the photographer or Unsplash. This license does not include the right to compile photos from Unsplash to replicate a similar or competing service."
                ),

			(new static("Dexie.js - a minimalistic wrapper for IndexedDB v2.0.4"))
                ->setCopyright("by David Fahlander, david.fahlander@gmail.com, Fri May 25 2018")
                ->setProductUrl("http://dexie.org")
                ->setLicence(static::LICENCE_APACHE2)
                ->setLicenceUrl("http://www.apache.org/licenses/"),

			(new static("ProgressBar.js v1.1.0"))
                ->setCopyright("Copyright 2016, Kimmo Brunfeldt")
                ->setProductUrl("https://kimmobrunfeldt.github.io/progressbar.js")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vue.js v2.6.12"))
                ->setCopyright("Copyright 2014-2020, Evan You")
                ->setProductUrl("https://vuejs.org")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vue-router v3.5.1"))
                ->setCopyright("Copyright 2021, Evan You")
                ->setProductUrl("https://router.vuejs.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vuex v3.6.2"))
                ->setCopyright("Copyright 2021, Evan You")
                ->setProductUrl("https://vuex.vuejs.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Portal Vue v2.1.7"))
                ->setCopyright("Copyright 2019, Thorsten Lunborg")
                ->setProductUrl("https://portal-vue.linusb.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vue.js v3.0.5"))
                ->setCopyright("Copyright 2014-2021, Evan You")
                ->setProductUrl("https://v3.vuejs.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vue-router v4.0.3"))
                ->setCopyright("Copyright 2021, Eduardo San Martin Morote")
                ->setProductUrl("https://next.router.vuejs.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Vuex v4.0.0"))
                ->setCopyright("Copyright 2021, Evan You")
                ->setProductUrl("https://next.vuex.vuejs.org/")
                ->setLicence(static::LICENCE_MIT),

			(new static("Jssor Slider"))
                ->setCopyright("Jssor Slider 2009-2020")
                ->setProductUrl("https://www.jssor.com")
                ->setLicence(static::LICENCE_MIT),

			(new static("jQuery Nivo Slider v3.2"))
                ->setCopyright("Copyright 2012, Dev7studios")
                ->setProductUrl("http://nivo.dev7studios.com")
                ->setLicence(static::LICENCE_MIT),

			(new static("Video.js v5.16.0"))
                ->setCopyright("Copyright Brightcove, Inc. <https://www.brightcove.com/>")
                ->setProductUrl("http://videojs.com/")
                ->setLicence(static::LICENCE_APACHE2),

			(new static("videojs-contrib-hls v5.2.1"))
                ->setCopyright("Copyright 2017 Brightcove, Inc. <https://www.brightcove.com/>")
                ->setLicence(static::LICENCE_APACHE2),

			(new static("videojs-playlist-thumbs v0.1.5"))
                ->setCopyright("Copyright 2016 Emmanuel Alves <manel.pb@gmail.com>")
                ->setLicence(static::LICENCE_MIT),

			(new static("videojs-vimeo v2.0.2"))
                ->setCopyright("Copyright Benoit Tremblay <trembl.ben@gmail.com>")
                ->setLicence(static::LICENCE_MIT),

			(new static("videojs-youtube v2.0.2"))
                ->setCopyright("Copyright Benoit Tremblay <trembl.ben@gmail.com>")
                ->setLicence(static::LICENCE_MIT),

			(new static("Petrovich v1.0.0"))
                ->setProductUrl('https://github.com/MikeBazhenov/petrovich')
                ->setLicence(static::LICENCE_MIT),

			(new static("Canvas confetti"))
                ->setCopyright("Copyright (c) 2020, Kiril Vatev")
                ->setProductUrl('https://github.com/catdad/canvas-confetti')
                ->setLicence(static::LICENCE_MIT),

			(new static("Leaflet"))
                ->setCopyright("Copyright (c) 2010-2019, Vladimir Agafonkin. Copyright (c) 2010-2011, CloudMade")
                ->setProductUrl('https://github.com/Leaflet/Leaflet')
                ->setLicence(static::LICENCE_BSD2),

			(new static("Lodash"))
                ->setCopyright("Copyright OpenJS Foundation and other contributors <https://openjsf.org/>")
                ->setProductUrl('https://lodash.com/')
                ->setLicence(static::LICENCE_MIT),
		];
	}
}

/** @example
 * Main\EventManager::getInstance()->addEventHandler("main", "onGetThirdPartySoftware", function()
 * {
 * return new Main\EventResult(Main\EventResult::SUCCESS, [
 * (new Copyright("jQuery JavaScript Library v1.7"))
 * ->setProductUrl("http://jquery.com/")
 * ->setCopyright("Copyright 2011, John Resig")
 * ->setLicence(Copyright::LICENCE_MIT),
 * ->setLicenceUrl("http://jquery.org/license"),
 * ]);
 * });
 */
