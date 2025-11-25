<?php

namespace Tests\Unit;

use App\Services\StorageDocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageDocumentServiceTest extends TestCase
{
    public function test_storage_document_returns_path_and_merges_data()
    {
        Storage::fake('s3');
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $requestData = ['name' => 'Teste'];
        request()->files->set('document', $file);

        $service = new StorageDocumentService();
        $result = $service->storageDocument($requestData);

        $this->assertArrayHasKey('document_path', $result);
        $this->assertStringStartsWith('documents/', $result['document_path']);
        Storage::disk('s3')->assertExists($result['document_path']);
    }
}

