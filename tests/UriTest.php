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

namespace Laemmi\Http\Message\Test;

use Laemmi\Http\Message\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriTest extends TestCase
{
    public function uriFactory(): UriInterface
    {
        $scheme   = 'https';
        $host     = 'spacerabbit.de';
        $port     = 443;
        $path     = '/module/controller/action';
        $query    = 'key=value123&key2=value456';
        $fragment = 'anchorA';
        $user     = 'laemmi';
        $password = 'fcknzs';

        return new Uri($scheme, $host, $port, $path, $query, $fragment, $user, $password);
    }

    public function testGetScheme()
    {
        $this->assertEquals('https', $this->uriFactory()->getScheme());
    }

    public function testGetAuthority()
    {
        $this->assertEquals('laemmi:fcknzs@spacerabbit.de', $this->uriFactory()->getAuthority());
    }

    public function testGetUserInfo()
    {
        $this->assertEquals('laemmi:fcknzs', $this->uriFactory()->getUserInfo());
    }

    public function testGetHost()
    {
        $this->assertEquals('spacerabbit.de', $this->uriFactory()->getHost());
    }

    public function testGetPort()
    {
        $this->assertNull($this->uriFactory()->getPort());
    }

    public function testGetPath()
    {
        $this->assertEquals('/module/controller/action', $this->uriFactory()->getPath());
    }

    public function testGetQuery()
    {
        $this->assertEquals('key=value123&key2=value456', $this->uriFactory()->getQuery());
    }

    public function testGetFragment()
    {
        $this->assertEquals('anchorA', $this->uriFactory()->getFragment());
    }

    public function testWithPort()
    {
        $uri = $this->uriFactory()->withPort(8080);

        $this->assertEquals(8080, $uri->getPort());
    }

    public function testWithPath()
    {
        $uri = $this->uriFactory()->withPath('/path');

        $this->assertEquals('/path', $uri->getPath());
    }

    public function testWithQuery()
    {
        $uri = $this->uriFactory()->withQuery('biff=baff&kling=klang');

        $this->assertEquals('biff=baff&kling=klang', $uri->getQuery());
    }

    public function testWithFragment()
    {
        $uri = $this->uriFactory()->withFragment('anchorB');

        $this->assertEquals('anchorB', $uri->getFragment());
    }
}
