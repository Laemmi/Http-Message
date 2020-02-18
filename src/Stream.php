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
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * Stream constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $this->filterResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isSeekable()) {
            $this->rewind();
        }
        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        fclose($this->resource);

        $this->detach();
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        $stats = fstat($this->resource);

        if ($stats && isset($stats['size'])) {
            return $stats['size'];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        $position = ftell($this->resource);

        if (false !== $position) {
            return $position;
        }

        throw new RuntimeException('Could not get the position of the pointer in stream.');
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        return (bool) $this->getMetadata('seekable');
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->isSeekable() || -1 === fseek($this->resource, $offset, $whence)) {
            throw new RuntimeException('Could not seek in stream.');
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        if (!$this->isSeekable() || false === rewind($this->resource)) {
            throw new RuntimeException('Could not rewind stream.');
        }
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');

        if (false !== strstr($mode, 'w') || false !== strstr($mode, '+')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function write($string): int
    {
        $written = false;

        if ($this->isWritable()) {
            $written = fwrite($this->resource, $string);
        }

        if (false !== $written) {
            return $written;
        }

        throw new RuntimeException('Could not write to stream.');
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');

        if (false !== strstr($mode, 'r') || false !== strstr($mode, '+')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function read($length): string
    {
        $data = false;

        if ($this->isReadable()) {
            $data = fread($this->resource, $length);
        }

        if (is_string($data)) {
            return $data;
        }

        throw new RuntimeException('Could not read from stream.');
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        $contents = stream_get_contents($this->resource);

        if (is_string($contents)) {
            return $contents;
        }

        throw new RuntimeException('Could not get contents of stream.');
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        $data = stream_get_meta_data($this->resource);

        if (!$key) {
            return $data;
        }

        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * @param $value
     * @return resource
     */
    protected function filterResource($value)
    {
        if (is_resource($value)) {
            return $value;
        }

        throw new InvalidArgumentException('Argument must be a valid PHP resource');
    }
}
