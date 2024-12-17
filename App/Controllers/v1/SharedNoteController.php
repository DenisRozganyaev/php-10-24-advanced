<?php

namespace App\Controllers\v1;

use App\Controllers\BaseApiController;
use App\Enums\Http\Status;
use App\Models\Note;
use App\Models\SharedNote;
use App\Validators\v1\SharedNoteValidator;
use splitbrain\phpcli\Exception;

class SharedNoteController extends BaseApiController
{
    protected ?Note $note;

    public function before(string $action, array $params = []): bool
    {
        $this->note = Note::find($params['note_id']);

        if (!$this->note) {
            throw new Exception('Note not found', Status::NOT_FOUND->value);
        }

        if ($this->note->user_id !== authId()) {
            throw new Exception('This resource is forbidden for you', Status::FORBIDDEN->value);
        }

        return true;
    }

    public function add(int $note_id)
    {
        $fields = [
            ...requestBody(),
            'note_id' => $note_id,
        ];

        if (SharedNoteValidator::validate($fields) && $note = SharedNote::createAndReturn($fields)) {
            return $this->response(Status::OK, $note->toArray());
        }

        return $this->response(Status::UNPROCESSABLE_ENTITY, $fields, SharedNoteValidator::getErrors());
    }

    public function remove(int $note_id)
    {
        // TODO: Do by yourself
        /**
         * Check if note is related to authId() user
         * check if shared_note record exists with entire data
         */
    }

    protected function getModel(): string
    {
        return SharedNote::class;
    }
}