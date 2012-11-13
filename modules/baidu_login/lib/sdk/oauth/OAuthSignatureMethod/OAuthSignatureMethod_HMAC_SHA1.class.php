<?php
/***************************************************************************
 *
 * Copyright (c)2009 Baidu.com, Inc. All Rights Reserved
 *
 **************************************************************************/
/**
 * OAuth signature implementation using HMAC-SHA1
 *
 * @author Marc Worrell <marcw@pobox.com>
 * @date  Sep 8, 2008 12:21:19 PM
 *
 * The MIT License
 *
 * Copyright (c) 2007-2008 Mediamatic Lab
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once(dirname(__FILE__).'/OAuthSignatureMethod.class.php');

class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod
{
	public function name()
	{
		return 'HMAC-SHA1';
	}

	/**
	 * Calculate the signature using HMAC-SHA1
	 * This function is copyright Andy Smith, 2007.
	 *
	 * @param string base_string
	 * @param string consumer_secret
	 * @param string token_secret
	 * @return string
	 */
	public function signature($base_string, $consumer_secret, $token_secret)
	{
		$key = $this->urlencode($consumer_secret) . '&' . $this->urlencode($token_secret);
		if (function_exists('hash_hmac')) {
			return base64_encode(hash_hmac('sha1', $base_string, $key, true));
		} else {
			$blocksize = 64;
			$hashfunc = 'sha1';
			if (strlen($key) > $blocksize) {
				$key = pack('H*', $hashfunc($key));
			}
		    $key	= str_pad($key,$blocksize,chr(0x00));
		    $ipad	= str_repeat(chr(0x36),$blocksize);
		    $opad	= str_repeat(chr(0x5c),$blocksize);
		    $hmac 	= pack(
		                'H*',$hashfunc(
		                    ($key^$opad).pack(
		                        'H*',$hashfunc(
		                            ($key^$ipad).$base_string
		                        )
		                    )
		                )
		            );
			return base64_encode($hmac);
		}
	}
}


/* vi:set ts=4 sts=4 sw=4 binary noeol: */