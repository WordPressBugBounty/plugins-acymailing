<?php

namespace AcyMailing\Libraries\Pear;

acym_cmsLoaded();

/**
 * Net_Socket
 *
 * PHP Version 5
 *
 * LICENSE:
 *
 * Copyright (c) 1997-2017 The PHP Group
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * o Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Net
 * @package   Net_Socket
 * @author    Stig Bakken <ssb@php.net>
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @copyright 1997-2017 The PHP Group
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 * @link      http://pear.php.net/packages/Net_Socket
 */

define('NET_SOCKET_READ', 1);
define('NET_SOCKET_WRITE', 2);
define('NET_SOCKET_ERROR', 4);

/**
 * Generalized Socket class.
 *
 * @category  Net
 * @package   Net_Socket
 * @author    Stig Bakken <ssb@php.net>
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @copyright 1997-2017 The PHP Group
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 * @link      http://pear.php.net/packages/Net_Socket
 */
class Socket extends Pear
{
    /**
     * Socket file pointer.
     * @var resource $fp
     */
    public $fp = null;

    /**
     * Whether the socket is blocking. Defaults to true.
     * @var boolean $blocking
     */
    public $blocking = true;

    /**
     * Whether the socket is persistent. Defaults to false.
     * @var boolean $persistent
     */
    public $persistent = false;

    /**
     * The IP address to connect to.
     * @var string $addr
     */
    public $addr = '';

    /**
     * The port number to connect to.
     * @var integer $port
     */
    public $port = 0;

    /**
     * Number of seconds to wait on socket operations before assuming
     * there's no more data. Defaults to no timeout.
     * @var integer|float $timeout
     */
    public $timeout = null;

    /**
     * Number of bytes to read at a time in readLine() and
     * readAll(). Defaults to 2048.
     * @var integer $lineLength
     */
    public $lineLength = 2048;

    /**
     * The string to use as a newline terminator. Usually "\r\n" or "\n".
     * @var string $newline
     */
    public $newline = "\r\n";

    /**
     * Connect to the specified port. If called when the socket is
     * already connected, it disconnects and connects again.
     *
     * @param string  $addr       IP address or host name (may be with protocol prefix).
     * @param integer $port       TCP port number.
     * @param boolean $persistent (optional) Whether the connection is
     *                            persistent (kept open between requests
     *                            by the web server).
     * @param integer $timeout    (optional) Connection socket timeout.
     * @param array   $options    See options for stream_context_create.
     *
     * @access public
     *
     * @return boolean|Error  True on success or a Error on failure.
     */
    public function connect(
        $addr,
        $port = 0,
        $persistent = null,
        $timeout = null,
        $options = null
    ) {
        if (is_resource($this->fp)) {
            @fclose($this->fp);
            $this->fp = null;
        }

        if (!$addr) {
            return $this->raiseError('$addr cannot be empty');
        } else {
            if (strspn($addr, ':.0123456789') === strlen($addr)) {
                $this->addr = strpos($addr, ':') !== false ? '['.$addr.']' : $addr;
            } else {
                $this->addr = $addr;
            }
        }

        $this->port = $port % 65536;

        if ($persistent !== null) {
            $this->persistent = $persistent;
        }

        $openfunc = $this->persistent ? 'pfsockopen' : 'fsockopen';
        $errno = 0;
        $errstr = '';

        if (function_exists('error_clear_last')) {
            error_clear_last();
        } else {
            $old_track_errors = @ini_set('track_errors', 1);
        }

        if ($timeout <= 0) {
            $timeout = @ini_get('default_socket_timeout');
        }

        if ($options && function_exists('stream_context_create')) {
            $context = stream_context_create($options);

            // Since PHP 5 fsockopen doesn't allow context specification
            if (function_exists('stream_socket_client')) {
                $flags = STREAM_CLIENT_CONNECT;

                if ($this->persistent) {
                    $flags = STREAM_CLIENT_PERSISTENT;
                }

                $addr = $this->addr.':'.$this->port;
                $fp = @stream_socket_client(
                    $addr,
                    $errno,
                    $errstr,
                    $timeout,
                    $flags,
                    $context
                );
            } else {
                $fp = @$openfunc(
                    $this->addr,
                    $this->port,
                    $errno,
                    $errstr,
                    $timeout,
                    $context
                );
            }
        } else {
            $fp = @$openfunc($this->addr, $this->port, $errno, $errstr, $timeout);
        }

        if (!$fp) {
            if ($errno === 0 && !strlen($errstr)) {
                $errstr = '';
                if (isset($old_track_errors)) {
                    $errstr = $php_errormsg ? : '';
                    @ini_set('track_errors', $old_track_errors);
                } else {
                    $lastError = error_get_last();
                    if (isset($lastError['message'])) {
                        $errstr = $lastError['message'];
                    }
                }
            }

            return $this->raiseError($errstr, $errno);
        }

        if (isset($old_track_errors)) {
            @ini_set('track_errors', $old_track_errors);
        }

        $this->fp = $fp;
        $this->setTimeout();

        return $this->setBlocking($this->blocking);
    }

