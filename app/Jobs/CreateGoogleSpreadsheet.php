<?php

namespace App\Jobs;

use App\GoogleDoc;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Service_Sheets_Spreadsheet;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateGoogleSpreadsheet
{
    use Dispatchable, SerializesModels;

    private $googleDoc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GoogleDoc $googleDoc)
    {
        $this->googleDoc = $googleDoc;
    }

    /**
     * Execute the job.
     * Sample spread sheet link
     * https://docs.google.com/spreadsheets/d/1jJAHaCJfTfqNuMQrbs9Uz-x39XxDd2PdFGZUIcJ2SKg/edit
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        try {
            $createFile = $this->createDriveFile(env('GOOGLE_SHARED_FOLDER'));
            $spreadsheetId = $createFile->id;

            $this->googleDoc->docId = $spreadsheetId;
            $this->googleDoc->save();
        } catch (Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();
            dd($e);
        }
    }

    public function moveFileToFolder($fileId, $folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $emptyFileMetadata = new DriveFile();
            // Retrieve the existing parents to remove
            $file = $driveService->files->get($fileId, array('fields' => 'parents'));

            $previousParents = join(',', $file->parents);

            // Move the file to the new folder
            $file = $driveService->files->update($fileId, $emptyFileMetadata, array(
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'));
            return $file->parents;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
        }
    }

    public function createDriveFile($folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile(array(
                'name' => $this->googleDoc->name,
                'parents' => array($folderId),
                "mimeType" => "application/vnd.google-apps.spreadsheet",
            ));

            $file = $driveService->files->create($fileMetadata, array(
                'fields' => 'id,parents,mimeType'
            ));

            return $file;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
            dd($e);
        }
    }

    public function createSpreadSheet()
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $service = new \Google_Service_Sheets($client);

        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => $this->googleDoc->name
            ]
        ]);

        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId,properties'
        ]);

        return $spreadsheet->spreadsheetId;
    }
}