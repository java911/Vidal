<?php
namespace Vidal\DrugBundle\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * My custom datatype.
 */
class BitType extends Type
{
	const MYTYPE = 'bit'; // modify to match your type name

	public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		// return the SQL used to create your column type. To create a portable column type, use the $platform.
		return $platform->getDoctrineTypeMapping('bit');
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		// This is executed when the value is read from the database. Make your conversions here, optionally using the $platform.
		return $value;
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		// This is executed when the value is written to the database. Make your conversions here, optionally using the $platform.
		return $value;
	}

	public function getName()
	{
		return self::MYTYPE; // modify to match your constant name
	}
}