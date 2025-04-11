<?php

namespace HeimrichHannot\UtilsBundle\StaticUtil;

class StaticUrlUtil extends AbstractStaticUtil
{
    /**
     * This method is the reverse of {@see \parse_url `parse_url(...)`} and is used to build a URL from its components.
     *
     * @see https://www.php.net/manual/en/function.parse-url.php
     *
     * @param array{
     *     scheme?: string,
     *     host?: string,
     *     port?: int,
     *     user?: string,
     *     pass?: string,
     *     path?: string,
     *     query?: string,
     *     fragment?: string,
     * }                $parsedUrl          The parsed URL components.
     * @param bool|null $emptySchemeSuffix  Whether to add `//` before the host if no scheme is provided. Defaults to true.
     * @param bool|null $queryPrefix        Whether to add `?` before the query string if the URL is otherwise empty. Defaults to true.
     * @param bool|null $fragmentPrefix     Whether to add `#` before the fragment if the URL is otherwise empty. Defaults to true.
     * @return string
     */
    public static function unparseUrl(
        array $parsedUrl,
        ?bool $emptySchemeSuffix = null,
        ?bool $queryPrefix = null,
        ?bool $fragmentPrefix = null,
    ): string {
        $emptySchemeSuffix ??= true;
        $queryPrefix ??= true;
        $fragmentPrefix ??= true;

        if (empty($parsedUrl))
        {
            return '';
        }

        $scheme = $parsedUrl['scheme'] ?? null;
        $host = $parsedUrl['host'] ?? null;
        $port = $parsedUrl['port'] ?? null;
        $user = $parsedUrl['user'] ?? null;
        $pass = $parsedUrl['pass'] ?? null;
        $path = $parsedUrl['path'] ?? null;
        $query = $parsedUrl['query'] ?? null;
        $fragment = $parsedUrl['fragment'] ?? null;

        $url = '';

        if ($host)
        {
            if (isset($scheme)) {
                $url .= $scheme . '://';
            } elseif ($emptySchemeSuffix) {
                $url .= '//';
            }

            $url .= isset($user) ? $user . (isset($pass) ? ':' . $pass : '') . '@' : '';
            $url .= $host;
            $url .= isset($port) ? ':' . $port : '';

            $url = isset($path) ? rtrim($url, '/') . '/' : $url;
        }

        $url .= isset($path) ? ltrim($path, '/') : '';
        $url .= isset($query) ? ($url || $queryPrefix ? '?' : '') . $query : '';
        $url .= isset($fragment) ? ($url || $fragmentPrefix ? '#' : '') . $fragment : '';

        return $url;
    }
}