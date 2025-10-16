<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20251016143227 extends AbstractMigration
{
    private const TABLE_PRODUCT = 'product';
    private const TABLE_COUPON = 'coupon';
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $tableProduct = $schema->createTable(self::TABLE_PRODUCT);
        $tableProduct->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $tableProduct->addColumn('name', Types::STRING)->setLength(191);
        $tableProduct->addColumn('price', Types::FLOAT);
        $tableProduct->setPrimaryKey(['id']);

        $tableCoupon = $schema->createTable(self::TABLE_COUPON);
        $tableCoupon->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $tableCoupon->addColumn('code', Types::STRING)->setLength(20);
        $tableCoupon->addColumn('type', Types::STRING)->setLength(10);
        $tableCoupon->addColumn('value', Types::FLOAT);
        $tableCoupon
            ->setPrimaryKey(['id'])
            ->addUniqueIndex(['code'], 'UNIQ_64BF3F0277153098')
        ;
    }

    public function down(Schema $schema): void
    {
        $schema
            ->dropTable(self::TABLE_PRODUCT)
            ->dropTable(self::TABLE_COUPON)
        ;
    }
}
