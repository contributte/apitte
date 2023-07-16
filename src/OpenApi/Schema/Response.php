<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Response
{

	private string $description;

	/** @var Header[]|Reference[] */
	private array $headers = [];

	/** @var MediaType[] */
	private array $content = [];

	/** @var Link[]|Reference[] */
	private array $links = [];

	public function __construct(string $description)
	{
		$this->description = $description;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Response
	{
		$response = new Response($data['description']);
		foreach ($data['headers'] ?? [] as $key => $headerData) {
			if (isset($headerData['$ref'])) {
				$response->setHeader($key, Reference::fromArray($headerData));
			} else {
				$response->setHeader($key, Header::fromArray($headerData));
			}
		}

		foreach ($data['content'] ?? [] as $key => $contentData) {
			$response->setContent($key, MediaType::fromArray($contentData));
		}

		foreach ($data['links'] ?? [] as $key => $linkData) {
			if (isset($linkData['$ref'])) {
				$response->setLink($key, Reference::fromArray($linkData));
			} else {
				$response->setLink($key, Link::fromArray($linkData));
			}
		}

		return $response;
	}

	public function setContent(string $type, MediaType $mediaType): void
	{
		$this->content[$type] = $mediaType;
	}

	public function setHeader(string $key, Header|Reference $header): void
	{
		$this->headers[$key] = $header;
	}

	public function setLink(string $key, Link|Reference $link): void
	{
		$this->links[$key] = $link;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['description'] = $this->description;
		foreach ($this->headers as $key => $header) {
			$data['headers'][$key] = $header->toArray();
		}

		foreach ($this->content as $key => $mediaType) {
			$data['content'][$key] = $mediaType->toArray();
		}

		foreach ($this->links as $key => $link) {
			$data['links'][$key] = $link->toArray();
		}

		return $data;
	}

}
