<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server;

use function __;
use PhpMyAdmin\Url;
use function intval;
use PhpMyAdmin\Util;
use PhpMyAdmin\Import;
use PhpMyAdmin\Message;
use PhpMyAdmin\Plugins;
use function is_numeric;
use PhpMyAdmin\Charsets;
use PhpMyAdmin\Encoding;
use PhpMyAdmin\Template;
use PhpMyAdmin\Import\Ajax;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Utils\ForeignKey;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\Controllers\AbstractController;

final class ImportController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(ResponseRenderer $response, Template $template, DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
        $this->dbi = $dbi;
    }

    public function __invoke(): void
    {
        global $db, $table, $SESSION_KEY, $cfg, $errorUrl;

        $pageSettings = new PageSettings('Import');
        $pageSettingsErrorHtml = $pageSettings->getErrorHTML();
        $pageSettingsHtml = $pageSettings->getHTML();

        $this->addScriptFiles(['import.js']);
        $errorUrl = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        [$SESSION_KEY, $uploadId] = Ajax::uploadProgressSetup();

        $importList = Plugins::getImport('server');

        if (empty($importList)) {
            $this->response->addHTML(Message::error(__(
                'Could not load import plugins, please check your installation!'
            ))->getDisplay());

            return;
        }

        $offset = null;
        if (isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset'])) {
            $offset = intval($_REQUEST['offset']);
        }

        $timeoutPassed = $_REQUEST['timeout_passed'] ?? null;
        $localImportFile = $_REQUEST['local_import_file'] ?? null;
        $compressions = Import::getCompressions();

        $charsets = Charsets::getCharsets($this->dbi, $cfg['Server']['DisableIS']);

        $idKey = $_SESSION[$SESSION_KEY]['handler']::getIdKey();
        $hiddenInputs = [
            $idKey => $uploadId,
            'import_type' => 'server',
        ];

        $default = isset($_GET['format']) ? (string) $_GET['format'] : Plugins::getDefault('Import', 'format');
        $choice = Plugins::getChoice($importList, $default);
        $options = Plugins::getOptions('Import', $importList);
        $skipQueriesDefault = Plugins::getDefault('Import', 'skip_queries');
        $isAllowInterruptChecked = Plugins::checkboxCheck('Import', 'allow_interrupt');
        $maxUploadSize = (int) $GLOBALS['config']->get('max_upload_size');

        $this->render('server/import/index', [
            'page_settings_error_html' => $pageSettingsErrorHtml,
            'page_settings_html' => $pageSettingsHtml,
            'upload_id' => $uploadId,
            'handler' => $_SESSION[$SESSION_KEY]['handler'],
            'hidden_inputs' => $hiddenInputs,
            'db' => $db,
            'table' => $table,
            'max_upload_size' => $maxUploadSize,
            'formatted_maximum_upload_size' => Util::getFormattedMaximumUploadSize($maxUploadSize),
            'plugins_choice' => $choice,
            'options' => $options,
            'skip_queries_default' => $skipQueriesDefault,
            'is_allow_interrupt_checked' => $isAllowInterruptChecked,
            'local_import_file' => $localImportFile,
            'is_upload' => $GLOBALS['config']->get('enable_upload'),
            'upload_dir' => $cfg['UploadDir'] ?? null,
            'timeout_passed_global' => $GLOBALS['timeout_passed'] ?? null,
            'compressions' => $compressions,
            'is_encoding_supported' => Encoding::isSupported(),
            'encodings' => Encoding::listEncodings(),
            'import_charset' => $cfg['Import']['charset'] ?? null,
            'timeout_passed' => $timeoutPassed,
            'offset' => $offset,
            'can_convert_kanji' => Encoding::canConvertKanji(),
            'charsets' => $charsets,
            'is_foreign_key_check' => ForeignKey::isCheckEnabled(),
            'user_upload_dir' => Util::userDir((string) ($cfg['UploadDir'] ?? '')),
            'local_files' => Import::getLocalFiles($importList),
        ]);
    }
}
