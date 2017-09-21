<?php

namespace Apitte\Core\Http;

use Apitte\Negotiation\Http\ArrayStream;
use Contributte\Psr7\ResponseWrapper;

/**
 * Tiny wrapper for PSR-7 ResponseInterface
 */
class ApiResponse extends ResponseWrapper
{

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param array $data
	 * @return static
	 */
	public function withData(array $data)
	{
		$new = clone $this;
		$new = $new->withBody(ArrayStream::from($this)->with($data));

		return $new;
	}

}
