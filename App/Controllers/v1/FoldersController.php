<?php

namespace App\Controllers\v1;

use App\Controllers\BaseApiController;
use App\Enums\Http\Status;
use App\Enums\SQL;
use App\Models\Folder;

class FoldersController extends BaseApiController
{
    public function index()
    {
        $folders = Folder::where('user_id', value: authId())
            ->or('user_id', SQL::IS, null)
            ->orderBy([
                'updated_at' => 'DESC'
            ])
            ->get();

        return $this->response(Status::OK, $folders);
    }

    public function show(int $id)
    {
        return $this->response(Status::OK, Folder::find($id)?->toArray());
    }

    public function store()
    {

    }

    public function update(int $id)
    {

    }

    public function destroy(int $id)
    {

    }

    protected function getModel(): string
    {
        return Folder::class;
    }
}
