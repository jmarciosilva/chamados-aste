<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class AttachmentService
{
    /**
     * Pasta base de uploads públicos
     */
    private string $basePath = 'uploads/tickets';

    /**
     * Upload de imagem colada (editor)
     */
    public function uploadEditorImage(UploadedFile $file): string
    {
        $filename = uniqid('editor_') . '.' . $file->getClientOriginalExtension();

        $destination = public_path($this->basePath . '/temp');

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return asset($this->basePath . '/temp/' . $filename);
    }

    /**
     * Upload de anexos do ticket
     */
    public function uploadTicketAttachment(
        UploadedFile $file,
        int $ticketId
    ): string {
        $filename = uniqid('attach_') . '.' . $file->getClientOriginalExtension();

        $destination = public_path($this->basePath . '/' . $ticketId);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return $this->basePath . '/' . $ticketId . '/' . $filename;
    }
}
