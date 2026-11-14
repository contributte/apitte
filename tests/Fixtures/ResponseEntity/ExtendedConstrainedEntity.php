<?php declare(strict_types = 1);

namespace Tests\Fixtures\ResponseEntity;

use Symfony\Component\Validator\Constraints as Assert;

class ExtendedConstrainedEntity
{

	#[Assert\Positive]
	public int $positive;

	#[Assert\NegativeOrZero]
	public int $negativeOrZero;

	#[Assert\GreaterThan(1.5)]
	#[Assert\LessThan(10)]
	public float $exclusive;

	#[Assert\DivisibleBy(5)]
	public int $divisible;

	#[Assert\GreaterThanOrEqual('today')]
	public string $date;

	#[Assert\EqualTo('v1')]
	public string $version;

	#[Assert\NotIdenticalTo('admin')]
	public string $username;

	#[Assert\IsTrue]
	public bool $agreed;

	#[Assert\Date]
	public string $birthday;

	#[Assert\Url(relativeProtocol: true)]
	public string $relativeUrl;

	#[Assert\Ulid]
	public string $ulid;

	#[Assert\Week]
	public string $week;

	/** @var string[] */
	#[Assert\Choice(choices: ['read', 'write'], multiple: true, min: 1, max: 2)]
	public array $permissions;

	#[Assert\Choice(callback: 'getRoles')]
	public string $role;

	/** @var mixed[] */
	#[Assert\Unique(fields: ['id'])]
	public array $uniqueById;

	#[Assert\NotBlank]
	public array $notEmpty;

	#[Assert\Length(max: 10, countUnit: Assert\Length::COUNT_BYTES)]
	public string $bytes;

	/** @var mixed[] */
	#[Assert\All([new Assert\Type('int')])]
	public array $integers;

	#[Assert\Collection(
		fields: [
			'host' => [new Assert\NotBlank(), new Assert\Hostname()],
			'port' => new Assert\Optional([new Assert\Range(min: 1, max: 65535)]),
			'tag' => new Assert\Optional(),
		],
	)]
	public array $server;

	#[Assert\AtLeastOneOf([new Assert\Email(), new Assert\Ip(version: Assert\Ip::V4)])]
	public string $contact;

	#[Assert\AtLeastOneOf([new Assert\Email(), new Assert\Callback(callback: 'validate')])]
	public string $indescribable;

	#[Assert\Sequentially([new Assert\NotBlank(), new Assert\Length(max: 32)])]
	public string $sequential;

	#[Assert\Length(max: 5, groups: ['strict'])]
	public string $grouped;

	#[Assert\Callback(callback: 'validate')]
	public string $callback;

	/**
	 * @return list<string>
	 */
	public static function getRoles(): array
	{
		return ['admin', 'user'];
	}

}
