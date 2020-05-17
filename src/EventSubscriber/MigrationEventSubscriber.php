<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * @codeCoverageIgnore
 */
class MigrationEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return array(
            ToolEvents::postGenerateSchema,
        );
    }

    /**
     * Avoid public schema creation on doctrine:migration:diff
     * @throws SchemaException
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $Args): void
    {
        $Schema = $Args->getSchema();

        if (!$Schema->hasNamespace('public')) {
            $Schema->createNamespace('public');
        }
    }
}
