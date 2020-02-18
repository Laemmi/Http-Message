<?php

/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category   http-messages
 * @author     Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright  ©2020 laemmi
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      11.02.20
 */

declare(strict_types=1);

namespace Laemmi\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * Uri constructor.
     * @param string $scheme
     * @param string $host
     * @param int|null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string $user
     * @param string $password
     */
    public function __construct(
        string $scheme,
        string $host,
        ?int $port = null,
        string $path = '/',
        string $query = '',
        string $fragment = '',
        string $user = '',
        string $password = ''
    ) {
        $this->scheme   = $this->filterScheme($scheme);
        $this->host     = $this->filterHost($host);
        $this->port     = $this->filterPort($port);
        $this->path     = $this->filterPath($path);
        $this->query    = $this->filterQuery($query);
        $this->fragment = $this->filterFragment($fragment);
        $this->user     = $this->filterUser($user);
        $this->password = $this->filterPassword($password);
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $port     = $this->getPort();

        return ('' !== $userInfo ? $userInfo . '@' : '') . $this->getHost() . (null !== $port ? ':' . $port : '');
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->user . ('' !== $this->password ? ':' . $this->password : '');
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        $default = [
            'http'  => 80,
            'https' => 443
        ];

        if (isset($default[$this->scheme]) && $this->port !== $default[$this->scheme]) {
            return $this->port;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): UriInterface
    {
        $clone = clone $this;
        $clone->scheme = $this->filterScheme($scheme);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = ''): UriInterface
    {
        $clone = clone $this;
        $clone->user     = $this->filterUser($user);
        $clone->password = $this->filterPassword($password);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): UriInterface
    {
        $clone = clone $this;
        $clone->host = $this->filterHost($host);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): UriInterface
    {
        $clone = clone $this;
        $clone->port = $this->filterPort($port);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): UriInterface
    {
        $clone = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): UriInterface
    {
        $clone = clone $this;
        $clone->query = $this->filterQuery($query);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = $this->filterFragment($fragment);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        $scheme    = $this->getScheme();
        $authority = $this->getAuthority();
        $query     = $this->getQuery();
        $fragment  = $this->getFragment();

        return ('' !== $scheme ? $scheme . ':' : '')
            . ('' !== $authority ? '//' . $authority : '')
            . $this->getPath()
            . ('' !== $query ? '?' . $query : '')
            . ('' !== $fragment ? '#' . $fragment : '');
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterScheme($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterHost($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return int|null
     */
    protected function filterPort($value): ?int
    {
        if (is_null($value) || (1 <= $value && 65535 >= $value)) {
            return $value;
        }

        throw new InvalidArgumentException('Uri port must be null or an integer between 1 - 65535');
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterPath($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterQuery($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterFragment($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterUser($value): string
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function filterPassword($value): string
    {
        return $value;
    }
}
