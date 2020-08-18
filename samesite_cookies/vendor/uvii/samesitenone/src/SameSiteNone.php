<?php
declare(strict_types = 1);

namespace Uvii;

class SameSiteNone
{
    public $uaStr = '';

    public function __construct(String $uaStr = '') {
        $this->uaStr = $uaStr;
    }

    public static function isSafe(String $useragent): bool
    {
        return ((new self($useragent))->shouldSendSameSiteNone());
    }

    function shouldSendSameSiteNone(): bool
    {
		return !$this->isSameSiteNoneIncompatible();
	}

    function isSameSiteNoneIncompatible(): bool
    {
		return $this->hasWebKitSameSiteBug() ||
			$this->dropsUnrecognizedSameSiteCookies();
	}

    function hasWebKitSameSiteBug(): bool
    {
		return $this->isIosVersion(12) ||
			($this->isMacosxVersion(10,14) && 
			  ($this->isSafari() || 
               $this->isMacEmbeddedBrowser()
              )
            );
	}

    function dropsUnrecognizedSameSiteCookies(): bool
    {
		if ($this->isUcBrowser()) {
			return !$this->isUcBrowserVersionAtLeast(12,13,2);
		}
        return $this->isChromiumBased() &&
            $this->isChromiumVersionAtLeast(51) &&
            !$this->isChromiumVersionAtLeast(67);
	}

    public function isIosVersion(int $major): bool
    {
           
        $regex = "/\(iP.+; CPU .*OS (\d+)[_\d]*.*\) AppleWebKit\//";
        $ver = 0;
        if (preg_match($regex, $this->uaStr, $matches)) {
            $ver = intval($matches[1]);
        }
        return $ver === $major;
    }

    public function isMacosxVersion(int $major, int $minor): bool
    {
           
        $regex = "/\(Macintosh;.*Mac OS X (\d+)_(\d+)[_\d]*.*\) AppleWebKit\//";
        $major_version = 0;
        $minor_version = 0;
        if (preg_match($regex, $this->uaStr, $matches)) {
            $major_version = intval($matches[1]);
            $minor_version = intval($matches[2]);
        }
        return  ($major_version === $major) &&
                ($minor_version === $minor);
    }

    public function isSafari(): bool
    {
        $regex = "/Version\/.* Safari\//";
        return (1 === preg_match($regex, $this->uaStr)) &&
            !$this->isChromiumBased();
    }

    public function isMacEmbeddedBrowser(): bool
    {
        $regex = "/^Mozilla\/[\.\d]+ \(Macintosh;.*Mac OS X [_\d]+\) AppleWebKit\/[\.\d]+ \(KHTML, like Gecko\)$/";
        return (1 === preg_match($regex, $this->uaStr));
    }

    public function isChromiumBased(): bool
    {
        $regex = "/Chrom(e|ium)/";
        return (1 === preg_match($regex, $this->uaStr));
    }

    public function isChromiumVersionAtLeast(int $major): bool
    {
        $regex = "/Chrom[^ \/]+\/(\d+)[\.\d]*/";
        $ver = 0;
        if (preg_match($regex, $this->uaStr, $matches)) {
            $ver = intval($matches[1]);
        }
        return $ver >= $major;
    }

    public function isUcBrowser(): bool
    {
        $regex = "/UCBrowser/";
        return (1 === preg_match($regex, $this->uaStr)) ? true: false;
    }

    public function isUcBrowserVersionAtLeast(int $major, int $minor, int $build): bool
    {
        $regex = "/UCBrowser\/(\d+)\.(\d+)\.(\d+)[\.\d]* /";
        $major_version = 0;
        $minor_version = 0;
        $build_version = 0;
        if (preg_match($regex, $this->uaStr, $matches)) {
            $major_version = intval($matches[1]);
            $minor_version = intval($matches[2]);
            $build_version = intval($matches[3]);
        }
        if ($major_version != $major) {
            return $major_version > $major;
        }
        if ($minor_version != $minor) {
            return $minor_version > $minor;
        }
        return $build_version >= $build;
    }
}


