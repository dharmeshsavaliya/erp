<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex;

use App\Elasticsearch\Elasticsearch;
use App\Models\IndexerState;
use App\Elasticsearch\Reindex\Interfaces\Reindex as ReindexInterface;
use Throwable;
use Log;

class Reindex
{
    public function execute(): void
    {
        $indexerState = IndexerState::all();

        /** @var IndexerState $indexer */
        foreach ($indexerState as $indexer) {
            try {
                // skip running indexes
                if ($indexer->isSkip()) {
                    continue;
                }

                $indexer->setStatus(ReindexInterface::RUNNING);
                $indexer->save();

                $this->createIndexIfNotExist($indexer->getIndex());
                $this->removeAll($indexer->getIndex());

                $className = $indexer->getClassName();

                /** @var ReindexInterface $class */
                $class = new $className();
                $class->execute();

                $indexer->setStatus(ReindexInterface::VALID);
                $indexer->save();
            } catch (Throwable $throwable) {
                $indexer->setStatus(ReindexInterface::INVALIDATE);
                $indexer->save();

                Log::error('Reindex error: ' . $throwable->getMessage() . ' trace: ' . json_encode($throwable->getTrace()));
            }
        }
    }

    private function removeAll(string $index): void
    {
        Elasticsearch::deleteByQuery([
            'index' => $index,
            'body' => [
                'query' => [
                    'match_all' => (object)[]
                ]
            ]]);
    }

    public function createIndexIfNotExist(string $index): void
    {
        $indexParams = [
            'index' => $index
        ];
        if (!Elasticsearch::indices()->exists($indexParams)) {
            Elasticsearch::indices()->create($indexParams);
        }
    }
}