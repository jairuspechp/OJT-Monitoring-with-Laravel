<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    private const MODES = ['standard', 'horizontal', 'solo', 'three'];

    private function clampCount($n): int
    {
        $n = (int) $n;
        return min(20, max(1, $n ?: 4));
    }

    private function mode($m): string
    {
        return in_array($m, self::MODES, true) ? $m : 'standard';
    }

    // GET /api/boards
    public function index(): JsonResponse
    {
        $boards = Board::with('slots')->orderBy('created_at')->get()->map(function ($b) {
            $byIndex = $b->slots->keyBy('slot_index');
            $slots = [];
            for ($i = 0; $i < $b->slot_count; $i++) {
                $s = $byIndex->get($i);
                $slots[] = $s ? ['label' => $s->label, 'url' => $s->url] : null;
            }
            return [
                'id' => $b->id,
                'name' => $b->name,
                'layoutMode' => $b->layout_mode,
                'slots' => $slots,
            ];
        });

        return response()->json(['boards' => $boards]);
    }

    // POST /api/boards
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:40', 'unique:boards,id'],
            'name' => ['nullable', 'string'],
            'slotCount' => ['nullable'],
            'layoutMode' => ['nullable', 'string'],
        ]);

        Board::create([
            'id' => $data['id'],
            'name' => mb_substr($data['name'] ?? 'Untitled', 0, 255) ?: 'Untitled',
            'layout_mode' => $this->mode($data['layoutMode'] ?? null),
            'slot_count' => $this->clampCount($data['slotCount'] ?? 4),
            'created_at' => (int) round(microtime(true) * 1000),
        ]);

        return response()->json(['ok' => true]);
    }

    // PATCH /api/boards/{id}
    public function update(Request $request, string $id): JsonResponse
    {
        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            return response()->json(['error' => 'Name required'], 400);
        }
        Board::where('id', $id)->update(['name' => mb_substr($name, 0, 255)]);
        return response()->json(['ok' => true]);
    }

    // DELETE /api/boards/{id}  (slots removed by ON DELETE CASCADE)
    public function destroy(string $id): JsonResponse
    {
        Board::where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }

    // PUT /api/boards/{id}/layout
    public function layout(Request $request, string $id): JsonResponse
    {
        $count = $this->clampCount($request->input('slotCount'));
        $mode = $this->mode($request->input('layoutMode'));

        DB::transaction(function () use ($id, $count, $mode) {
            Board::where('id', $id)->update(['slot_count' => $count, 'layout_mode' => $mode]);
            Slot::where('board_id', $id)->where('slot_index', '>=', $count)->delete();
        });

        return response()->json(['ok' => true]);
    }

    // PUT /api/boards/{id}/slots/{index}
    public function saveSlot(Request $request, string $id, int $index): JsonResponse
    {
        $url = $request->input('url');
        if ($index < 0 || !$url) {
            return response()->json(['error' => 'Invalid slot'], 400);
        }

        Slot::upsert(
            [[
                'board_id' => $id,
                'slot_index' => $index,
                'label' => mb_substr((string) $request->input('label', ''), 0, 255),
                'url' => mb_substr((string) $url, 0, 2048),
            ]],
            ['board_id', 'slot_index'],
            ['label', 'url']
        );

        return response()->json(['ok' => true]);
    }

    // DELETE /api/boards/{id}/slots/{index}
    public function clearSlot(string $id, int $index): JsonResponse
    {
        Slot::where('board_id', $id)->where('slot_index', $index)->delete();
        return response()->json(['ok' => true]);
    }
}
