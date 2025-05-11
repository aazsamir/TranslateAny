<?php

declare(strict_types=1);

namespace Tests\Mock;

use Laminas\Diactoros\UploadedFile;
use Tempest\Http\Upload;

class UploadMock
{
    public static function upload(): Upload
    {
        return new Upload(
            self::uploadedFile(),
        );
    }

    public static function uploadedFile(): UploadedFile
    {
        return new UploadedFile(
            streamOrFile: \realpath(__DIR__ . '/file'),
            size: null,
            errorStatus: 0,
        );
    }
}
