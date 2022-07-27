<?php

use App\ErpLog;

function printStatusView() {
}

function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null) {
    if (empty($timeZoneSource)) {
        $timeZoneSource = date_default_timezone_get();
    }
    if (empty($timeZoneTarget)) {
        $timeZoneTarget = date_default_timezone_get();
    }

    $dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
    $dt->setTimezone(new DateTimeZone($timeZoneTarget));

    return $dt->format("Y-m-d H:i:s");
}

/**
 * Create image and text
 *
 *
 */

function createProductTextImage($path, $uploadPath = "", $text = "", $color = "545b62", $fontSize = "40", $needAbs = true) {
    $text = wordwrap(strtoupper($text), 24, "\n");

    $img = \IImage::make($path);
    $img->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    // use callback to define details
    $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
        $font->file(public_path('/fonts/HelveticaNeue.ttf'));
        $font->size($fontSize);
        $font->color("#" . $color);
        $font->align('top');
    });

    $name = round(microtime(true) * 1000) . "_watermarked";

    if (!file_exists(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR))) {
        mkdir(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR), 0777, true);
    }

    $path = 'uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR . $name . '.jpg';

    $img->save(public_path($path));

    return ($needAbs) ? public_path($path) : url('/') . "/" . $path;
}

function get_folder_number($id) {
    return floor($id / config('constants.image_per_folder'));
}

function previous_sibling(array $elements, $previous_sibling = 0, &$branch = []) {
    foreach ($elements as $k => $element) {
        if ($element['previous_sibling'] == $previous_sibling && $previous_sibling != 0) {
            $branch[] = $element;
            previous_sibling($elements, $element["id"], $branch);
        }
    }

    return $branch;
}

/**
 * return all types of short message with postfix
 *
 */

function show_short_message($message, $size = 50, $postfix = "...") {
    $message = trim($message);

    $dot = "";

    if (strlen($message) > $size) {
        $dot = $postfix;
    }

    return substr($message, 0, $size) . $dot;
}

/**
 * key is using for to attach customer via session
 *
 */

function attach_customer_key() {
    return "customer_list_" . time() . "_" . auth()->user()->id;
}

/**
 *  get scraper last log file name
 */

function get_server_last_log_file($screaperName = "", $serverId = "") {
    $d = date('j', strtotime("-1 days"));
    return "/scrap-logs/file-view/" . $screaperName . "-" . $d . ".log/" . $serverId;
}

function getStartAndEndDate($week, $year) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('Y-m-d');
    $dto->modify('+7 days');
    $ret['week_end'] = $dto->format('Y-m-d');
    return $ret;
}

/**
 * Moved function from chat api to here due to duplicates
 *
 */
if (!function_exists('getInstance')) {
    function getInstance($number) {
        $number = !empty($number) ? $number : 0;
        return isset(config("apiwha.instances")[$number])
            ? config("apiwha.instances")[$number]
            : config("apiwha.instances")[0];
    }
}

function human_error_array($errors) {
    $list = [];
    if (!empty($errors)) {
        foreach ($errors as $key => $berror) {
            foreach ($berror as $serror) {
                $list[] = "{$key} : " . $serror;
            }
        }
    }

    return $list;
}

/**
 * Get all instances no with array list
 *
 */
if (!function_exists('getInstanceNo')) {
    function getInstanceNo() {
        $nos = config("apiwha.instances");

        $list = [];

        if (!empty($nos)) {
            foreach ($nos as $key => $no) {
                $n        = ($key == 0) ? $no["number"] : $key;
                $list[$n] = $n;
            }
        }

        return $list;
    }
}

/**
 * Check if the date is valid
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * dropdown returns in helpers
 *
 */

function drop_down_frequency() {
    return [
        "0"    => "Disabled",
        "1"    => "Just Once",
        "5"    => "Every 5 Minutes",
        "10"   => "Every 10 Minutes",
        "15"   => "Every 15 Minutes",
        "20"   => "Every 20 Minutes",
        "25"   => "Every 25 Minutes",
        "30"   => "Every 30 Minutes",
        "35"   => "Every 35 Minutes",
        "40"   => "Every 40 Minutes",
        "45"   => "Every 45 Minutes",
        "50"   => "Every 50 Minutes",
        "55"   => "Every 55 Minutes",
        "60"   => "Every Hour",
        "360"  => "Every 6 hr",
        "1440" => "Every 24 hr",
    ];
}

/**
 * format the duration in Hour:minute:seconds format
 */
function formatDuration($seconds_time) {
    if ($seconds_time < 24 * 60 * 60) {
        return gmdate('H:i:s', $seconds_time);
    } else {
        $hours   = floor($seconds_time / 3600);
        $minutes = floor(($seconds_time - $hours * 3600) / 60);
        $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));
        return "$hours:$minutes:$seconds";
    }
}

function get_field_by_number($no, $field = "name") {
    $no = explode("@", $no);

    if (!empty($no[0])) {

        $customer = \App\Customer::where("phone", $no[0])->first();
        if ($customer) {
            return $customer->{$field} . " (Customer)";
        }

        $vendor = \App\Vendor::where("phone", $no[0])->first();
        if ($vendor) {
            return $vendor->{$field} . " (Vendor)";
        }

        $supplier = \App\Supplier::where("phone", $no[0])->first();
        if ($supplier) {
            return $supplier->{$field} . "(Supplier)";
        }
    }

    return "";
}

