<?php
/**
 * @title            Browser Class
 * @desc             Useful Browser methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Navigation
 * @version          1.1
 */

namespace PH7\Framework\Navigation;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str, PH7\Framework\Mvc\Model\DbConfig, PH7\Framework\Server\Server;

/**
 * @internal In this class, there're some yoda conditions.
 */
class Browser
{

    /**
     * Detect the user's preferred language.
     *
     * @return string First two letters of the languages ​​of the client browser.
     */
    public function getLanguage()
    {
        $oStr = new Str;
        $sLang = explode(',', Server::getVar(Server::HTTP_ACCEPT_LANGUAGE))[0];
        // The rtrim function is slightly faster than chop function
        return $oStr->escape($oStr->lower(substr(rtrim($sLang), 0, 2)));
        unset($oStr);
    }

    /**
     * Active browser cache.
     * @return object this
     */
    public function cache()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24 * 30) . ' GMT');
        header('Pragma: public');
        //header ('Not Modified', true, 304);

        return $this;
    }

    /**
     * Prevent caching in the browser.
     *
     * @return object this
     */
    public function noCache()
    {
        $sNow = gmdate('D, d M Y H:i:s') . ' GMT';
        header('Expires: ' . $sNow);
        header('Last-Modified: ' . $sNow);
        unset($sNow);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        return $this;
    }

    /**
     * Are we capable to receive gzipped data?
     *
     * @return mixed (string | boolean) Returns the encoding if it is accepted, false otherwise. Maybe additional check for Mac OS...
     */
    public function encoding()
    {
        if (headers_sent() || connection_aborted())
            return false;

        $sEncoding = Server::getVar(Server::HTTP_ACCEPT_ENCODING);
        if (false !== strpos($sEncoding, 'gzip'))
            return 'gzip';

        if (false !== strpos($sEncoding, 'x-gzip'))
            return 'x-gzip';

        return false;
    }

    /**
     * @return boolean
     */
    public function isMobile()
    {
        if (null !== Server::getVar(Server::HTTP_X_WAP_PROFILE) || null !== Server::getVar(Server::HTTP_PROFILE))
            return true;

        $sHttpAccept = Server::getVar(Server::HTTP_ACCEPT);
        if (null !== $sHttpAccept)
        {
            $sHttpAccept = strtolower($sHttpAccept);

            if (false !== strpos($sHttpAccept, 'wap'))
                return true;
        }

        $sUserAgent = self::getUserAgent();
        if (null !== $sUserAgent)
        {
            if (false !== strpos($sUserAgent, 'Mobile'))
                return true;

            if (false !== strpos($sUserAgent, 'Opera Mini'))
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isFullAjaxSite()
    {
        return DbConfig::getSetting('fullAjaxSite') ? true : false;
    }

    /**
     * @return mixed (string | null) The HTTP User Agent is it exists, otherwise the NULL value.
     */
    public function getUserAgent()
    {
        return Server::getVar(Server::HTTP_USER_AGENT);
    }

    /**
     * @return mixed (string or null)  The HTTP Referer is it exists, otherwise the NULL value.
     */
    public function getHttpReferer()
    {
        return Server::getVar(Server::HTTP_REFERER);
    }

    /**
     * @return boolean
     */
    public function isAjaxRequest()
    {
        return array_key_exists(Server::HTTP_X_REQUESTED_WITH, Server::getVar());
    }

    /**
     * Get favicon from a URL.
     *
     * @static
     * @param string $sUrl
     * @return string The favicon image.
     */
    public static function favicon($sUrl)
    {
        $sApiUrl = 'http://www.google.com/s2/favicons?domain=';
        $sDomainName = \PH7\Framework\Http\Http::getHostName($sUrl);

        return $sApiUrl . $sDomainName;
    }

}