    /**
     * Disconnects from the peer, closes the socket.
     *
     * @access public
     * @return mixed true on success or a Error instance otherwise
     */
    public function disconnect()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        @fclose($this->fp);
        $this->fp = null;

        return true;
    }

    /**
     * Set the newline character/sequence to use.
     *
     * @param string $newline Newline character(s)
     *
     * @return boolean True
     */
    public function setNewline($newline)
    {
        $this->newline = $newline;

        return true;
    }

    /**
     * Find out if the socket is in blocking mode.
     *
     * @access public
     * @return boolean  The current blocking mode.
     */
    public function isBlocking()
    {
        return $this->blocking;
    }

    /**
     * Sets whether the socket connection should be blocking or
     * not. A read call to a non-blocking socket will return immediately
     * if there is no data available, whereas it will block until there
     * is data for blocking sockets.
     *
     * @param boolean $mode True for blocking sockets, false for nonblocking.
     *
     * @access public
     * @return mixed true on success or a Error instance otherwise
     */
    public function setBlocking($mode)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $this->blocking = $mode;
        stream_set_blocking($this->fp, (int)$this->blocking);

        return true;
    }

    /**
     * Sets the timeout value on socket descriptor,
     * expressed in the sum of seconds and microseconds
     *
     * @param integer $seconds      Seconds.
     * @param integer $microseconds Microseconds, optional.
     *
     * @access public
     * @return mixed True on success or false on failure or
     *               a Error instance when not connected
     */
    public function setTimeout($seconds = null, $microseconds = null)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        if ($seconds === null && $microseconds === null) {
            $seconds = (int)$this->timeout;
            $microseconds = (int)(($this->timeout - $seconds) * 1000000);
        } else {
            $this->timeout = $seconds + $microseconds / 1000000;
        }

        if ($this->timeout > 0) {
            return stream_set_timeout($this->fp, (int)$seconds, (int)$microseconds);
        } else {
            return false;
        }
    }

    /**
     * Sets the file buffering size on the stream.
     * See php's stream_set_write_buffer for more information.
     *
     * @param integer $size Write buffer size.
     *
     * @access public
     * @return mixed on success or an Error object otherwise
     */
    public function setWriteBuffer($size)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $returned = stream_set_write_buffer($this->fp, $size);
        if ($returned === 0) {
            return true;
        }

        return $this->raiseError('Cannot set write buffer.');
    }

    /**
     * Returns information about an existing socket resource.
     * Currently returns four entries in the result array:
     *
     * <p>
     * timed_out (bool) - The socket timed out waiting for data<br>
     * blocked (bool) - The socket was blocked<br>
     * eof (bool) - Indicates EOF event<br>
     * unread_bytes (int) - Number of bytes left in the socket buffer<br>
     * </p>
     *
     * @access public
     * @return mixed Array containing information about existing socket
     *               resource or a Error instance otherwise
     */
    public function getStatus()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        return stream_get_meta_data($this->fp);
    }

    /**
     * Get a specified line of data
     *
     * @param int $size Reading ends when size - 1 bytes have been read,
     *                  or a newline or an EOF (whichever comes first).
     *                  If no size is specified, it will keep reading from
     *                  the stream until it reaches the end of the line.
     *
     * @access public
     * @return mixed $size bytes of data from the socket, or a Error if
     *         not connected. If an error occurs, FALSE is returned.
     */
    public function gets($size = null)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        if (null === $size) {
            return @fgets($this->fp);
        } else {
            return @fgets($this->fp, $size);
        }
    }

    /**
     * Read a specified amount of data. This is guaranteed to return,
     * and has the added benefit of getting everything in one fread()
     * chunk; if you know the size of the data you're getting
     * beforehand, this is definitely the way to go.
     *
     * @param integer $size The number of bytes to read from the socket.
     *
     * @access public
     * @return string $size bytes of data from the socket, or a Error if
     *         not connected.
     */
    public function read($size)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        return @fread($this->fp, $size);
    }

    /**
     * Write a specified amount of data.
     *
     * @param string  $data      Data to write.
     * @param integer $blocksize Amount of data to write at once.
     *                           NULL means all at once.
     *
     * @access public
     * @return mixed If the socket is not connected, returns an instance of
     *               Error.
     *               If the write succeeds, returns the number of bytes written.
     *               If the write fails, returns false.
     *               If the socket times out, returns an instance of Error.
     */
    public function write($data, $blocksize = null)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        if (null === $blocksize && !OS_WINDOWS) {
            $written = @fwrite($this->fp, $data);

            // Check for timeout or lost connection
            if ($written === false) {
                $meta_data = $this->getStatus();

                if (!is_array($meta_data)) {
                    return $meta_data; // Error
                }

                if (!empty($meta_data['timed_out'])) {
                    return $this->raiseError('timed out');
                }
            }

            return $written;
        } else {
            if (null === $blocksize) {
                $blocksize = 1024;
            }

            $pos = 0;
            $size = strlen($data);
            while ($pos < $size) {
                $written = @fwrite($this->fp, substr($data, $pos, $blocksize));

                // Check for timeout or lost connection
                if ($written === false) {
                    $meta_data = $this->getStatus();

                    if (!is_array($meta_data)) {
                        return $meta_data; // Error
                    }

                    if (!empty($meta_data['timed_out'])) {
                        return $this->raiseError('timed out');
                    }

                    return $written;
                }

                $pos += $written;
            }

            return $pos;
        }
    }

    /**
     * Write a line of data to the socket, followed by a trailing newline.
     *
     * @param string $data Data to write
     *
     * @access public
     * @return mixed fwrite() result, or Error when not connected
     */
    public function writeLine($data)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        return fwrite($this->fp, $data.$this->newline);
    }

    /**
     * Tests for end-of-file on a socket descriptor.
     *
     * Also returns true if the socket is disconnected.
     *
     * @access public
     * @return bool
     */
    public function eof()
    {
        return (!is_resource($this->fp) || feof($this->fp));
    }

    /**
     * Reads a byte of data
     *
     * @access public
     * @return integer 1 byte of data from the socket, or a Error if
     *         not connected.
     */
    public function readByte()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        return ord(@fread($this->fp, 1));
    }

    /**
     * Reads a word of data
     *
     * @access public
     * @return integer 1 word of data from the socket, or a Error if
     *         not connected.
     */
    public function readWord()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $buf = @fread($this->fp, 2);

        return (ord($buf[0]) + (ord($buf[1]) << 8));
    }

    /**
     * Reads an int of data
     *
     * @access public
     * @return integer  1 int of data from the socket, or a Error if
     *                  not connected.
     */
    public function readInt()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $buf = @fread($this->fp, 4);

        return (ord($buf[0]) + (ord($buf[1]) << 8) +
            (ord($buf[2]) << 16) + (ord($buf[3]) << 24));
    }

    /**
     * Reads a zero-terminated string of data
     *
     * @access public
     * @return string, or a Error if
     *         not connected.
     */
    public function readString()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $string = '';
        while (($char = @fread($this->fp, 1)) !== "\x00") {
            $string .= $char;
        }

        return $string;
    }

    /**
     * Reads an IP Address and returns it in a dot formatted string
     *
     * @access public
     * @return string Dot formatted string, or a Error if
     *         not connected.
     */
    public function readIPAddress()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $buf = @fread($this->fp, 4);

        return sprintf(
            '%d.%d.%d.%d',
            ord($buf[0]),
            ord($buf[1]),
            ord($buf[2]),
            ord($buf[3])
        );
    }

    /**
     * Read until either the end of the socket or a newline, whichever
     * comes first. Strips the trailing newline from the returned data.
     *
     * @access public
     * @return string All available data up to a newline, without that
     *         newline, or until the end of the socket, or a Error if
     *         not connected.
     */
    public function readLine()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $line = '';

        $timeout = time() + $this->timeout;

        while (!feof($this->fp) && (!$this->timeout || time() < $timeout)) {
            $line .= @fgets($this->fp, $this->lineLength);
            if (substr($line, -1) == "\n") {
                return rtrim($line, $this->newline);
            }
        }

        return $line;
    }

    /**
     * Read until the socket closes, or until there is no more data in
     * the inner PHP buffer. If the inner buffer is empty, in blocking
     * mode we wait for at least 1 byte of data. Therefore, in
     * blocking mode, if there is no data at all to be read, this
     * function will never exit (unless the socket is closed on the
     * remote end).
     *
     * @access public
     *
     * @return string  All data until the socket closes, or a Error if
     *                 not connected.
     */
    public function readAll()
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $data = '';
        $timeout = time() + $this->timeout;

        while (!feof($this->fp) && (!$this->timeout || time() < $timeout)) {
            $data .= @fread($this->fp, $this->lineLength);
        }

        return $data;
    }

    /**
     * Runs the equivalent of the select() system call on the socket
     * with a timeout specified by tv_sec and tv_usec.
     *
     * @param integer $state   Which of read/write/error to check for.
     * @param integer $tv_sec  Number of seconds for timeout.
     * @param integer $tv_usec Number of microseconds for timeout.
     *
     * @access public
     * @return False if select fails, integer describing which of read/write/error
     *         are ready, or Error if not connected.
     */
    public function select($state, $tv_sec, $tv_usec = 0)
    {
        if (!is_resource($this->fp)) {
            return $this->raiseError('not connected');
        }

        $read = null;
        $write = null;
        $except = null;
        if ($state & NET_SOCKET_READ) {
            $read[] = $this->fp;
        }
        if ($state & NET_SOCKET_WRITE) {
            $write[] = $this->fp;
        }
        if ($state & NET_SOCKET_ERROR) {
            $except[] = $this->fp;
        }
        if (false === ($sr = stream_select(
                $read,
                $write,
                $except,
                $tv_sec,
                $tv_usec
            ))
        ) {
            return false;
        }

        $result = 0;
        if (count($read)) {
            $result |= NET_SOCKET_READ;
        }
        if (count($write)) {
            $result |= NET_SOCKET_WRITE;
        }
        if (count($except)) {
            $result |= NET_SOCKET_ERROR;
        }

        return $result;
    }

    /**
     * Turns encryption on/off on a connected socket.
     *
     * @param bool    $enabled Set this parameter to true to enable encryption
     *                         and false to disable encryption.
     * @param integer $type    Type of encryption. See stream_socket_enable_crypto()
     *                         for values.
     *
     * @return false on error, true on success and 0 if there isn't enough data
     *         and the user should try again (non-blocking sockets only).
     *         A Error object is returned if the socket is not
     *         connected
     * @see    http://se.php.net/manual/en/function.stream-socket-enable-crypto.php
     * @access public
     */
    public function enableCrypto($enabled, $type)
    {
        if (version_compare(phpversion(), '5.1.0', '>=')) {
            if (!is_resource($this->fp)) {
                return $this->raiseError('not connected');
            }

            return @stream_socket_enable_crypto($this->fp, $enabled, $type);
        } else {
            $msg = 'Net_Socket::enableCrypto() requires php version >= 5.1.0';

            return $this->raiseError($msg);
        }
    }

}
