<?php

namespace App\Controllers\v1;

use App\Controllers\BaseApiController;
use App\Enums\Http\Status;
use App\Enums\SQL;
use App\Models\Folder;
use App\Models\Note;
use App\Validators\v1\FolderValidator;
use splitbrain\phpcli\Exception;

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
        $fields = requestBody();

        if (FolderValidator::validate($fields) && $folder = Folder::createAndReturn([...$fields, 'user_id' => authId()])) {
            return $this->response(Status::OK, $folder->toArray());
        }

        return $this->response(Status::UNPROCESSABLE_ENTITY, $fields, FolderValidator::getErrors());
    }

    public function update(int $id)
    {
        $fields = [
            ...requestBody(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (FolderValidator::validate($fields) && $folder = $this->model->update($fields)) {
            return $this->response(Status::OK, $folder->toArray());
        }

        return $this->response(Status::UNPROCESSABLE_ENTITY, $fields, FolderValidator::getErrors());
    }

    public function destroy(int $id)
    {
        $result = $this->model->destroy();

        if (!$result) {
            return $this->response(Status::UNPROCESSABLE_ENTITY, [], [
                'message' => 'Oops, smth went wrong'
            ]);
        }

        return $this->response(Status::OK, $this->model->toArray());
    }

    public function notes(int $id)
    {
        $folder = Folder::find($id);

        if (!is_null($folder->user_id) && $folder->user_id !== authId()) {
            throw new Exception('This folder is forbidden for you', Status::FORBIDDEN->value);
        }

        $notes = match (true) {
            $folder->title === Folder::DEFAULTS['general'] && is_null($folder->user_id) =>
            Note::where('user_id', value: authId())
                ->and('folder_id', value: $folder->id)
                ->get(),
            $folder->title === Folder::DEFAULTS['shared'] && is_null($folder->user_id) =>
            Note::select(['notes.*'])
                ->join(
                    'shared_notes',
                    [
                        [
                            'left' => 'notes.id',
                            'operator' => SQL::EQUAL->value,
                            'right' => 'shared_notes.note_id'
                        ],
                        [
                            'left' => 'shared_notes.user_id',
                            'operator' => SQL::EQUAL->value,
                            'right' => authId()
                        ],
                    ],
                    'INNER'
                )->get(),
            default => Note::where('folder_id', value: $folder->id)->get()
        };

        return $this->response(Status::OK, $notes);
    }

    protected function getModel(): string
    {
        return Folder::class;
    }
}
