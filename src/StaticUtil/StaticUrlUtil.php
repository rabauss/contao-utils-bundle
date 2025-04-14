<?php

namespace HeimrichHannot\UtilsBundle\StaticUtil;

class StaticUrlUtil extends AbstractStaticUtil
{
    /**
     * This method is the reverse of {@see \parse_url `parse_url(...)`} and is used to build a URL from its components.
     *
     * @see https://www.php.net/manual/en/function.parse-url.php
     *
     * Options:
     * - suffixEmptyScheme (bool): Whether to add `//` before the host if no scheme is provided. (default: true)
     * - prefixPath (bool):        Whether to add `/` before the path if the URL is otherwise empty. (default: true)
     * - prefixQuery (bool):       Whether to add `?` before the query string if the URL is otherwise empty. (default: true)
     * - prefixFragment (bool):    Whether to add `#` before the fragment if the URL is otherwise empty. (default: true)
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
     * } $parsedUrl The parsed URL components.
     * @param array{
     *     suffixEmptyScheme?: bool,
     *     prefixPath?: bool,
     *     prefixQuery?: bool,
     *     prefixFragment?: bool,
     * } $options   Options for URL construction.
     * @return string The reconstructed URL.
     */
    public static function unparseUrl(array $parsedUrl, array $options = []): string
    {
        $suffixEmptyScheme = (bool) ($options['suffixEmptyScheme'] ?? true);
        $prefixPath        = (bool) ($options['prefixPath'] ?? true);
        $prefixQuery       = (bool) ($options['prefixQuery'] ?? true);
        $prefixFragment    = (bool) ($options['prefixFragment'] ?? true);

        if (empty($parsedUrl))
        {
            return '';
        }

        $scheme   = $parsedUrl['scheme'] ?? null;
        $host     = $parsedUrl['host'] ?? null;
        $port     = $parsedUrl['port'] ?? null;
        $user     = $parsedUrl['user'] ?? null;
        $pass     = $parsedUrl['pass'] ?? null;
        $path     = $parsedUrl['path'] ?? null;
        $query    = $parsedUrl['query'] ?? null;
        $fragment = $parsedUrl['fragment'] ?? null;

        $url = '';

        if ($host)
        {
            if (isset($scheme)) {
                $url .= $scheme . '://';
            } elseif ($suffixEmptyScheme) {
                $url .= '//';
            }

            $url .= isset($user) ? $user . (isset($pass) ? ':' . $pass : '') . '@' : '';
            $url .= $host;
            $url .= isset($port) ? ':' . $port : '';

            $url = rtrim($url, '/');
        }

        $url .= empty($path) ? '' : ($url || $prefixPath ? '/' : '') . ltrim($path, '/');
        $url .= empty($query) ? '' : ($url || $prefixQuery ? '?' : '') . $query;
        $url .= empty($fragment) ? '' : ($url || $prefixFragment ? '#' : '') . $fragment;

        return $url;
    }
}