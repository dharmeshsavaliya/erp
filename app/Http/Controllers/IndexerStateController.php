<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Elasticsearch\Elasticsearch;
use App\Models\IndexerState;
use Exception;
use App\Models\MagentoModuleCareers as Career;
use App\StoreWebsite;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Elasticsearch\Reindex\Interfaces\Reindex;

class IndexerStateController extends Controller
{
    public function index(Request $request)
    {
        $indexerStates = IndexerState::all();
        $this->createIndexerStateIfNotExist();

        if ($request->ajax()) {
            return view('indexer_state.list', [
                'indexerStates' => $indexerStates
            ]);
        }

        return view('indexer_state.index', [
            'indexerStates' => $indexerStates
        ]);
    }

    public function elasticConnect()
    {
        try {
            $elastic = new Elasticsearch();
            $elastic->connect();
            $elastic->getConn()->ping();
            return response()->json([
                'code' => 200,
                'message' => 'Connection successful to elasticsearch.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reindex(Request $request)
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;

            if ($id === null) {
                throw new \Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new \Exception(sprintf('Indexer with %s id not found.', $id));
            }

            if ($indexerState->isSkip()) {
                throw new \Exception(sprintf('Cannot start again reindex for index: %s', $indexerState->getIndex()));
            }

            Artisan::call('reindex:messages');
        } catch (\Throwable $throwable) {

        }
        return response()->json(['message' => 'Reindex successful, reload page.', 'code' => 200]);
    }

    private function createIndexerStateIfNotExist(): void
    {
        foreach (IndexerState::INDEXER_MAPPING as $index => $className) {
            $exists = IndexerState::where(IndexerState::INDEX, $index)->exists();

            if (!$exists) {
                $indexerState = new IndexerState();
                $indexerState->setIndex($index);
                $indexerState->setStatus(Reindex::INVALIDATE);
                $indexerState->save();
            }
        }
    }
}