function splitTextIntoSentences($text) {
    return preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text);
}

function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function array_find($needle, array $haystack) {
    foreach ($haystack as $key => $value) {
        if (false !== stripos($value, $needle)) {
            return true;
        }
    }
    return false;
}

function GUID() {
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function replace_dash($string) {

    $string = str_replace(' ', '_', strtolower($string)); // Replaces all spaces with hyphens.
    $string = str_replace('-', '_', strtolower($string)); // Replaces all spaces with hyphens.

    //$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/\s+/', '_', strtolower($string));
}

function storeERPLog($erpData) {

    if (!empty($erpData)) {

        $erpData['request']  = json_encode($erpData['request']);
        $erpData['response'] = json_encode($erpData['response']);
        ErpLog::create($erpData);
    }
}
function getStr($srt) {

    preg_match("/\[(.*)\]/", $srt, $matches);
    if ($matches && $matches[0] !== '') {
        return true;
    };
    return false;
}

function string_convert($msg2) {

    // $message = str_replace('||',"\n",$msg2);
    // $message = json_encode($msg2);
    $message = explode("||", $msg2);

    return $message;
}

function convertToThumbUrl($url, $extension) {
    $arr                  = explode("/", $url);
    $arr[count($arr) - 1] = 'thumbnail/' . $arr[count($arr) - 1];

    $converted_str = implode('/', $arr);

    return str_replace('.' . $extension, '_thumb.' . $extension, $converted_str); // if product name is abc.jpg than thumb url name is abc_thumb.jpg name with in /thumbnaiil folder of relateable folder path.
}

function resizeCropImage($max_width = 150, $max_height = 150, $source_file, $dst_dir = null, $quality = 80) {

    if ($dst_dir === null) {
        $dst_dir = $source_file;
    }
    $imgsize = getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            $quality      = 7;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            $quality      = 80;
            break;

        default:
            return false;
            break;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);

    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if ($width_new > $width) {
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        // return true;
    } else {
        //cut point by width
        $w_point            = (($width - $width_new) / 2);
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        // return true;

    }

    //    if($image === 'imagepng'){
    //        imagesavealpha($dst_img, true);
    //    }
    // dd($dst_img, $dst_dir, $quality);
    $image($dst_img, $dst_dir, $quality);

    if ($dst_img) {
        $imagedestroy = imagedestroy($dst_img);
    }

    if ($src_img) {
        $imagedestroy = imagedestroy($src_img);
    }

    return @file_get_contents($dst_dir);
}


function _p($data, $exit = 0) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    echo '<pre>______________________________________________________________________________________________________________</pre>';
    if ($exit) die('');
}
function dateRangeArr($stDate, $enDate) {
    $data = [];
    while ($stDate <= $enDate) {
        $data[] = [
            'date' => $stDate,
            'day' => strtolower(date('l', strtotime($stDate))),
        ];
        $stDate = date('Y-m-d', strtotime($stDate . '+1 day'));
    }
    return $data;
}
function pad0($curr) {
    return $curr < 10 ? '0' . $curr : $curr;
}
function nextHour($curr) {
    $curr++;
    if ($curr == 24) $curr = '0';
    return $curr < 10 ? '0' . $curr : $curr;
}
function hourlySlots($stTime, $enTime) {
    $slots = [];
    $intrvl =  strtotime("1970-01-01 01:00:00 UTC");

    $dateTimes = new \DatePeriod(
        new \DateTime($stTime),
        new \DateInterval('PT' . $intrvl . 'S'),
        new \DateTime($enTime)
    );
    foreach ($dateTimes as $dt) {
        $slots[] = $dt->format('Y-m-d H:i');
    }
    return $slots;
}

function siteJs($path) {
    return env('APP_URL') . '/js/pages/' . $path . '?v=' . date('YmdH');
}
function makeDropdown($options = [], $selected = []) {
    if (!is_array($selected)) {
        $selected = is_numeric($selected) ? (int) $selected : $selected;
    }
    $return = [];
    if (count($options)) {
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $return[] = '<optgroup label="' . $k . '">';
                $return[] = makeDropdown($v, $selected);
                $return[] = '</optgroup>';
            } else {
                $sel = '';
                if (is_array($selected)) {
                    if (in_array($k, $selected))
                        $sel = 'selected';
                } else if ($selected === $k) {
                    $sel = 'selected';
                }
                $return[] = '<option value="' . $k . '" ' . $sel . '>' . ucfirst(trim(strip_tags($v))) . '</option>';
            }
        }
    }
    return implode('', $return);
}
function exMessage($e) {
    return 'Error on line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
}
function respException($e, $data = []) {
    return response()->json(array_merge_recursive(['message' => exMessage($e)], $data), 500);
}
function isDeveloperTaskId($id) {
    return substr($id, 0, 3) == 'DT-' ? str_replace('DT-', '', $id) : 0;
}
function isRegularTaskId($id) {
    return substr($id, 0, 2) == 'T-' ? str_replace('T-', '', $id) : 0;
}
function respJson($code, $message = '', $data = []) {
    return response()->json(array_merge_recursive(['message' => $message], $data), $code);
}

function dailyHours($type = null) {
    $data = [];
    for ($i = 0; $i < 24; $i++) {
        $temp = pad0($i) . ':00:00';
        $data[$temp] = $temp;
    }
    return $data;
}
