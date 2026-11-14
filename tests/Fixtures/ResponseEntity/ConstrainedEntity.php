<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use Symfony\Component\Validator\Constraints as Assert;

class ConstrainedEntity
{

	#[Assert\NotBlank]
	#[Assert\Length(max: 255)]
	public string $name;

	#[Assert\Length(min: 8, max: 64)]
	public string $password;

	#[Assert\Regex(pattern: '/^[A-Za-z0-9_-]+$/')]
	public string $token;

	#[Assert\Email]
	public ?string $email;

	#[Assert\Url]
	public string $url;

	#[Assert\Uuid]
	public string $uuid;

	#[Assert\Hostname]
	public string $hostname;

	#[Assert\Ip(version: Assert\Ip::V6)]
	public string $ipv6;

	#[Assert\Ip(version: Assert\Ip::ALL)]
	public string $ip;

	#[Assert\Range(min: 1, max: 65535)]
	public int $port;

	#[Assert\GreaterThanOrEqual(0)]
	#[Assert\LessThanOrEqual(100)]
	public float $percent;

	#[Assert\Choice(choices: ['ssl', 'tls'])]
	public string $secure;

	/** @var string[] */
	#[Assert\Count(min: 1, max: 10)]
	#[Assert\Unique]
	#[Assert\All([
		new Assert\Length(max: 64),
		new Assert\Hostname(),
	])]
	public array $names;

}
