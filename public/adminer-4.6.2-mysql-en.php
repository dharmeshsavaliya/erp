<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
*
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*
* @version 4.6.2
*/error_reporting(6135);
$tc = ! preg_match('~^(unsafe_raw)?$~', ini_get('filter.default'));
if ($tc || ini_get('filter.default_flags')) {
    foreach (['_GET', '_POST', '_COOKIE', '_SERVER']as$X) {
        $Qg = filter_input_array(constant("INPUT$X"), FILTER_UNSAFE_RAW);
        if ($Qg) {
            $$X = $Qg;
        }
    }
} if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('8bit');
}function connection()
{
    global$e;

    return$e;
}function adminer()
{
    global$b;

    return$b;
}function version()
{
    global$ga;

    return$ga;
}function idf_unescape($Qc)
{
    $md = substr($Qc, -1);

    return
    str_replace($md.$md, $md, substr($Qc, 1, -1));
}function escape_string($X)
{
    return
    substr(q($X), 1, -1);
}function number($X)
{
    return
    preg_replace('~[^0-9]+~', '', $X);
}function number_type()
{
    return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';
}function remove_slashes($Ye, $tc = false)
{
    if (get_magic_quotes_gpc()) {
        while ([$z, $X] = each($Ye)) {
            foreach ($X
            as$fd => $W) {
                unset($Ye[$z][$fd]);
                if (is_array($W)) {
                    $Ye[$z][stripslashes($fd)] = $W;
                    $Ye[] = &$Ye[$z][stripslashes($fd)];
                } else {
                    $Ye[$z][stripslashes($fd)] = ($tc ? $W : stripslashes($W));
                }
            }
        }
    }
}function bracket_escape($Qc, $_a = false)
{
    static$Eg = [':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4'];

    return
    strtr($Qc, ($_a ? array_flip($Eg) : $Eg));
}function min_version($eh, $zd = '', $f = null)
{
    global$e;
    if (! $f) {
        $f = $e;
    }
    $Gf = $f->server_info;
    if ($zd && preg_match('~([\d.]+)-MariaDB~', $Gf, $C)) {
        $Gf = $C[1];
        $eh = $zd;
    }

    return version_compare($Gf, $eh) >= 0;
}function charset($e)
{
    return min_version('5.5.3', 0, $e) ? 'utf8mb4' : 'utf8';
}function script($Nf, $Dg = "\n")
{
    return'<script'.nonce().">$Nf</script>$Dg";
}function script_src($Vg)
{
    return"<script src='".h($Vg)."'".nonce()."></script>\n";
}function nonce()
{
    return' nonce="'.get_nonce().'"';
}function target_blank()
{
    return' target="_blank" rel="noreferrer noopener"';
}function h($Xf)
{
    return
    str_replace("\0", '&#0;', htmlspecialchars($Xf, ENT_QUOTES, 'utf-8'));
}function nbsp($Xf)
{
    return trim($Xf) != '' ? h($Xf) : '&nbsp;';
}function nl_br($Xf)
{
    return
    str_replace("\n", '<br>', $Xf);
}function checkbox($E, $Y, $Na, $jd = '', $ie = '', $Ra = '', $kd = '')
{
    $K = "<input type='checkbox' name='$E' value='".h($Y)."'".($Na ? ' checked' : '').($kd ? " aria-labelledby='$kd'" : '').'>'.($ie ? script("qsl('input').onclick = function () { $ie };", '') : '');

    return $jd != '' || $Ra ? '<label'.($Ra ? " class='$Ra'" : '').">$K".h($jd).'</label>' : $K;
}function optionlist($me, $Bf = null, $Yg = false)
{
    $K = '';
    foreach ($me
    as$fd => $W) {
        $ne = [$fd => $W];
        if (is_array($W)) {
            $K .= '<optgroup label="'.h($fd).'">';
            $ne = $W;
        }
        foreach ($ne
        as$z => $X) {
            $K .= '<option'.($Yg || is_string($z) ? ' value="'.h($z).'"' : '').(($Yg || is_string($z) ? (string) $z : $X) === $Bf ? ' selected' : '').'>'.h($X);
        }
        if (is_array($W)) {
            $K .= '</optgroup>';
        }
    }

    return$K;
}function html_select($E, $me, $Y = '', $he = true, $kd = '')
{
    if ($he) {
        return"<select name='".h($E)."'".($kd ? " aria-labelledby='$kd'" : '').'>'.optionlist($me, $Y).'</select>'.(is_string($he) ? script("qsl('select').onchange = function () { $he };", '') : '');
    }
    $K = '';
    foreach ($me
    as$z => $X) {
        $K .= "<label><input type='radio' name='".h($E)."' value='".h($z)."'".($z == $Y ? ' checked' : '').'>'.h($X).'</label>';
    }

    return$K;
}function select_input($wa, $me, $Y = '', $he = '', $Le = '')
{
    $mg = ($me ? 'select' : 'input');

    return"<$mg$wa".($me ? "><option value=''>$Le".optionlist($me, $Y, true).'</select>' : " size='10' value='".h($Y)."' placeholder='$Le'>").($he ? script("qsl('$mg').onchange = $he;", '') : '');
}function confirm($D = '', $Cf = "qsl('input')")
{
    return
    script("$Cf.onclick = function () { return confirm('".($D ? js_escape($D) : 'Are you sure?')."'); };", '');
}function print_fieldset($u, $rd, $hh = false)
{
    echo'<fieldset><legend>',"<a href='#fieldset-$u'>$rd</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$u');", ''),'</legend>',"<div id='fieldset-$u'".($hh ? '' : " class='hidden'").">\n";
}function bold($Ga, $Ra = '')
{
    return $Ga ? " class='active $Ra'" : ($Ra ? " class='$Ra'" : '');
}function odd($K = ' class="odd"')
{
    static$t = 0;
    if (! $K) {
        $t = -1;
    }

    return $t++ % 2 ? $K : '';
}function js_escape($Xf)
{
    return
    addcslashes($Xf, "\r\n'\\/");
}function json_row($z, $X = null)
{
    static$uc = true;
    if ($uc) {
        echo'{';
    }
    if ($z != '') {
        echo($uc ? '' : ',')."\n\t\"".addcslashes($z, "\r\n\t\"\\/").'": '.($X !== null ? '"'.addcslashes($X, "\r\n\"\\/").'"' : 'null');
        $uc = false;
    } else {
        echo"\n}\n";
        $uc = true;
    }
}function ini_bool($Uc)
{
    $X = ini_get($Uc);

    return preg_match('~^(on|true|yes)$~i', $X) || (int) $X;
}function sid()
{
    static$K;
    if ($K === null) {
        $K = (SID && ! ($_COOKIE && ini_bool('session.use_cookies')));
    }

    return$K;
}function set_password($dh, $O, $V, $G)
{
    $_SESSION['pwds'][$dh][$O][$V] = ($_COOKIE['adminer_key'] && is_string($G) ? [encrypt_string($G, $_COOKIE['adminer_key'])] : $G);
}function get_password()
{
    $K = get_session('pwds');
    if (is_array($K)) {
        $K = ($_COOKIE['adminer_key'] ? decrypt_string($K[0], $_COOKIE['adminer_key']) : false);
    }

    return$K;
}function q($Xf)
{
    global$e;

    return$e->quote($Xf);
}function get_vals($I, $c = 0)
{
    global$e;
    $K = [];
    $J = $e->query($I);
    if (is_object($J)) {
        while ($L = $J->fetch_row()) {
            $K[] = $L[$c];
        }
    }

    return$K;
}function get_key_vals($I, $f = null, $ug = 0, $Jf = true)
{
    global$e;
    if (! is_object($f)) {
        $f = $e;
    }
    $K = [];
    $f->timeout = $ug;
    $J = $f->query($I);
    $f->timeout = 0;
    if (is_object($J)) {
        while ($L = $J->fetch_row()) {
            if ($Jf) {
                $K[$L[0]] = $L[1];
            } else {
                $K[] = $L[0];
            }
        }
    }

    return$K;
}function get_rows($I, $f = null, $k = "<p class='error'>")
{
    global$e;
    $eb = (is_object($f) ? $f : $e);
    $K = [];
    $J = $eb->query($I);
    if (is_object($J)) {
        while ($L = $J->fetch_assoc()) {
            $K[] = $L;
        }
    } elseif (! $J && ! is_object($f) && $k && defined('PAGE_HEADER')) {
        echo$k.error()."\n";
    }

    return$K;
}function unique_array($L, $w)
{
    foreach ($w
    as$v) {
        if (preg_match('~PRIMARY|UNIQUE~', $v['type'])) {
            $K = [];
            foreach ($v['columns']as$z) {
                if (! isset($L[$z])) {
                    continue
                    2;
                }
                $K[$z] = $L[$z];
            }

            return$K;
        }
    }
}function escape_key($z)
{
    if (preg_match('(^([\w(]+)('.str_replace('_', '.*', preg_quote(idf_escape('_'))).')([ \w)]+)$)', $z, $C)) {
        return$C[1].idf_escape(idf_unescape($C[2])).$C[3];
    }

    return
        idf_escape($z);
}function where($Z, $m = [])
{
    global$e,$y;
    $K = [];
    foreach ((array) $Z['where']as$z => $X) {
        $z = bracket_escape($z, 1);
        $c = escape_key($z);
        $K[] = $c.($y == 'sql' && preg_match('~^[0-9]*\\.[0-9]*$~', $X) ? ' LIKE '.q(addcslashes($X, '%_\\')) : ($y == 'mssql' ? ' LIKE '.q(preg_replace('~[_%[]~', '[\0]', $X)) : ' = '.unconvert_field($m[$z], q($X))));
        if ($y == 'sql' && preg_match('~char|text~', $m[$z]['type']) && preg_match('~[^ -@]~', $X)) {
            $K[] = "$c = ".q($X).' COLLATE '.charset($e).'_bin';
        }
    }
    foreach ((array) $Z['null']as$z) {
        $K[] = escape_key($z).' IS NULL';
    }

    return
        implode(' AND ', $K);
}function where_check($X, $m = [])
{
    parse_str($X, $Ma);
    remove_slashes([&$Ma]);

    return
    where($Ma, $m);
}function where_link($t, $c, $Y, $je = '=')
{
    return"&where%5B$t%5D%5Bcol%5D=".urlencode($c)."&where%5B$t%5D%5Bop%5D=".urlencode(($Y !== null ? $je : 'IS NULL'))."&where%5B$t%5D%5Bval%5D=".urlencode($Y);
}function convert_fields($d, $m, $N = [])
{
    $K = '';
    foreach ($d
    as$z => $X) {
        if ($N && ! in_array(idf_escape($z), $N)) {
            continue;
        }
        $ua = convert_field($m[$z]);
        if ($ua) {
            $K .= ", $ua AS ".idf_escape($z);
        }
    }

    return$K;
}function cookie($E, $Y, $ud = 2592000)
{
    global$ba;

    return
    header("Set-Cookie: $E=".urlencode($Y).($ud ? '; expires='.gmdate('D, d M Y H:i:s', time() + $ud).' GMT' : '').'; path='.preg_replace('~\\?.*~', '', $_SERVER['REQUEST_URI']).($ba ? '; secure' : '').'; HttpOnly; SameSite=lax', false);
}function restart_session()
{
    if (! ini_bool('session.use_cookies')) {
        session_start();
    }
}function stop_session()
{
    if (! ini_bool('session.use_cookies')) {
        session_write_close();
    }
}function &get_session($z)
{
    return$_SESSION[$z][DRIVER][SERVER][$_GET['username']];
}function set_session($z, $X)
{
    $_SESSION[$z][DRIVER][SERVER][$_GET['username']] = $X;
}function auth_url($dh, $O, $V, $i = null)
{
    global$Ib;
    preg_match('~([^?]*)\\??(.*)~', remove_from_uri(implode('|', array_keys($Ib)).'|username|'.($i !== null ? 'db|' : '').session_name()), $C);

    return"$C[1]?".(sid() ? SID.'&' : '').($dh != 'server' || $O != '' ? urlencode($dh).'='.urlencode($O).'&' : '').'username='.urlencode($V).($i != '' ? '&db='.urlencode($i) : '').($C[2] ? "&$C[2]" : '');
}function is_ajax()
{
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}function redirect($B, $D = null)
{
    if ($D !== null) {
        restart_session();
        $_SESSION['messages'][preg_replace('~^[^?]*~', '', ($B !== null ? $B : $_SERVER['REQUEST_URI']))][] = $D;
    }
    if ($B !== null) {
        if ($B == '') {
            $B = '.';
        }
        header("Location: $B");
        exit;
    }
}function query_redirect($I, $B, $D, $gf = true, $gc = true, $nc = false, $tg = '')
{
    global$e,$k,$b;
    if ($gc) {
        $Tf = microtime(true);
        $nc = ! $e->query($I);
        $tg = format_time($Tf);
    }
    $Pf = '';
    if ($I) {
        $Pf = $b->messageQuery($I, $tg, $nc);
    }
    if ($nc) {
        $k = error().$Pf.script('messagesPrint();');

        return
        false;
    }
    if ($gf) {
        redirect($B, $D.$Pf);
    }

    return
        true;
}function queries($I)
{
    global$e;
    static$bf = [];
    static$Tf;
    if (! $Tf) {
        $Tf = microtime(true);
    }
    if ($I === null) {
        return
        [implode("\n", $bf), format_time($Tf)];
    }
    $bf[] = (preg_match('~;$~', $I) ? "DELIMITER ;;\n$I;\nDELIMITER " : $I).';';

    return$e->query($I);
}function apply_queries($I, $S, $cc = 'table')
{
    foreach ($S
    as$Q) {
        if (! queries("$I ".$cc($Q))) {
            return
            false;
        }
    }

    return
        true;
}function queries_redirect($B, $D, $gf)
{
    [$bf, $tg] = queries(null);

    return
    query_redirect($bf, $B, $D, $gf, false, ! $gf, $tg);
}function format_time($Tf)
{
    return
    sprintf('%.3f s', max(0, microtime(true) - $Tf));
}function remove_from_uri($Ae = '')
{
    return
    substr(preg_replace("~(?<=[?&])($Ae".(SID ? '' : '|'.session_name()).')=[^&]*&~', '', "$_SERVER[REQUEST_URI]&"), 0, -1);
}function pagination($F, $pb)
{
    return' '.($F == $pb ? $F + 1 : '<a href="'.h(remove_from_uri('page').($F ? "&page=$F".($_GET['next'] ? '&next='.urlencode($_GET['next']) : '') : '')).'">'.($F + 1).'</a>');
}function get_file($z, $xb = false)
{
    $rc = $_FILES[$z];
    if (! $rc) {
        return
        null;
    }
    foreach ($rc
    as$z => $X) {
        $rc[$z] = (array) $X;
    }
    $K = '';
    foreach ($rc['error']as$z => $k) {
        if ($k) {
            return$k;
        }
        $E = $rc['name'][$z];
        $Ag = $rc['tmp_name'][$z];
        $fb = file_get_contents($xb && preg_match('~\\.gz$~', $E) ? "compress.zlib://$Ag" : $Ag);
        if ($xb) {
            $Tf = substr($fb, 0, 3);
            if (function_exists('iconv') && preg_match("~^\xFE\xFF|^\xFF\xFE~", $Tf, $mf)) {
                $fb = iconv('utf-16', 'utf-8', $fb);
            } elseif ($Tf == "\xEF\xBB\xBF") {
                $fb = substr($fb, 3);
            }
            $K .= $fb."\n\n";
        } else {
            $K .= $fb;
        }
    }

    return$K;
}function upload_error($k)
{
    $Ed = ($k == UPLOAD_ERR_INI_SIZE ? ini_get('upload_max_filesize') : 0);

    return $k ? 'Unable to upload a file.'.($Ed ? ' '.sprintf('Maximum allowed file size is %sB.', $Ed) : '') : 'File does not exist.';
}function repeat_pattern($Je, $sd)
{
    return
    str_repeat("$Je{0,65535}", $sd / 65535)."$Je{0,".($sd % 65535).'}';
}function is_utf8($X)
{
    return preg_match('~~u', $X) && ! preg_match('~[\\0-\\x8\\xB\\xC\\xE-\\x1F]~', $X);
}function shorten_utf8($Xf, $sd = 80, $bg = '')
{
    if (! preg_match('(^('.repeat_pattern("[\t\r\n -\x{10FFFF}]", $sd).')($)?)u', $Xf, $C)) {
        preg_match('(^('.repeat_pattern("[\t\r\n -~]", $sd).')($)?)', $Xf, $C);
    }

    return
        h($C[1]).$bg.(isset($C[2]) ? '' : '<i>...</i>');
}function format_number($X)
{
    return
    strtr(number_format($X, 0, '.', ','), preg_split('~~u', '0123456789', -1, PREG_SPLIT_NO_EMPTY));
}function friendly_url($X)
{
    return
    preg_replace('~[^a-z0-9_]~i', '-', $X);
}function hidden_fields($Ye, $Rc = [])
{
    $K = false;
    while ([$z, $X] = each($Ye)) {
        if (! in_array($z, $Rc)) {
            if (is_array($X)) {
                foreach ($X
                as$fd => $W) {
                    $Ye[$z."[$fd]"] = $W;
                }
            } else {
                $K = true;
                echo'<input type="hidden" name="'.h($z).'" value="'.h($X).'">';
            }
        }
    }

    return$K;
}function hidden_fields_get()
{
    echo(sid() ? '<input type="hidden" name="'.session_name().'" value="'.h(session_id()).'">' : ''),(SERVER !== null ? '<input type="hidden" name="'.DRIVER.'" value="'.h(SERVER).'">' : ''),'<input type="hidden" name="username" value="'.h($_GET['username']).'">';
}function table_status1($Q, $oc = false)
{
    $K = table_status($Q, $oc);

    return $K ? $K : ['Name' => $Q];
}function column_foreign_keys($Q)
{
    global$b;
    $K = [];
    foreach ($b->foreignKeys($Q)as$n) {
        foreach ($n['source']as$X) {
            $K[$X][] = $n;
        }
    }

    return$K;
}function enum_input($U, $wa, $l, $Y, $Wb = null)
{
    global$b;
    preg_match_all("~'((?:[^']|'')*)'~", $l['length'], $_d);
    $K = ($Wb !== null ? "<label><input type='$U'$wa value='$Wb'".((is_array($Y) ? in_array($Wb, $Y) : $Y === 0) ? ' checked' : '').'><i>'.'empty'.'</i></label>' : '');
    foreach ($_d[1]as$t => $X) {
        $X = stripcslashes(str_replace("''", "'", $X));
        $Na = (is_int($Y) ? $Y == $t + 1 : (is_array($Y) ? in_array($t + 1, $Y) : $Y === $X));
        $K .= " <label><input type='$U'$wa value='".($t + 1)."'".($Na ? ' checked' : '').'>'.h($b->editVal($X, $l)).'</label>';
    }

    return$K;
}function input($l, $Y, $q)
{
    global$Lg,$b,$y;
    $E = h(bracket_escape($l['field']));
    echo"<td class='function'>";
    if (is_array($Y) && ! $q) {
        $ta = [$Y];
        if (version_compare(PHP_VERSION, 5.4) >= 0) {
            $ta[] = JSON_PRETTY_PRINT;
        }
        $Y = call_user_func_array('json_encode', $ta);
        $q = 'json';
    }
    $of = ($y == 'mssql' && $l['auto_increment']);
    if ($of && ! $_POST['save']) {
        $q = null;
    }
    $Bc = (isset($_GET['select']) || $of ? ['orig' => 'original'] : []) + $b->editFunctions($l);
    $wa = " name='fields[$E]'";
    if ($l['type'] == 'enum') {
        echo
        nbsp($Bc['']).'<td>'.$b->editInput($_GET['edit'], $l, $wa, $Y);
    } else {
        $Ic = (in_array($q, $Bc) || isset($Bc[$q]));
        echo(count($Bc) > 1 ? "<select name='function[$E]'>".optionlist($Bc, $q === null || $Ic ? $q : '').'</select>'.on_help("getTarget(event).value.replace(/^SQL\$/, '')", 1).script("qsl('select').onchange = functionChange;", '') : nbsp(reset($Bc))).'<td>';
        $Wc = $b->editInput($_GET['edit'], $l, $wa, $Y);
        if ($Wc != '') {
            echo$Wc;
        } elseif (preg_match('~bool~', $l['type'])) {
            echo"<input type='hidden'$wa value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i', $Y) ? " checked='checked'" : '')."$wa value='1'>";
        } elseif ($l['type'] == 'set') {
            preg_match_all("~'((?:[^']|'')*)'~", $l['length'], $_d);
            foreach ($_d[1]as$t => $X) {
                $X = stripcslashes(str_replace("''", "'", $X));
                $Na = (is_int($Y) ? ($Y >> $t) & 1 : in_array($X, explode(',', $Y), true));
                echo" <label><input type='checkbox' name='fields[$E][$t]' value='".(1 << $t)."'".($Na ? ' checked' : '').'>'.h($b->editVal($X, $l)).'</label>';
            }
        } elseif (preg_match('~blob|bytea|raw|file~', $l['type']) && ini_bool('file_uploads')) {
            echo"<input type='file' name='fields-$E'>";
        } elseif (($rg = preg_match('~text|lob~', $l['type'])) || preg_match("~\n~", $Y)) {
            if ($rg && $y != 'sqlite') {
                $wa .= " cols='50' rows='12'";
            } else {
                $M = min(12, substr_count($Y, "\n") + 1);
                $wa .= " cols='30' rows='$M'".($M == 1 ? " style='height: 1.2em;'" : '');
            }
            echo"<textarea$wa>".h($Y).'</textarea>';
        } elseif ($q == 'json' || preg_match('~^jsonb?$~', $l['type'])) {
            echo"<textarea$wa cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';
        } else {
            $Gd = (! preg_match('~int~', $l['type']) && preg_match('~^(\\d+)(,(\\d+))?$~', $l['length'], $C) ? ((preg_match('~binary~', $l['type']) ? 2 : 1) * $C[1] + ($C[3] ? 1 : 0) + ($C[2] && ! $l['unsigned'] ? 1 : 0)) : ($Lg[$l['type']] ? $Lg[$l['type']] + ($l['unsigned'] ? 0 : 1) : 0));
            if ($y == 'sql' && min_version(5.6) && preg_match('~time~', $l['type'])) {
                $Gd += 7;
            }
            echo'<input'.((! $Ic || $q === '') && preg_match('~(?<!o)int(?!er)~', $l['type']) && ! preg_match('~\[\]~', $l['full_type']) ? " type='number'" : '')." value='".h($Y)."'".($Gd ? " data-maxlength='$Gd'" : '').(preg_match('~char|binary~', $l['type']) && $Gd > 20 ? " size='40'" : '')."$wa>";
        }
        echo$b->editHint($_GET['edit'], $l, $Y);
        $uc = 0;
        foreach ($Bc
        as$z => $X) {
            if ($z === '' || ! $X) {
                break;
            }
            $uc++;
        }
        if ($uc) {
            echo
            script("mixin(qsl('td'), {onchange: partial(skipOriginal, $uc), oninput: function () { this.onchange(); }});");
        }
    }
}function process_input($l)
{
    global$b,$j;
    $Qc = bracket_escape($l['field']);
    $q = $_POST['function'][$Qc];
    $Y = $_POST['fields'][$Qc];
    if ($l['type'] == 'enum') {
        if ($Y == -1) {
            return
            false;
        }
        if ($Y == '') {
            return'NULL';
        }

        return+$Y;
    }
    if ($l['auto_increment'] && $Y == '') {
        return
        null;
    }
    if ($q == 'orig') {
        return $l['on_update'] == 'CURRENT_TIMESTAMP' ? idf_escape($l['field']) : false;
    }
    if ($q == 'NULL') {
        return'NULL';
    }
    if ($l['type'] == 'set') {
        return
        array_sum((array) $Y);
    }
    if ($q == 'json') {
        $q = '';
        $Y = json_decode($Y, true);
        if (! is_array($Y)) {
            return
            false;
        }

        return$Y;
    }
    if (preg_match('~blob|bytea|raw|file~', $l['type']) && ini_bool('file_uploads')) {
        $rc = get_file("fields-$Qc");
        if (! is_string($rc)) {
            return
            false;
        }

        return$j->quoteBinary($rc);
    }

    return$b->processInput($l, $Y, $q);
}function fields_from_edit()
{
    global$j;
    $K = [];
    foreach ((array) $_POST['field_keys']as$z => $X) {
        if ($X != '') {
            $X = bracket_escape($X);
            $_POST['function'][$X] = $_POST['field_funs'][$z];
            $_POST['fields'][$X] = $_POST['field_vals'][$z];
        }
    }
    foreach ((array) $_POST['fields']as$z => $X) {
        $E = bracket_escape($z, 1);
        $K[$E] = ['field' => $E, 'privileges' => ['insert' => 1, 'update' => 1], 'null' => 1, 'auto_increment' => ($z == $j->primary)];
    }

    return$K;
}function search_tables()
{
    global$b,$e;
    $_GET['where'][0]['val'] = $_POST['query'];
    $Ef = "<ul>\n";
    foreach (table_status('', true)as$Q => $R) {
        $E = $b->tableName($R);
        if (isset($R['Engine']) && $E != '' && (! $_POST['tables'] || in_array($Q, $_POST['tables']))) {
            $J = $e->query('SELECT'.limit('1 FROM '.table($Q), ' WHERE '.implode(' AND ', $b->selectSearchProcess(fields($Q), [])), 1));
            if (! $J || $J->fetch_row()) {
                $Ue = "<a href='".h(ME.'select='.urlencode($Q).'&where[0][op]='.urlencode($_GET['where'][0]['op']).'&where[0][val]='.urlencode($_GET['where'][0]['val']))."'>$E</a>";
                echo"$Ef<li>".($J ? $Ue : "<p class='error'>$Ue: ".error())."\n";
                $Ef = '';
            }
        }
    }
    echo($Ef ? "<p class='message'>".'No tables.' : '</ul>')."\n";
}function dump_headers($Pc, $Nd = false)
{
    global$b;
    $K = $b->dumpHeaders($Pc, $Nd);
    $ye = $_POST['output'];
    if ($ye != 'text') {
        header('Content-Disposition: attachment; filename='.$b->dumpFilename($Pc).".$K".($ye != 'file' && ! preg_match('~[^0-9a-z]~', $ye) ? ".$ye" : ''));
    }
    session_write_close();
    ob_flush();
    flush();

    return$K;
}function dump_csv($L)
{
    foreach ($L
    as$z => $X) {
        if (preg_match("~[\"\n,;\t]~", $X) || $X === '') {
            $L[$z] = '"'.str_replace('"', '""', $X).'"';
        }
    }
    echo
    implode(($_POST['format'] == 'csv' ? ',' : ($_POST['format'] == 'tsv' ? "\t" : ';')), $L)."\r\n";
}function apply_sql_function($q, $c)
{
    return $q ? ($q == 'unixepoch' ? "DATETIME($c, '$q')" : ($q == 'count distinct' ? 'COUNT(DISTINCT ' : strtoupper("$q("))."$c)") : $c;
}function get_temp_dir()
{
    $K = ini_get('upload_tmp_dir');
    if (! $K) {
        if (function_exists('sys_get_temp_dir')) {
            $K = sys_get_temp_dir();
        } else {
            $sc = @tempnam('', '');
            if (! $sc) {
                return
                false;
            }
            $K = dirname($sc);
            unlink($sc);
        }
    }

    return$K;
}function file_open_lock($sc)
{
    $p = @fopen($sc, 'r+');
    if (! $p) {
        $p = @fopen($sc, 'w');
        if (! $p) {
            return;
        }
        chmod($sc, 0660);
    }
    flock($p, LOCK_EX);

    return$p;
}function file_write_unlock($p, $rb)
{
    rewind($p);
    fwrite($p, $rb);
    ftruncate($p, strlen($rb));
    flock($p, LOCK_UN);
    fclose($p);
}function password_file($g)
{
    $sc = get_temp_dir().'/adminer.key';
    $K = @file_get_contents($sc);
    if ($K || ! $g) {
        return$K;
    }
    $p = @fopen($sc, 'w');
    if ($p) {
        chmod($sc, 0660);
        $K = rand_string();
        fwrite($p, $K);
        fclose($p);
    }

    return$K;
}function rand_string()
{
    return
    md5(uniqid(mt_rand(), true));
}function select_value($X, $A, $l, $sg)
{
    global$b;
    if (is_array($X)) {
        $K = '';
        foreach ($X
        as$fd => $W) {
            $K .= '<tr>'.($X != array_values($X) ? '<th>'.h($fd) : '').'<td>'.select_value($W, $A, $l, $sg);
        }

        return"<table cellspacing='0'>$K</table>";
    }
    if (! $A) {
        $A = $b->selectLink($X, $l);
    }
    if ($A === null) {
        if (is_mail($X)) {
            $A = "mailto:$X";
        }
        if (is_url($X)) {
            $A = $X;
        }
    }
    $K = $b->editVal($X, $l);
    if ($K !== null) {
        if ($K === '') {
            $K = '&nbsp;';
        } elseif (! is_utf8($K)) {
            $K = "\0";
        } elseif ($sg != '' && is_shortable($l)) {
            $K = shorten_utf8($K, max(0, +$sg));
        } else {
            $K = h($K);
        }
    }

    return$b->selectVal($K, $A, $l, $X);
}function is_mail($Tb)
{
    $va = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
    $Hb = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
    $Je = "$va+(\\.$va+)*@($Hb?\\.)+$Hb";

    return
    is_string($Tb) && preg_match("(^$Je(,\\s*$Je)*\$)i", $Tb);
}function is_url($Xf)
{
    $Hb = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';

    return
    preg_match("~^(https?)://($Hb?\\.)+$Hb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i", $Xf);
}function is_shortable($l)
{
    return
    preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~', $l['type']);
}function count_rows($Q, $Z, $cd, $s)
{
    global$y;
    $I = ' FROM '.table($Q).($Z ? ' WHERE '.implode(' AND ', $Z) : '');

    return $cd && ($y == 'sql' || count($s) == 1) ? 'SELECT COUNT(DISTINCT '.implode(', ', $s).")$I" : 'SELECT COUNT(*)'.($cd ? " FROM (SELECT 1$I GROUP BY ".implode(', ', $s).') x' : $I);
}function slow_query($I)
{
    global$b,$T;
    $i = $b->database();
    $ug = $b->queryTimeout();
    if (support('kill') && is_object($f = connect()) && ($i == '' || $f->select_db($i))) {
        $hd = $f->result(connection_id());
        echo'<script',nonce(),'>
var timeout = setTimeout(function () {
	ajax(\'',js_escape(ME),'script=kill\', function () {
	}, \'kill=',$hd,'&token=',$T,'\');
}, ',1000 * $ug,');
</script>
';
    } else {
        $f = null;
    }
    ob_flush();
    flush();
    $K = @get_key_vals($I, $f, $ug, false);
    if ($f) {
        echo
        script('clearTimeout(timeout);');
        ob_flush();
        flush();
    }

    return$K;
}function get_token()
{
    $ef = rand(1, 1e6);

    return($ef ^ $_SESSION['token']).":$ef";
}function verify_token()
{
    [$T, $ef] = explode(':', $_POST['token']);

    return($ef ^ $_SESSION['token']) == $T;
}function lzw_decompress($Da)
{
    $Db = 256;
    $Ea = 8;
    $Ta = [];
    $pf = 0;
    $qf = 0;
    for ($t = 0; $t < strlen($Da); $t++) {
        $pf = ($pf << 8) + ord($Da[$t]);
        $qf += 8;
        if ($qf >= $Ea) {
            $qf -= $Ea;
            $Ta[] = $pf >> $qf;
            $pf &= (1 << $qf) - 1;
            $Db++;
            if ($Db >> $Ea) {
                $Ea++;
            }
        }
    }
    $Cb = range("\0", "\xFF");
    $K = '';
    foreach ($Ta
    as$t => $Sa) {
        $Sb = $Cb[$Sa];
        if (! isset($Sb)) {
            $Sb = $nh.$nh[0];
        }
        $K .= $Sb;
        if ($t) {
            $Cb[] = $nh.$Sb[0];
        }
        $nh = $Sb;
    }

    return$K;
}function on_help($Za, $Kf = 0)
{
    return
    script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $Za, $Kf) }, onmouseout: helpMouseout});", '');
}function edit_form($a, $m, $L, $Tg)
{
    global$b,$y,$T,$k;
    $gg = $b->tableName(table_status1($a, true));
    page_header(($Tg ? 'Edit' : 'Insert'), $k, ['select' => [$a, $gg]], $gg);
    if ($L === false) {
        echo"<p class='error'>".'No rows.'."\n";
    }
    echo'<form action="" method="post" enctype="multipart/form-data" id="form">
';
    if (! $m) {
        echo"<p class='error'>".'You have no privileges to update this table.'."\n";
    } else {
        echo"<table cellspacing='0'>".script("qsl('table').onkeydown = editingKeydown;");
        foreach ($m
        as$E => $l) {
            echo'<tr><th>'.$b->fieldName($l);
            $yb = $_GET['set'][bracket_escape($E)];
            if ($yb === null) {
                $yb = $l['default'];
                if ($l['type'] == 'bit' && preg_match("~^b'([01]*)'\$~", $yb, $mf)) {
                    $yb = $mf[1];
                }
            }
            $Y = ($L !== null ? ($L[$E] != '' && $y == 'sql' && preg_match('~enum|set~', $l['type']) ? (is_array($L[$E]) ? array_sum($L[$E]) : +$L[$E]) : $L[$E]) : (! $Tg && $l['auto_increment'] ? '' : (isset($_GET['select']) ? false : $yb)));
            if (! $_POST['save'] && is_string($Y)) {
                $Y = $b->editVal($Y, $l);
            }
            $q = ($_POST['save'] ? (string) $_POST['function'][$E] : ($Tg && $l['on_update'] == 'CURRENT_TIMESTAMP' ? 'now' : ($Y === false ? null : ($Y !== null ? '' : 'NULL'))));
            if (preg_match('~time~', $l['type']) && $Y == 'CURRENT_TIMESTAMP') {
                $Y = '';
                $q = 'now';
            }
            input($l, $Y, $q);
            echo"\n";
        }
        if (! support('table')) {
            echo'<tr>'."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select('field_funs[]', $b->editFunctions(['null' => isset($_GET['select'])]))."<td><input name='field_vals[]'>"."\n";
        }
        echo"</table>\n";
    }
    echo"<p>\n";
    if ($m) {
        echo"<input type='submit' value='".'Save'."'>\n";
        if (! isset($_GET['select'])) {
            echo"<input type='submit' name='insert' value='".($Tg ? 'Save and continue edit' : 'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($Tg ? script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."...', this); };") : '');
        }
    }
    echo $Tg ? "<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n" : ($_POST || ! $m ? '' : script("focus(qsa('td', qs('#form'))[1].firstChild);"));
    if (isset($_GET['select'])) {
        hidden_fields(['check' => (array) $_POST['check'], 'clone' => $_POST['clone'], 'all' => $_POST['all']]);
    }
    echo'<input type="hidden" name="referer" value="',h(isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER']),'">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="',$T,'">
</form>
';
} if (isset($_GET['file'])) {
    if ($_SERVER['HTTP_IF_MODIFIED_SINCE']) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
    header('Expires: '.gmdate('D, d M Y H:i:s', time() + 365 * 24 * 60 * 60).' GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: immutable');
    if ($_GET['file'] == 'favicon.ico') {
        header('Content-Type: image/x-icon');
        echo
        lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");
    } elseif ($_GET['file'] == 'default.css') {
        header('Content-Type: text/css; charset=utf-8');
        echo
        lzw_decompress("\n1̇�ٌ�l7��B1�4vb0��fs���n2B�ѱ٘�n:�#(�b.\rDc)��a7E����l�ñ��i1̎s���-4��f�	��i7������Fé�vt2���!�r0���t~�U�'3M��W�B�'c�P�:6T\rc�A�zr_�WK�\r-�VNFS%~�c���&�\\^�r����u�ŎÞ�ً4'7k����Q��h�'g\rFB\ryT7SS�P�1=ǤcI��:�d��m>�S8L�J��t.M���	ϋ`'C����889�� �Q����2�#8А����6m����j��h�<�����9/��:�J�)ʂ�\0d>!\0Z��v�n��o(���k�7��s��>��!�R\"*nS�\0@P\"��(�#[���@g�o���zn�9k�8�n���1�I*��=�n������0�c(�;�à��!���*c��>Ύ�E7D�LJ��1����`�8(��3M��\"�39�?E�e=Ҭ�~������Ӹ7;�C����E\rd!)�a*�5ajo\0�#`�38�\0��]�e���2�	mk��e]���AZs�StZ�Z!)BR�G+�#Jv2(���c�4<�#sB�0���6YL\r�=���[�73��<�:��bx��J=	m_ ���f�l��t��I��H�3�x*���6`t6��%�U�L�eق�<�\0�AQ<P<:�#u/�:T\\>��-�xJ�͍QH\nj�L+j�z��7���`����\nk��'�N�vX>�C-T˩�����4*L�%Cj>7ߨ�ި���`���;y���q�r�3#��} :#n�\r�^�=C�Aܸ�Ǝ�s&8��K&��*0��t�S���=�[��:�\\]�E݌�/O�>^]�ø�<����gZ�V��q����� ��x\\������޺��\"J�\\î��##���D��x6��5x�������\rH�l ����b��r�7��6���j|����ۖ*�FAquvyO��WeM����D.F��:R�\$-����T!�DS`�8D�~��A`(�em�����T@O1@��X��\nLp�P�����m�yf��)	���GSEI���xC(s(a�?\$`tE�n��,�� \$a��U>,�В\$Z�kDm,G\0��\\��i��%ʹ� n��������g���b	y`��Ԇ�W� 䗗�_C��T\ni��H%�da��i�7�At�,��J�X4n����0o͹�9g\nzm�M%`�'I���О-���7:p�3p��Q�rED������b2]�PF����>e���3j\n�߰t!�?4f�tK;��\rΞи�!�o�u�?���Ph���0uIC}'~��2�v�Q���8)���7�DI�=��y&��ea�s*hɕjlA�(�\"�\\��m^i��M)��^�	|~�l��#!Y�f81RS����!���62P�C��l&���xd!�|��9�`�_OY�=��G�[E�-eL�CvT� )�@�j-5���pSg�.�G=���ZE��\$\0�цKj�U��\$���G'I�P��~�ځ� ;��hNێG%*�Rj�X[�XPf^��|��T!�*N��І�\rU��^q1V!��Uz,�I|7�7�r,���7���ľB���;�+���ߕ�A�p����^���~ؼW!3P�I8]��v�J��f�q�|,���9W�f`\0�q�Z�p}[Jdhy��N�Y|�Cy,�<s A�{e�Q���hd���Ǉ �B4;ks&�������a�������;˹}�S��J���)�=d��|���Nd��I�*8���dl�ѓ�E6~Ϩ�F����X`�M\rʞ/�%B/V�I�N&;���0�UC cT&.E+��������@�0`;���G�5��ަj'������Ɛ�Y�+��QZ-i���yv��I�5��,O|�P�]Fۏ�����\0���2�49͢���n/χ]س&��I^�=�l��qfI��= �]x1GR�&�e�7��)��'��:B�B�>a�z�-���2.����bz���#�����Uᓍ�L7-�w�t�3ɵ��e���D��\$�#���j�@�G�8� �7p���R�YC��~��:�@��EU�J��;67v]�J'���q1ϳ�El�QІi�����/��{k<��֡M�po�}��r��q�؞�c�ä�_m�w��^�u������������ln���	��_�~�G�n����{kܞ�w���\rj~�K�\0�����-����B�;����b`}�CC,���-��L��8\r,��kl�ǌ�n}-5����3u�gm��Ÿ�*�/������׏�`�`�#x�+B?#�ۏN;OR\r����\$�����k��ϙ\01\0k�\0�8��a��/t���#(&�l&���p��삅���i�M�{�zp*�-g���v��6�k�	���d�؋����A`6�lX)+d ��7 �\r�� �ځcj6��\rp�\r��\r\"oP�7�\r��\0�\0�y��P���\rQ7���Z��4Q���ڍp/�y\r��##D�;����<�g�\0fi2�)f�\\	m�Gh\r�#�n����@[ �G�\"Sqm��\r���#�(Aj��qѣ%���̑3qE��\0r�����0��я����.��Q7шW���u����� �@�H��q'vs�0�\n�+0����SG�p�O`�\r)c�#�����R=\$�ƐR\r�Gы\$R?%2C�[\0؍�~�!�\\��p�#@���O(rg%�?ra\$��)r](��&�?&�#&R�',\rqV3�\"H�m+���l�Q\"\0�4��\$r�,�=����&2;.�H@`���a����\$�_*RIS&��q��_�1�1+1������3)2�V7��2l�ڄ!1g-�2f`���,Q�7��0qg�]!q��m6����_�M7 ���7�o6Q����kp�3�g9��s� 3�6�\r�:S�9ӏ;� �\r9�-\0�Yӧ0Q�<b#<Ӂ�w/�G��>r�\r��=3��^&Q;ѣ?q�0\"�0HЙ�|���ʖS��i��@*�T�2�T#�� �\0�C��07]?��&���E��D�;:/�3�E�5��EQ�e��T\"�m����5�E;��#=4�8��*���LS�5Hr�JE TO\rԅJ��J��J���eG)8B�8�,&�G����	��+M���ɲ��^*���G��14�6�\$.\"拢�I4w!\$L �8b�A2�L�'M?MF�\$�,����Nr��/4�BJ�¨");
    } elseif ($_GET['file'] == 'functions.js') {
        header('Content-Type: text/javascript; charset=utf-8');
        echo
        lzw_decompress("f:��gCI��\n8��3)��7���81��x:\nOg#)��r7\n\"��`�|2�gSi�H)N�S��\r��\"0��@�)�`(\$s6O!��V/=��' T4�=��iS��6IO��er�x�9�*ź��n3�\rщv�C��`���2G%�Y�����1��f���Ȃl��1�\ny�*pC\r\$�n�T��3=\\�r9O\"�	��l<�\r�\\��I,�s\nA��eh+M�!�q0��f�`(�N{c��+w���Y��p٧3�3��+I��j�����k��n�q���zi#^r�����3���[��o;��(��6�#�Ґ��\":cz>ߣC2v�CX�<�P��c*5\n���/�P97�|F��c0�����!���!���!��\nZ%�ć#CH�!��r8�\$���,�Rܔ2���^0��@�2��(�88P/��݄�\\�\$La\\�;c�H��HX���\nʃt���8A<�sZ�*�;I��3��@�2<���!A8G<�j�-K�({*\r��a1���N4Tc\"\\�!=1^���M9O�:�;j��\r�X��L#H�7�#Tݪ/-���p�;�B \n�2!���t]apΎ��\0R�C�v�M�I,\r���\0Hv��?kT�4����uٱ�;&���+&���\r�X���bu4ݡi88�2B�/⃖4���N8A�A)52������2��s�8�5���p�WC@�:�t�㾴�e��h\"#8_��cp^��I]OH��:zd�3g�(���Ök��\\6����2�ږ��i��7���]\r�xO�n�p�<��p�Q�U�n��|@���#G3��8bA��6�2�67%#�\\8\r��2�c\r�ݟk��.(�	��-�J;��� ��L�� ���W��㧓ѥɤ����n��ҧ���M��9ZНs]�z����y^[��4-�U\0ta��62^��.`���.C�j�[ᄠ% Q\0`d�M8�����\$O0`4���\n\0a\rA�<�@����\r!�:�BA�9�?h>�Ǻ��~̌�6Ȉh�=�-�A7X��և\\�\r��Q<蚧q�'!XΓ2�T �!�D\r��,K�\"�%�H�qR\r�̠��C =�������<c�\n#<�5�M� �E��y�������o\"�cJKL2�&��eR��W�AΐTw�ё;�J���\\`)5��ޜB�qhT3��R	�'\r+\":�8��tV�A�+]��S72��Y�F��Z85�c,���J��/+S�nBpoW�d��\"�Q��a�ZKp�ާy\$�����4�I�@L'@�xC�df�~}Q*�ҺA��Q�\"B�*2\0�.��kF�\"\r��� �o�\\�Ԣ���VijY��M��O�\$��2�ThH����0XH�5~kL���T*:~P��2�t���B\0�Y������j�vD�s.�9�s��̤�P�*x���b�o����P�\$�W/�*��z';��\$�*����d�m�Ã�'b\r�n%��47W�-�������K���@<�g�èbB��[7�\\�|�VdR��6leQ�`(Ԣ,�d��8\r�]S:?�1�`��Y�`�A�ғ%��ZkQ�sM�*���{`�J*�w��ӊ>�վ�D���>�eӾ�\"�t+po����=�*��Apc7g��]��l�!׎ї+��zsN�����P����ia�y}U�a����`��A�����w\n�����j��<�:+�7;\"��N3tqd4źg���T�x��PH��FvW�V\n�h;��B�D�س/�bJ��\\�+ %�����]��ъ��wa�ݫ��������E��(i�!��7��x��z������Hɳ�d��md���Q�r@�a��ja?�\r�\ry�4-4�fP�҉W��`,�x@���x���A���K.�O�i��o�;���)�Ш��ɆS�d��eO��%�N��L78��F㪛�S�����I��\r��Z��r^�>����*�d\ri�Y��Yd�u��s�*�	��E��ʽ�D�9��!�>�kCရA���d���!WW�1����QA��k��d%��# �y��{��`}T�_YY�R��-�M��O�2���,�,Š�`�-2����+]L��7E���{`��˕��~w�-�����M6����]F����@���e`�/�8�@�e���\\ap.�H����C���*EAoz2���g0��?]͝~�s���`�hJ`��箤`�}���^`���>��O�5\r�W^I����\n����;��:���_h�n�����YP4���)��*��������6v��[ˤ�C;������n�W/j�<\$J*qĢ���-L�\0�����\0O�\$�ZW z�	\0}��.4F�\rnu\0����䏋��L���IA\nz��*����jJ�̅P�����p���6�ئN��D�Bf\\	\0�	��W@L\r��`�g'Bd�	Bi	�����*|r%|\nr\r#���@w���(T.�v�8���\nm���<p��`�Y0�������\0�#���}�.I �x�T\\���\n��Q��@bR MF��|��%0SDr�����f/b����:��k/��	f%�Ш�e\nx\0�l\0���	�0�W`���\n�8\r\0}p�����;\0�.B��V��,z&�f �\r��WOcK�\n������k�z2\r����W@�%\n~1��X���q�D�!�^��t<�\$�{0<E��Ѫ�2&�N�\r\n�^i���\"�#n� �#2D������D��o!�zK6��:������#Rl�%q'k޾*��À� Z@��J�`^P�H�bSR|�	�%|���.��µ�^�rc&o��k<����&��xK��'��LĂ��(��mE)�*���`R�bWGbTR���`VNf��j���woV��(\"��ڧ�&s\0��.��޳8��=h�� Q&���n*h�\0�v�B�G��@\\F\n�W�r f\$�e6��6�a㤥�5H����bY�f��RF��9�(Һ�.EQ�*���(�1�*�/+,�\"��\r��	��8�\0��3@�%l厭�,+���&�#-\$���%���gF!s�1��%��s�/�nK�q�\0O\"EA�8�2��}5\0�8��A\n��RrH����9�4U�dW3!b�z`�>�F>�i,�a?L>��`�r��r�ta;L���%�RxR��t�ʥHW/m7Dr�EsG2�.B5I���Q3�_��Ԉ봤�24.��Rk��z@��@�N�[4�&<%b>n�YPW���6n\$bK5�t��ZB�YI L�~G�Y���cQc	6DXֵ\"}ƞf�ТI�j��5�\\� X٢td��\nbtNaE�Tb;�l�p��|�\0��x\n���dV����]X��Yf��%D`�Qb�svDsk0�qT��7�l�c7������SZ�6��㵊Ğ#�x��h ՚���`�_`ܾ�ڧ������+w`�%U�����虯��̻U���D�Xl#��Ju�[ �Q'�\\H������GR��0�oa����C�X�+�a�c�N䞮`�re�\n��%�4�S�_�k_�ښ�!3({7�bI\rV\r�5��\0�\\��aeSg[�z�f-P�O,ju;XUv������m�l�\"\\B1Ğ��0� ��p��4��;2*��.b�\0��u��J\"NV��rrO�f�2�W3[�آ���	���5\r7��0,yt��wS	W	]kG�X�iA*=P\rbs\"�\\�o{e��5k��k�<��;�;x��-�0��_\$4� ����8*i\0f�.�(`����D`�P�&�����A+eB\"Z�����W̢\\M>�w�����g0��G���������\r�ܩ*�f\\�p\0��Kf#���˃\r��͡��@\r���d���\n�&D�%���3��w���.}�����ŭ� �kH�k1x~]�P٭Ӄ�[��;��Y���ؑK�6 �Z���t��>gL\r��HsM�e�\0���&3�\$�n3�� wʓ7՗��\"���+��;�s;���*1� y*���;TG|�|B�!�{!��\"/ʖo��j�W��+���L�DJ��ͅ�w2��VTZ�Gg/��֊�]4n�4��������i�=�T��]d�&���M\0�[88�ȮE��8&LXVm�v��	ʔj�כ�F��\\��	���&t\0Q��\\\"�b��	��\rBs�	w��	����N �7�C/|��	��\n\nN��K�y�*A�`�W�YvUZ4tz;~0}��J?hW�d*#�3��О�yF\nKT���@|�gy�\0�O�x�a�`w�Z9��bO����WY�R��}J��X��P�U2`���G��beu��zW�+���\r�\$4���\"\n\0�\n`�X@N���%d|�h�����Ňeg��+�H�t�(���( �^\0Zk@��P�@%�(W�{��/���t{o\$�\0[������%���ə��hU]�B,�rD��e:D���X��V&�Wll@�d��Y4�˯�iYy��[���+�Z��]�g��Fr�F���w���#1�tϦ��N�hq`�D����v|��Z�L�v�:S���@�e���B��.2��E�%گB�@[����B�*Y;��[�#������@:5�`Y8۾��&��	@�	���Q�S8!����� ���2MY���O;����ƛ�)��F¨FZ�A\\1�PF�B�lF+���<�R�><J?��{�f��kĘ8��W���뮺M\r���ۖRsC�N����%��J�~��?���,\r4�k0�,J󪎕b���o\0�!1 �5'�\r���u\r\0��\$��=�}\r7N��=DW6K�8v��\r� �\n��	*�\r��7)��D�m�1	a�@�և��w.�T����~���pV���J�u�\r�&N Mqc�d��d�8����_�K�aU&�H#]�d}`P�\0~�U/�����ynY<>dC�<G�@��\"�eZS�w㕛��Gy�\\j)�}���\r5�1,p�^u\0����n��C��HP֬G<��p��2�\n�FD�\r�\$��y�uyc���v�6�e)�p�YH�Ē��#VP�����eW��=�m��c:&���-��Pv.��ˀ�杍���	��أ\0\$��@+���P�l&_�Cb-U&�0\"�F��Vy��p\r�a5�q9U>�5�\\LBg��U�[�7m d��yV[5�*}�4�5/��ҏ�H�D60 ���쐿��:Suy\r����SM���;W���εL4�G�N������� �e�m��t��sq���\".F�����CsQ� h�e7��n�>��*�c!iS�j��ّ̭�������{����%t��\0`&lrœ,�!0ahy	R�B=�egW��o\0�H�h/v(�N4�\r���Tz�&q�?X\$�X!�J^�,���b���`2@:��7�CX�H�e��@q��\ny��0��������P�O02@�v�/IPa�2��0\n]-(^��t.��3&�\"�0��\"�\0]�1���a��E�S��P|\\���A�p�9��\$K���Byuد�z�7Z�\r�b�u�_��8��m�q����E<-��@\0�!)�� )�)�~Q�	rّ�/M�P�\n�	��`�!\n(��\n\n>X��!` W�������p4A�	Ŷ��d��\0X�٧V\n�+Cd/E�F��m+`\0�2��p/-��2���e��C@C�\0pX,4�쪼��9���Xt!.P��\\���q��b{�v�bfM��)D]�w�������X�B4'��f�tXЦ�(O վ�	�q�#��3��p]�i\".��7�iw[T\0y\r�4C�;,\$a2i(�\$�mȆD�&Ԕ4��Z �;E#6UA�R����eFF��U�1�h2\n��UpևÞ�Tʹ�����[�+�^�Xդ�78 A\rnK��d1�>�p�+�`�:���I�o<�L�@�a	���\0:���G�� hQ�\$�jR��'�Ȍ�K!�`���1���H�C��Z0\$�e�yXG�5h�E�\r1�G�\n�`�g'\0��6qV�(\r��VPH�ǌ�b֊\r�-k�\0B�b���G�:��Z��|�>�*�XX�!����\"&��:E�a��,vB�P��h!pf;\0��[��/r:qT����8\"x3Gl��\"Xm#�`�5���x\n��G�;��EQ�X�ǂ<HhA����+1Ns������k�jsH{�����&1�G�aI�?76�22�p4���șV!������2͟:��z	�I�ĉZ�1ER7��%�����6��!�?@(����,&�2����>�I8 �P+���h�&7N'2V��\0��i\0���ܙi%8��V8e�Z:�@ʴ��6�R{�Jz�s2�	j(C`Z*�J-b��#�DEu\$�W�*��*#9���D3y��?\"�9�,Q�/��w8��U�=�q��]\0�ʹ�m�t��-*�(��d҉�!��+F�X\$I�̄�����U\$�`���e�'c��Vr��n��1l���5�?XT�&*@���IB�tyt��f��N��%��S�H�x�\$�\0}/sH\\������6@y1�\0~@+�V�7U�Lh`_C�����hBA|��*pE��	�\"։0\0�0\$R���p\0���[��g�fb�r����\0P��,�\0�tc����|d	��,F���0�6+�U������[	ZL���R�%�j���4�I���#x���W�v����6M�\"�m�P�U7P6;n /	t�R�Ap��<R3NX�\0���S|1K�@0<̈́S	O+��J�7`1��oS`�8�	�e����X��7Q���s�*��@W2�M�ZaǼK����E@�\r��Ŧl���X(/�j�0�Y�<W�7Z�Ǉ|�&H|�م��%T�sFGNq<I�������7&-�z�V��[��w�1\\���S�\r��:����S-՟}�2䃊>��9h�`,=��RȰ��Je4Kp�EE��}H���a@�&;����{.	���!���I��0c��f�:\r�PwN�u���W���+�����M\0007�|!���Yh��W�\$�i�;I�aL���\$S ,�S.Se�@N0y*ۦ&���D\0dɤOE�1Eu��q�2J}E��+ �DZ��E��+a[O;�(ćEdm}\0e�\0��4\r��˅+���_��P�l�u��ɱ�Q�Q	�\$��1��!\\��\n�1O)6]u�&�K' ��G�=�t�LD�׍?H�Қ���H�(�HJTRLa��e �B�ހ[dн�\nR�=�BSgF��nʘ\0���0e�c&�@�Ж����1�\0\0�O��)�>z�&0��M����ZJj�ě�%�!�z�\0�8��AP��P�y��FcDJ�щ6��-��������RY&��~��\$�	���C�4��c#;�ȚAbݭ#C�hBBtO�h;��p�l���u�\nY	�'������\03�\0�	�IX@� \"��\0P�Z4�T�WUC�,��􀰩�(	��	M�,�����P`I���h���/Q�\0�������@)\nFH�����������Qo�@>S�C@p�H���V@Bn�	a1�ĞE�*�5a�H7dP\n�B� JD��J�����&��{���A�'�h5�-�@t���)dJu�����JqU��Q��%N�S�(&�.�R�T����e�r=\\Sވ�√����hn��N���Y\"�\n\nJx�G\r\0�r5��T@�[`����Z\r�Ip%|�A*9w\"�+���2c��l�9#\$�@a���*�T�@\0+��+=a9�C�I���Y~#�!�B�?��A�\n��E!�kC�-�d�fk��^\0�U�k5�:��pǜ����(8���v�5���*�8��Ă ����c�+�W�Z�FP�BWS)�@�=��S���;r@@Ȑ1�78�E�X��0�~�cӱ�z)f���\$d6ma���]g���kAp��M�g�~��!�c<P\"�X��Z��������hk4�d�K��	\0b���MHY4��T��/�M��J��u۔���)\n�I�?v�	�i��F�Q�m�\$�(�w7-�x �+�t]xlugA�F�/s��=d2�n=��<��?e��2��\\� ��V.ِ.��,}�?K����0O��������k2)E0ȹI���O�z_���6CB�/����.ܨ��*1�Խ�H����Z�Z8\0��;%�DLCW00\0��u��G..��D�>�(�P��֮�\\ܞ\n�-��,/rz��<]i������aR���E�S�=B��X�t[�fj��\0�(��%�c�2��#���℡{��D��wh�.��)���&C0b���Z�+<�FN_���a!�,!\r�m���]j��O��Bi`0:�3�MO(�\\�����hrF9�����,�59��u�I����n�h^R0(��=�������'5y�ڔ�������ұR��\n2�]���%Ek_\n���4!T98Y#��l�\\ʯ�Q�E4��\r-<��_�}�>�����騖I�T�!��Z�e=��NX?�-��T��\"���F0J�#�?���*�A�Ԋ���a�j�i,z,|/���`(?�v���g�~�bO�����8N�R&�գ���6�6�<_���}>��0JS�O�k	o��C;�Ԩ��,���O%U��!�h|BQ!�ӊ�Y���M\0v[��q���	x3�E���~�N඼7��KP/z�vR<��\rL�d�B!5��H��p/����-�<,�A�p��c��Ltń�u��[浀�61���CQ�`�6ϴ�,�kמ� ְ��E��g-���@^<�J�D@���>���ie�CA�&�DpN,���v�`�?0�����=���I{�T�e�o_m_K��Ž�H�'=@�\$��7p��a\$js��\r��Ãհ�Q�;�c�pn����K<ia�ܠ:�f4b%����!O�N5�_zc��g�f9�d�F�ePA�ta��k6���<aջI�����\nJ�eoKϥ�\0006�ٱj��C]�,e���I���Q�b�VI���; �@'�mE\\Gv�*`|�kYgO���J�10�y��<�O�~NB\\�K�w)M�����(ܨ�*j%i��el6\n��xlXlOLAl���WA �]i#�pP;&`[H�𭄃�\0�l1`����O�v�9%r�\\�KQ[\0�Xb^�LO��4�	8�7�AB��PF����������L���*!��R�1E��e�O�4ƅ�H�K�2�D9fS��:n��<��Π�>�%����7VTr�]��)-�C��J!�U���✋������%�0�cs��fV=GͰ��a�Ƒ:�%C�������)S5`[��;G����<y�km�_c\\�g�G,gcJ9�ab��^�\\Yj�~�݋v�0�楀L੗��fw(���i[����'c��;���5)ey�\nb�q���D��\"��1hsG��ih�8��*Y��-�\$��th��B��X*��Щ߬v�QY�*�~�������X8 ,��� �9bd�\\O�L.|)�1\0�L���P ������znG�qv�L^�\\\\C��~��~=YK���z|��!��z�����I���L������M�m��wv�{&��X�)ťr=i�r�&�P�^����f��W�O��5j�(N�f�E�P�©���/U�A�8:��Ư���WX\nep���x�\0�z835��)tfS�T�c2�Vx�X�^K�fF�}k@�(|��l��yZŴ��_G�=��_�˟�����\n�H�G\r{�h��()Hs��f2�e>�A�%��>�\r]��e��H Nk�*��\n����!N��/p��R�\$�FO�D���_9�|=��H�z��{2�f�P^��~���'6�=6̈��F5h�Dv`.�Ds�fQw��i�)��I)�k&V�\0��&^P��i\"�0SL]\\��(�<�^��z����a`�~,,���L�Y:�>%\$:%ku��B���j.ɂ�e��f���[!c�/�pk���Et�5䀖h�v?�]��kDZ�\\}vNu�P|u\r!�r�-��攣��q���kH�#P:D�m�@�Uve�0�8��i�oR�ES�rA)(��۳!9�Ht�E��]m��IU��~�T����~���k�bՙX\0����RI*�����B��{����Ě-8�}4�]���7����ޡ#�Ҿ��w��=��Sj�ܨN��s�\rmo����ߦ��NLo����K�S�����\0;��\"VdlX�`9�zڑ�縘=���}U��Ҕ�~�E�I�1��w���foj\$[c��ل};�tŮ@HX�e\"��!Ws++��X`=�\n���h��cۨ�����DGq�k�󗂺�)�Ŷx�Wb�����B+�tuVJ��ĂΉ��T{�p�0VG���˲o��	x��\"�+�D|�S��B��Б�/g�J�,�oQ�-A�~SK_��ǔ䘡�t�[Q/̍L�������I�wÌD�'�X��y��E��䆾^��sz\r��]��3�P~�ֲ��H�++�@��B�w���wƒ��T*`;��y��S��&K������se+����e�F:qO\r\\UDB!�\"~�h`2����,xm����G�Kb�e�ؠ%�)ixJ?��#���wI�����rm��Df�5�\0006���[���2鹅>P���Mwa��F��Q�8o��6���I �`�5��@'���q˞�A6�ꨵ��S|������W)�4SU`1��~�J\$�֫\r�����BlB�m�*�O��`��]�P6��h U�\0�@\"��	�FERǒ4L\0�5�r�GL8\n4�*��� ��N(@0�˳�hq��N�to����X�L	�Fmb��{�����Z-0�g�	�F���ѹ~��=�{	�� s�ʏ��q-�{�Y��7-��,�\0��z��6@t��R{�����F�o9���r�/�7/�7�\0�3�B��ڃ~�E)�bq-y fT(��\"u��Q�{�Z)CHS_�Jp*;Q\\���U��d��Y��u��6��xhw>Q�����x�����-Z��iCT����B�ʂ@�8�����S���\n�'I������T�J0�^�ӭ��:x�� h����j����B?������Oï�����7���g��.��9�-y��dU� [�?*��ޞPo����3�8�����;@����6�JL��o6������e�t+����.��P5���N��G��y�\rL���^@����֗�C����S֒�z֏��_�F\"6\rg����`-�ד={����%�O���߬V��Mh���Q1�3h���a�	��'��b�C��H|�\$��*�>���<��m�}'�FH\n��/L\0��8�UϹ���A��C�\"�����:w��4�.H\\�Ύ��޾s�GÖW�ʑ����P�C�0���ʋ����r�{��0����Ӷ�-����a�t�r�����6�u:�|[�t�`%<3+q��B��\"��	�S\0+��>!���`�LJ��o�_���	���T���J�o�xnl�)���~��\r�� N�u� ���ؘ8]��{�8-N�SS�߆?	�Q���Z�Yv�62���J0���_ƀ�o%������@!�pH����A�h��&I�˃\0!�Q�Zy�r�\n�\$�7��#@�l���hw��@����Ds%�k��i���4	�]\nk@���́<H΍Jy�˧��]ט���h\"���G�.@��݀#�5PnD��S��2��{�G�2{�*!���؀�\0��#�W �������9�ӆ���_��\0s	 \0t��A���?Q��������0��q�6�=�\0Nk��\08W��0�^�@��:5�\0�i�	6zO\n���*��ҔQO刊.��o�	r���K ���~cY����4��+�F5�!���C���i*c?�33�!C:��\\NE\0�");
    } elseif ($_GET['file'] == 'jush.js') {
        header('Content-Type: text/javascript; charset=utf-8');
        echo
        lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0��\0�Dbc�!�Q7Шd8���~��N)�Eг`�Ns��`�S)�O���/�<�x�9�o�����3n��2�!r�:;�+�9�CȨ���\n<�`��b�\\�?�`�4\r#`�<�Be�B#�N ��\r.D`��j�4���p�ar��㢺�>�8�\$�c��1�c���c����{n7����A�N�RLi\r1���!�(�j´�+��62�X�8+����.\r����!x���h�'��6S�\0R����O�\n��1(W0���7q��:N�E:68n+��մ5_(�s�\r��/m�6P�@�EQ���9\n�V-���\"�.:�J��8we�q�|؇�X�]��Y X�e�zW�� �7��Z1��hQf��u�j�4Z{p\\AU�J<��k��@�ɍ��@�}&���L7U�wuYh��2��@�u� P�7�A�h����3Û��XEͅZ�]�l�@Mplv�)� ��HW���y>�Y�-�Y��/�������hC�[*��F�#~�!�`�\r#0P�C˝�f������\\���^�%B<�\\�f�ޱ�����&/�O��L\\jF��jZ�1�\\:ƴ>�N��XaF�A�������f�h{\"s\n�64������?�8�^p�\"띰�ȸ\\�e(�P�N��q[g��r�&�}Ph���W��*��r_s�P�h���\n���om������#���.�\0@�pdW �\$Һ�Q۽Tl0� ��HdH�)��ۏ��)P���H�g��U����B�e\r�t:��\0)\"�t�,�����[�(D�O\nR8!�Ƭ֚��lA�V��4�h��Sq<��@}���gK�]���]�=90��'����wA<����a�~��W��D|A���2�X�U2��yŊ��=�p)�\0P	�s��n�3�r�f\0�F���v��G��I@�%���+��_I`����\r.��N���KI�[�ʖSJ���aUf�Sz���M��%��\"Q|9��Bc�a�q\0�8�#�<a��:z1Uf��>�Z�l������e5#U@iUG��n�%Ұs���;gxL�pP�?B��Q�\\�b��龒Q�=7�:��ݡQ�\r:�t�:y(� �\n�d)���\n�X;����CaA�\r���P�GH�!���@�9\n\nAl~H���V\ns��ի�Ư�bBr���������3�\r�P�%�ф\r}b/�Α\$�5�P�C�\"w�B_��U�gAt��夅�^Q��U���j����Bvh졄4�)��+�)<�j^�<L��4U*���Bg�����*n�ʖ�-����	9O\$��طzyM�3�\\9���.o�����E(i������7	tߚ�-&�\nj!\r��y�y�D1g���]��yR�7\"������~����)TZ0E9M�YZtXe!�f�@�{Ȭyl	8�;���R{��8�Į�e�+UL�'�F�1���8PE5-	�_!�7��[2�J��;�HR��ǹ�8p痲݇@��0,ծpsK0\r�4��\$sJ���4�DZ��I��'\$cL�R��MpY&����i�z3G�zҚJ%��P�-��[�/x�T�{p��z�C�v���:�V'�\\��KJa��M�&���Ӿ\"�e�o^Q+h^��iT��1�OR�l�,5[ݘ\$��)��jLƁU`�S�`Z^�|��r�=��n登��TU	1Hyk��t+\0v�D�\r	<��ƙ��jG���t�*3%k�YܲT*�|\"C��lhE�(�\r�8r��{��0����D�_��.6и�;����rBj�O'ۜ���>\$��`^6��9�#����4X��mh8:��c��0��;�/ԉ����;�\\'(��t�'+�����̷�^�]��N�v��#�,�v���O�i�ϖ�>��<S�A\\�\\��!�3*tl`�u�\0p'�7�P�9�bs�{�v�{��7�\"{��r�a�(�^��E����g��/���U�9g���/��`�\nL\n�)���(A�a�\" ���	�&�P��@O\n師0�(M&�FJ'�! �0�<�H�������*�|��*�OZ�m*n/b�/�������.��o\0��dn�)����i�:R���P2�m�\0/v�OX���Fʳψ���\"�����0�0�����0b��gj��\$�n�0}�	�@�=MƂ0n�P�/p�ot������.�̽�g\0�)o�\n0���\rF����b�i��o}\n�̯�	NQ�'�x�Fa�J���L������\r��\r����0��'��d	oep��4D��ʐ�q(~�� �\r�E��pr�QVFH�l��Kj���N&�j!�H`�_bh\r1���n!�Ɏ�z�����\\��\r���`V_k��\"\\ׂ'V��\0ʾ`AC������V�`\r%�����\r����k@N����B�횙� �!�\n�\0Z�6�\$d��,%�%la�H�\n�#�S\$!\$@��2���I\$r�{!��J�2H�ZM\\��hb,�'||cj~g�r�`�ļ�\$���+�A1�E���� <�L��\$�Y%-FD��d�L焳��\n@�bVf�;2_(��L�п��<%@ڜ,\"�d��N�er�\0�`��Z��4�'ld9-�#`��Ŗ����j6�ƣ�v���N�͐f��@܆�&�B\$�(�Z&���278I ��P\rk\\���2`�\rdLb@E��2`P( B'�����0�&��{���:��dB�1�^؉*\r\0c<K�|�5sZ�`���O3�5=@�5�C>@�W*	=\0N<g�6s67Sm7u?	{<&L�.3~D��\rŚ�x��),r�in�/��O\0o{0k�]3>m��1\0�I@�9T34+ԙ@e�GFMC�\rE3�Etm!�#1�D @�H(��n ��<g,V`R]@����3Cr7s~�GI�i@\0v��5\rV�'������P��\r�\$<b�%(�Dd��PW����b�fO �x\0�} ��lb�&�vj4�LS��ִԶ5&dsF M�4��\".H�M0�1uL�\"��/J`�{�����xǐYu*\"U.I53Q�3Q��J��g��5�s���&jь��u�٭ЪGQMTmGB�tl-c�*��\r��Z7���*hs/RUV����B�Nˈ�����Ԋ�i�Lk�.���t�龩�rYi���-S��3�\\�T�OM^�G>�ZQj���\"���i��MsS�S\$Ib	f���u����:�SB|i��Y¦��8	v�#�D�4`��.��^�H�M�_ռ�u��U�z`Z�J	e��@Ce��a�\"m�b�6ԯJR���T�?ԣXMZ��І��p����Qv�j�jV�{���C�\r��7�Tʞ� ��5{P��]�\r�?Q�AA������2񾠓V)Ji��-N99f�l Jm��;u�@�<F�Ѡ�e�j��Ħ�I�<+CW@�����Z�l�1�<2�iF�7`KG�~L&+N��YtWH飑w	����l��s'g��q+L�zbiz���Ţ�.Њ�zW�� �zd�W����(�y)v�E4,\0�\"d��\$B�{��!)1U�5bp#�}m=��@�w�	P\0�\r�����`O|���	�ɍ����Y��JՂ�E��Ou�_�\n`F`�}M�.#1��f�*�ա��  �z�uc���� xf�8kZR�s2ʂ-���Z2�+�ʷ�(�sU�cD�ѷ���X!��u�&-vP�ر\0'L�X �L����o	��>�Վ�\r@�P�\rxF��E��ȭ�%����=5N֜��?�7�N�Å�w�`�hX�98 �����q��z��d%6̂t�/������L��l��,�Ka�N~�����,�'�ǀM\rf9�w��!x��x[�ϑ�G�8;�xA��-I�&5\$�D\$���%��xѬ���´���]����&o�-3�9�L��z���y6�;u�zZ ��8�_�ɐx\0D?�X7����y�OY.#3�8��ǀ�e�Q�=؀*��G�wm ���Y�����]YOY�F���)�z#\$e��)�/�z?�z;����^��F�Zg�����������`^�e����#�������?��e��M��3u�偃0�>�\"?��@חXv�\"������*Ԣ\r6v~��OV~�&ר�^g���đٞ�'��f6:-Z~��O6;zx��;&!�+{9M�ٳd� \r,9����W��ݭ:�\r�ٜ��@睂+��]��-�[g��ۇ[s�[i��i�q��y��x�+�|7�{7�|w�}����E��W��Wk�|J؁��xm��q xwyj���#��e��(�������ߞþ��� {��ڏ�y���M���@��ɂ��Y�(g͚-����������J(���@�;�y�#S���Y��p@�%�s��o�9;�������+��	�;����ZNٯº��� k�V��u�[�x��|q��ON?���	�`u��6�|�|X����س|O�x!�:���ϗY]�����c���\r�h�9n�������8'������\rS.1��USȸ��X��+��z]ɵ��?����C�\r��\\����\$�`��)U�|ˤ|Ѩx'՜����<�̙e�|�ͳ����L���M�y�(ۧ�l�к�O]{Ѿ�FD���}�yu��Ē�,XL\\�x��;U��Wt�v��\\OxWJ9Ȓ�R5�WiMi[�K��f(\0�dĚ�迩�\r�M����7�;��������6�KʦI�\r���xv\r�V3���ɱ.��R������|��^2�^0߾\$�Q��[�D��ܣ�>1'^X~t�1\"6L���+��A��e�����I��~����@����pM>�m<��SK��-H���T76�SMfg�=��GPʰ�P�\r��>�����2Sb\$�C[���(�)��%Q#G`u��Gwp\rk�Ke�zhj��zi(��rO�������T=�7���~�4\"ef�~�d���V�Z���U�-�b'V�J�Z7���)T��8.<�RM�\$�����'�by�\n5����_��w����U�`ei޿J�b�g�u�S��?��`���+��� M�g�7`���\0�_�-���_��?�F�\0����X���[��J�8&~D#��{P���4ܗ��\"�\0��������@ғ��\0F ?*��^��w�О:���u��3xK�^�w���߯�y[Ԟ(���#�/zr_�g��?�\0?�1wMR&M���?�St�T]ݴG�:I����)��B�� v����1�<�t��6�:�W{���x:=��ޚ��:�!!\0x�����q&��0}z\"]��o�z���j�w�����6��J�P۞[\\ }��`S�\0�qHM�/7B��P���]FT��8S5�/I�\r�\n ��O�0aQ\n�>�2�j�;=ڬ�dA=�p�VL)X�\n¦`e\$�TƦQJ����lJ����y�I�	�:����B�bP���Z��n����U;>_�\n	�����`��uM򌂂�֍m����Lw�B\0\\b8�M��[z��&�1�\0�	�\r�T������+\\�3�Plb4-)%Wd#\n��r��MX\"ϡ�(Ei11(b`@f����S���j�D��bf�}�r����D�R1���b��A��Iy\"�Wv��gC�I�J8z\"P\\i�\\m~ZR��v�1ZB5I��i@x����-�uM\njK�U�h\$o��JϤ!�L\"#p7\0� P�\0�D�\$	�GK4e��\$�\nG�?�3�EAJF4�Ip\0��F�4��<f@� %q�<k�w��	�LOp\0�x��(	�G>�@�����9\0T����GB7�-�����G:<Q��#���Ǵ�1�&tz��0*J=�'�J>���8q��Х���	�O��X�F��Q�,����\"9��p�*�66A'�,y��IF�R��T���\"��H�R�!�j#kyF���e��z�����G\0�p��aJ`C�i�@�T�|\n�Ix�K\"��*��Tk\$c��ƔaAh��!�\"�E\0O�d�Sx�\0T	�\0���!F�\n�U�|�#S&		IvL\"����\$h���EA�N\$�%%�/\nP�1���{��) <���L���-R1��6���<�@O*\0J@q��Ԫ#�@ǵ0\$t�|�]�`��ĊA]���Pᑀ�C�p\\pҤ\0���7���@9�b�m�r�o�C+�]�Jr�f��\r�)d�����^h�I\\�. g��>���8���'�H�f�rJ�[r�o���.�v���#�#yR�+�y��^����F\0᱁�]!ɕ�ޔ++�_�,�\0<@�M-�2W���R,c���e2�*@\0�P ��c�a0�\\P���O���`I_2Qs\$�w��=:�z\0)�`�h�������\nJ@@ʫ�\0�� 6qT��4J%�N-�m����.ɋ%*cn��N�6\"\r͑�����f�A���p�MۀI7\0�M�>lO�4�S	7�c���\"�ߧ\0�6�ps�����y.��	���RK��PAo1F�tI�b*��<���@�7�˂p,�0N��:��N�m�,�xO%�!��v����gz(�M���I��	��~y���h\0U:��OZyA8�<2����us�~l���E�O�0��0]'�>��ɍ�:���;�/��w�����'~3GΖ~ӭ����c.	���vT\0c�t'�;P�\$�\$����-�s��e|�!�@d�Obw��c��'�@`P\"x����0O�5�/|�U{:b�R\"�0�шk���`BD�\nk�P��c��4�^ p6S`��\$�f;�7�?ls��߆gD�'4Xja	A��E%�	86b�:qr\r�]C8�c�F\n'ьf_9�%(��*�~��iS����@(85�T��[��Jڍ4�I�l=��Q�\$d��h�@D	-��!�_]��H�Ɗ�k6:���\\M-����\r�FJ>\n.��q�eG�5QZ����' ɢ���ہ0��zP��#������r���t����ˎ��<Q��T��3�D\\����pOE�%)77�Wt�[��@����\$F)�5qG0�-�W�v�`�*)Rr��=9qE*K\$g	��A!�PjBT:�K���!��H� R0?�6�yA)B@:Q�8B+J�5U]`�Ҭ��:���*%Ip9�̀�`KcQ�Q.B��Ltb��yJ�E�T��7���Am�䢕Ku:��Sji� 5.q%LiF��Tr��i��K�Ҩz�55T%U��U�IՂ���Y\"\nS�m���x��Ch�NZ�UZ���( B��\$Y�V��u@蔻����|	�\$\0�\0�oZw2Ҁx2���k\$�*I6I�n�����I,��QU4�\n��).�Q���aI�]����L�h\"�f���>�:Z�>L�`n�ض��7�VLZu��e��X����B���B�����Z`;���J�]�����S8��f \nڶ�#\$�jM(��ޡ����a�G���+A�!�xL/\0)	C�\n�W@�4�����۩� ��RZ����=���8�`�8~�h��P ��\r�	���D-FyX�+�f�QSj+X�|��9-��s�x�����+�V�cbp쿔o6H�q�����@.��l�8g�YM��WMP��U��YL�3Pa�H2�9��:�a�`��d\0�&�Y��Y0٘��S�-��%;/�T�BS�P�%f������@�F��(�֍*�q +[�Z:�QY\0޴�JUY֓/���pkzȈ�,�𪇃j�ꀥW�״e�J�F��VBI�\r��pF�Nقֶ�*ը�3k�0�D�{����`q��ҲBq�e�D�c���V�E���n����FG�E�>j�����0g�a|�Sh�7u�݄�\$���;a��7&��R[WX���(q�#���P���ז�c8!�H���VX�Ď�j��Z������Q,DUaQ�X0��ը���Gb��l�B�t9-oZ���L���­�pˇ�x6&��My��sҐ����\"�̀�R�IWU`c���}l<|�~�w\"��vI%r+��R�\n\\����][��6�&���ȭ�a�Ӻ��j�(ړ�Tѓ��C'��� '%de,�\n�FC�эe9C�N�Ѝ�-6�Ueȵ��CX��V������+�R+�����3B��ڌJ�虜��T2�]�\0P�a�t29��(i�#�aƮ1\"S�:�����oF)k�f���Ъ\0�ӿ��,��w�J@��V򄎵�q.e}KmZ����XnZ{G-���ZQ���}��׶�6ɸ���_�؁Չ�\n�@7�` �C\0]_ ��ʵ����}�G�WW: fCYk+��b۶���2S,	ڋ�9�\0﯁+�W�Z!�e��2�������k.Oc��(v̮8�DeG`ۇ�L���,�d�\"C���B-�İ(����p���p�=����!�k������}(���B�kr�_R�ܼ0�8a%ۘL	\0���b������@�\"��r,�0T�rV>����Q��\"�r��P�&3b�P��-�x���uW~�\"�*舞�N�h�%7���K�Y��^A����C����p����\0�..`c��+ϊ�GJ���H���E����l@|I#Ac��D��|+<[c2�+*WS<�r��g���}��>i�݀�!`f8�(c����Q�=f�\n�2�c�h4�+q���8\na�R�B�|�R����m��\\q��gX����ώ0�X�`n�F���O p��H�C��jd�f��EuDV��bJɦ��:��\\�!mɱ?,TIa���aT.L�]�,J��?�?��FMct!a٧R�F�G�!�A���rr�-p�X��\r��C^�7���&�R�\0��f�*�A\n�՛H��y�Y=���l�<��A�_��	+��tA�\0B�<Ay�(fy�1�c�O;p���ᦝ`�4СM��*��f�� 5fvy {?���:y��^c��u�'���8\0��ӱ?��g��� 8B��&p9�O\"z���rs�0��B�!u�3�f{�\0�:�\n@\0����p���6�v.;�����b�ƫ:J>˂��-�B�hkR`-����aw�xEj����r�8�\0\\����\\�Uhm� �(m�H3̴��S����q\0��NVh�Hy�	��5�M͎e\\g�\n�IP:Sj�ۡٶ�<���x�&�L��;nfͶc�q��\$f�&l���i�����0%yΞ�t�/��gU̳�d�\0e:��h�Z	�^�@��1��m#�N��w@��O��zG�\$�m6�6}��ҋ�X'�I�i\\Q�Y���4k-.�:yz���H��]��x�G��3��M\0��@z7���6�-DO34�ދ\0Κ��ΰt\"�\"vC\"Jf�Rʞ��ku3�M��~����5V ��j/3���@gG�}D���B�Nq��=]\$�I��Ӟ�3�x=_j�X٨�fk(C]^j�M��F��ա��ϣCz��V��=]&�\r�A<	������6�Ԯ�״�`jk7:g��4ծ��YZq�ftu�|�h�Z��6��i〰0�?��骭{-7_:��ސtѯ�ck�`Y��&���I�lP`:�� j�{h�=�f	��[by��ʀoЋB�RS���B6��^@'�4��1U�Dq}��N�(X�6j}�c�{@8���,�	�PFC���B�\$mv���P�\"��L��CS�]����E���lU��f�wh{o�(��)�\0@*a1G� (��D4-c��P8��N|R���VM���n8G`e}�!}���p�����@_���nCt�9��\0]�u��s���~�r��#Cn�p;�%�>wu���n�w��ݞ�.���[��hT�{��值	�ˁ��J���ƗiJ�6�O�=������E��ٴ��Im���V'��@�&�{��������;�op;^��6Ŷ@2�l���N��M��r�_ܰ�Í�` �( y�6�7�����ǂ��7/�p�e>|��	�=�]�oc����&�xNm���烻��o�G�N	p����x��ý���y\\3����'�I`r�G�]ľ�7�\\7�49�]�^p�{<Z��q4�u�|��Qۙ��p���i\$�@ox�_<���9pBU\"\0005�� i�ׂ��C�p�\n�i@�[��4�jЁ�6b�P�\0�&F2~������U&�}����ɘ	��Da<��zx�k���=���r3��(l_���FeF���4�1�K	\\ӎld�	�1�H\r���p!�%bG�Xf��'\0���	'6��ps_��\$?0\0�~p(�H\n�1�W:9�͢��`��:h�B��g�B�k��p�Ɓ�t��EBI@<�%����` �y�d\\Y@D�P?�|+!��W��.:�Le�v,�>q�A���:���bY�@8�d>r/)�B�4���(���`|�:t�!����?<�@���/��S��P\0��>\\�� |�3�:V�uw���x�(����4��ZjD^���L�'���C[�'�����jº[�E�� u�{KZ[s���6��S1��z%1�c��B4�B\n3M`0�;����3�.�&?��!YA�I,)��l�W['��ITj���>F���S���BбP�ca�ǌu�N����H�	LS��0��Y`���\"il�\r�B���/����%P���N�G��0J�X\n?a�!�3@M�F&ó����,�\"���lb�:KJ\r�`k_�b��A��į��1�I,�����;B,�:���Y%�J���#v��'�{������	wx:\ni����}c��eN���`!w��\0�BRU#�S�!�<`��&v�<�&�qO�+Σ�sfL9�Q�Bʇ����b��_+�*�Su>%0�����8@l�?�L1po.�C&��ɠB��qh�����z\0�`1�_9�\"���!�\$���~~-�.�*3r?�ò�d�s\0����>z\n�\0�0�1�~���J����|Sޜ��k7g�\0��KԠd��a��Pg�%�w�D��zm�����)����j�����`k���Q�^��1���+��>/wb�GwOk���_�'��-CJ��7&����E�\0L\r>�!�q́���7����o��`9O`�����+!}�P~E�N�c��Q�)��#��#�����������J��z_u{��K%�\0=��O�X�߶C�>\n���|w�?�F�����a�ϩU����b	N�Y��h����/��)�G��2���K|�y/�\0��Z�{��P�YG�;�?Z}T!�0��=mN����f�\"%4�a�\"!�ޟ����\0���}��[��ܾ��bU}�ڕm��2�����/t���%#�.�ؖ��se�B�p&}[˟��7�<a�K���8��P\0��g��?��,�\0�߈r,�>���W����/��[�q��k~�CӋ4��G��:��X��G�r\0������L%VFLUc��䑢��H�ybP��'#��	\0п���`9�9�~���_��0q�5K-�E0�b�ϭ�����t`lm����b��Ƙ; ,=��'S�.b��S���Cc����ʍAR,����X�@�'��8Z0�&�Xnc<<ȣ�3\0(�+*�3��@&\r�+�@h, ��\$O���\0Œ��t+>����b��ʰ�\r�><]#�%�;N�s�Ŏ����*��c�0-@��L� >�Y�p#�-�f0��ʱa�,>��`����P�:9��o���ov�R)e\0ڢ\\����\nr{îX����:A*��.�D��7�����#,�N�\r�E���hQK2�ݩ��z�>P@���	T<��=�:���X�GJ<�GAf�&�A^p�`���{��0`�:���);U !�e\0����c�p\r�����:(��@�%2	S�\$Y��3�hC��:O�#��L��/����k,��K�oo7�BD0{���j��j&X2��{�}�R�x��v���أ�9A����0�;0�����-�5��/�<�� �N�8E����	+�Ѕ�Pd��;���*n��&�8/jX�\r��>	PϐW>K��O��V�/��U\n<��\0�\nI�k@��㦃[��Ϧ²�#�?���%���.\0001\0��k�`1T� ����ɐl�������p���������< .�>��5��\0��	O�>k@Bn��<\"i%�>��z��������3�P�!�\r�\"��\r �>�ad���U?�ǔ3P��j3�䰑>;���>�t6�2�[��޾M\r�>��\0��P���B�Oe*R�n���y;� 8\0���o�0���i���3ʀ2@����?x�[����L�a����w\ns����A��x\r[�a�6�clc=�ʼX0�z/>+����W[�o2���)e�2�HQP�DY�zG4#YD����p)	�H�p���&�4*@�/:�	�T�	���aH5���h.�A>��`;.���Y��a	���t/ =3��BnhD?(\n�!�B�s�\0��D�&D�J��)\0�j�Q�y��hDh(�K�/!�>�h,=�����tJ�+�S��,\"M�Ŀ�N�1�[;�Т��+��#<��I�Zğ�P�)��LJ�D��P1\$����Q�>dO��v�#�/mh8881N:��Z0Z���T �B�C�q3%��@�\0��\"�XD	�3\0�!\\�8#�h�v�ib��T�!d�����V\\2��S��Œ\nA+ͽp�x�iD(�(�<*��+��E��T���B�S�CȿT���� e�A�\"�|�u�v8�T\0002�@8D^oo�����|�N������J8[��3����J�z׳WL\0�\0��Ȇ8�:y,�6&@�� �E�ʯݑh;�!f��.B�;:���[Z3������n���ȑ��A���qP4,��Xc8^��`׃��l.����S�hޔ���O+�%P#Ρ\n?��IB��eˑ�O\\]��6�#��۽؁(!c)�N����?E��B##D �Ddo��P�A�\0�:�n�Ɵ�`  ��Q��>!\r6�\0��V%cb�HF�)�m&\0B�2I�5��#]���D>��3<\n:ML��9C���0��\0���(ᏩH\n����M�\"GR\n@���`[���\ni*\0��)������u�)��Hp\0�N�	�\"��N:9q�.\r!���J��{,�'����4�B���lq���Xc��4��N1ɨ5�Wm��3\n��F��`�'��Ҋx��&>z>N�\$4?����(\n쀨>�	�ϵP�!Cq͌��p�qGLqq�G�y�H.�^��\0z�\$�AT9Fs�Ѕ�D{�a��cc_�G�z�)� �}Q��h��HBָ�<�y!L����!\\�����'�H(��-�\"�in]Ğ���\\�!�`M�H,gȎ��*�Kf�*\0�>6���6��2�hJ�7�{nq�8����H�#c�H�#�\r�:��7�8�܀Z��ZrD��߲`rG\0�l\n�I��i\0<����\0Lg�~���E��\$��P�\$�@�PƼT03�HGH�l�Q%*\"N?�%��	��\n�CrW�C\$��p�%�uR`��%��R\$�<�`�Ifx���\$/\$�����\$���O�(���\0��\0�RY�*�/	�\rܜC9��&hh�=I�'\$�RRI�'\\�a=E����u·'̙wI�'T���������K9%�d����!��������j�����&���v̟�\\=<,�E��`�Y��\\����*b0>�r��,d�pd���0DD ̖`�,T �1�% P���/�\r�b�(���J����T0�``ƾ����J�t���ʟ((d�ʪ�h+ <Ɉ+H%i�����#�`� ���'��B>t��J�Z\\�`<J�+hR���8�hR�,J]g�I��0\n%J�*�Y���JwD��&ʖD�������R�K\"�1Q�� ��AJKC,�mV�������-���KI*�r��\0�L�\"�Kb(����J:qKr�d�ʟ-)��ˆ#Ը�޸[�A�@�.[�Ҩʼ�4���.�1�J�.̮�u#J���g\0��򑧣<�&���K�+�	M?�/d��%'/��2Y��>�\$��l�\0��+����}-t��ͅ*�R�\$ߔ��K�.����JH�ʉ�2\r��B���(P���6\"��nf�\0#Ї ��%\$��[�\n�no�LJ�����e'<����1K��y�Y1��s�0�&zLf#�Ƴ/%y-�ˣ3-��K��L�΁��0����[,��̵,������0���(�.D��@��2�L+.|�����2�(�L�*��S:\0�3����G3l��aːl�@L�3z4�ǽ%̒�L�3����!0�33=L�4|ȗ��+\"���4���7�,\$�SPM�\\��?J�Y�̡��+(�a=K��4���C̤<Ё�=\$�,��UJ]5h�W�&t�I%��5�ҳ\\M38g�́5H�N?W1H��^��Ը�Y͗ؠ�͏.�N3M�4Å�`��i/P�7�dM>�d�/�LR���=K�60>�I\0[��\0��\r2���Z@�1��2��7�9�FG+�Ҝ�\r)�hQtL}8\$�BeC#��r*H�۫�-�H�/���6��\$�RC9�ب!���7�k/P�0Xr5��3D���<T�Ԓq�K���n�H�<�F�:1SL�r�%(��u)�Xr�1��nJ�I��S�\$\$�.·9��IΟ�3 �L�l���Ι9��C�N�#ԡ�\$�/��s��9�@6�t���N�9���N�:����7�Ӭ�:D���M)<#���M}+�2�N��O&��JNy*���ٸ[;���O\"m����M�<c�´���8�K�,���N�=07s�JE=T��O<����J�=D��:�C<���ˉ=���K�ʻ̳�L3�����LTЀ3�S,�.���q-��s�7�>�?�7O;ܠ`�OA9���ϻ\$���O�;��`9�n�I�A�xp��E=O�<��5����2�O�?d�����`N�iO�>��3�P	?���O�m��S�M�ˬ��=�(�d�Aȭ9���\0�#��@��9D����&���?����i9�\n�/��A���ȭA��S�Po?kuN5�~4���6���=򖌓*@(�N\0\\۔dG��p#��>�0��\$2�4z )�`�W���+\0��80�菦������z\"T��0�:\0�\ne \$��rM�=�r\n�N�P�Cmt80�� #��J=�&��3\0*��B�6�\"������#��>�	�(Q\n���8�1C\rt2�EC�\n`(�x?j8N�\0��[��QN>���'\0�x	c���\n�3��Ch�`&\0���8�\0�\n���O`/����A`#��Xc���D �tR\n>���d�B�D�L��������Dt4���j�p�GAoQoG8,-s����K#�);�E5�TQ�G�4Ao\0�>�tM�D8yRG@'P�C�	�<P�C�\"�K\0��x��~\0�ei9���v))ѵGb6���H\r48�@�M�:��F�tQ�!H��{R} �URp���O\0�I�t8������[D4F�D�#��+D�'�M����>RgI���Q�J���U�)Em���TZ�E�'��iE����qFzA��>�)T�Q3H�#TL�qIjNT���&C��h�X\nT���K\0000�5���JH�\0�FE@'љFp�hS5F�\"�oѮ�e%aoS E)� ��DU��Q�Fm�ѣM��Ѳe(tn� �U1ܣ~>�\$��ǂ��(h�ǑG�y`�\0��	��G��3�5Sp(��P�G�\$��#��	���N�\n�V\$��]ԜP�=\"RӨ?Lzt��1L\$\0��G~��,�KN�=���GM����NS�)��O]:ԊS}�81�RGe@C�\0�OP�S�N�1��T!P�@��S����S�G`\n�:��P�j�7R� @3��\n� �������DӠ��L�����	��\0�Q5���CP��SMP�v4��?h	h�T�D0��֏��>&�ITx�O�?�@U��R8@%Ԗ��K���N�K��RyE�E#�� @����%L�Q�Q����?N5\0�R\0�ԁT�F�ԔR�S�!oTE�C(�����ĵ\0�?3i�SS@U�QeM��	K�\n4P�CeS��\0�NC�P��O�!�\"RT�����S�N���U5OU>UiI�PU#UnKP��UYT�*�C��U�/\0+���)��:ReA�\$\0���x��WD�3���`����U5�IHUY��:�P	�e\0�MJi�����Q�>�@�T�C{��u��?�^�v\0WR�]U}C��1-5+U�?�\r�W<�?5�JU-SX��L�� \\t�?�sM�b�ՃV܁t�T�>�MU+�	E�c���9Nm\rRǃC�8�S�X�'R��XjCI#G|�!Q�Gh�t�Q��� )<�Y�*��RmX0����M���OQ�Y�h���du���Z(�Ao#�NlyN�V�Z9I���M��V�ZuOՅT�T�EՇַS�e����\n�X��S�QER����[MF�V�O=/����>�gչT�V�oU�T�Z�N�*T\\*����S-p�S��V�q��M(�Q=\\�-UUUV�C���Z�\nu�V\$?M@U�WJ\r\rU��\\�'U�W]�W��W8�N�'#h=oC���F(��:9�Yu����V-U�9�]�C�:U�\\�\n�qW���(TT?5P�\$ R3�⺟C}`>\0�E]�#R��	��#R�)�W���:`#�G�)4�R��;��ViD%8�)Ǔ^�Q��#�h	�HX	��\$N�x��#i x�ԒXR��'�9`m\\���\nE��Q�`�bu@��N�dT�#YY����GV�]j5#?L�xt/#���#酽O�P��Q��6����^� �������M\\R5t�Ӛp�*��X�V\"W�D�	oRALm\rdG�N	����6�p\$�P废E5����Tx\n�+��C[��V�����8U�Du}ػF\$.��Q-;4Ȁ�NX\n�.X�b͐�\0�b�)�#�N�G4K��ZS�^״M�8��d�\"C��>��dHe\n�Y8���.� ���ҏF�D��W1cZ6��Q�KH�@*\0�^���\\Q�F�4U3Y|�=�Ӥ�E��ۤ�?-�47Y�Pm�hYw_\r�VeױM���ُe(0��F�\r�!�PUI�u�7Q�C�ю?0����gu\rqधY-Q�����=g\0�\0M#�U�S5Zt�֟ae^�\$>�ArV�_\r;t���HW�Z�@H��hzD��\0�S2J� HI�O�'ǁe�g�6�[�R�<�?� /��KM����\n>��H�Z!i����TX6���i�C !ӛg�� �G }Q6��4>�w�!ڙC}�VB�>�UQڑj�8c�U�T���'<�>����HC]�V��7jj3v���`0���23����x�@U�k�\n�:Si5��#Y�-w����M?c��MQ�GQ�уb`��\0�@��ҧ\0M��)ZrKX�֟�Wl������l�TM�D\r4�QsS�40�sQ́�mY�h�d��C`{�V�gE�\n��XkՁ�'��,4���^��6�#<4��NXnM):��OM_6d�������[\"KU�n��?l�x\0&\0�R56�T~>��ո?�Jn��� ��Z/i�6���glͦ�U��F}�.����JL�CTbM�4��cL�TjSD�}Jt���Z����:�L���d:�Ez�ʤ�>��V\$2>����[�p�6��R�9u�W.?�1��RHu���R�?58Ԯ��D��u���p�c�Z�?�r׻ Eaf��}5wY���ϒ���W�wT[Sp7'�_aEk�\"[/i��#�\$;m�fأWO����F�\r%\$�ju-t#<�!�\n:�KEA����]�\nU�Q�KE��#��X��5[�>�`/��D��֭VEp�)��I%�q���n�x):��le���[e�\\�eV[j�����7 -+��G�WEwt�WkE�~u�Q/m�#ԐW�`�yu�ǣD�A�'ױ\r��ՙO�D )ZM^��u-|v8]�g��h���L��W\0���6�X��=Y�d�Q�7ϓ��9����r <�֏�D��B`c�9���`�D�=wx�I%�,ᄬ�����j[њ����O��� ``��|�����������.�	AO���	��@�@ 0h2�\\�ЀM{e�9^>���@7\0��˂W���\$,��Ś�@؀����w^fm�,\0�yD,ם^X�.�ֆ�7����2��f;��6�\n����^�zC�קmz��n�^���&LFF�,��[��e��aXy9h�!:z�9c�Q9b� !���Gw_W�g�9���S+t���p�tɃ\nm+����_�	��\\���k5���]�4�_h�9 ��N����]%|��7�֜�];��|���X��9�|����G���[��\0�}U���MC�I:�qO�Vԃa\0\r�R�6π�\0�@H��P+r�S�W���p7�I~�p/��H�^������E�-%��̻�&.��+�Jђ;:���!���N�	�~����/�W��!�B�L+�\$��q�=��+�`/Ƅe�\\���x�pE�lpS�JS�ݢ��6��_�(ů���b\\O��&�\\�59�\0�9n���D�{�\$���K��v2	d]�v�C�����?�tf|W�:���p&��Ln��賞�{;���G�R9��T.y���I8���\rl� �	T��n�3���T.�9��3����Z�s����G����:	0���z��.�]��ģQ�?�gT�%��x�Ռ.����n<�-�8B˳,B��rgQ�����Ɏ`��2�:{�g��s��g�Z��� ׌<��w{���bU9�	`5`4�\0BxMp�8qnah�@ؼ�-�(�>S|0�����3�8h\0���C�zLQ�@�\n?��`A��>2��,���N�&��x�l8sah1�|�B�ɇD�xB�#V��V�׊`W�a'@���	X_?\n�  �_�. �P�r2�bUar�I�~��S���\0ׅ\"�2����>b;�vPh{[�7a`�\0�˲j�o�~���v��|fv�4[�\$��{�P\rv�BKGbp������O�5ݠ2\0j�لL���)�m��V�ejBB.'R{C��V'`؂ ��%�ǀ�\$�O��\0�`����4 �N�>;4���/�π��*��\\5���!��`X*�%��N�3S�AM���Ɣ,�1����\\��caϧ ��@��˃�B/����0`�v2��`hD�JO\$�@p!9�!�\n1�7pB,>8F4��f�π:��7���3��3����T8�=+~�n���\\�e�<br����Fز� ��C�N�:c�:�l�<\r��\\3�>���6�ONn��!;��@�tw�^F�L�;���,^a��\ra\"��ڮ'�:�v�Je4�א;��_d\r4\r�:����S�����2��[c��X�ʦPl�\$�ޣ�i�w�d#�B��b��������`:���~ <\0�2����R���P�\r�J8D�t@�E��\0\r͜6����7����Y���\"����\r�����3��.�+�z3�;_ʟvL����wJ�94�I�Ja,A����;�s?�N\nR��!��ݐ�Om�s�_��-zۭw���zܭ7���z���M����o����\0��a��ݹ4�8�Pf�Y�?��i��eB�S�1\0�jDTeK��UYS�?66R	�c�6Ry[c���5�]B͔�R�_eA)&�[凕XYRW�6VYaeU�fYe�w��U�b�w�E�ʆ;z�^W�9��ק�ݖ��\0<ޘ�e�9S���da�	�_-��L�8ǅ�Q��TH[!<p\0��Py5�|�#��P�	�9v��2�|Ǹ��fao��,j8�\$A@k����a���b�c��f4!4���cr,;�����b�=��;\0��ź���cd��X�b�x�a�Rx0A�h�+w�xN[��B��p���w�T�8T%��M�l2�������}��s.kY��0\$/�fU�=��s�gK���M� �?���`4c.��!�&�分g��f�/�f1�=��V AE<#̹�f\n�)���Np��`.\"\"�A�����q��X��٬:a�8��f��Vs�G��r�:�V��c�g�Vl��g=��`��W���y�gU��˙�Ẽ�eT=�����x 0� M�@����%κb���w��f��O�筘�*0���|t�%��P��p��gK���?p�@J�<Bٟ#�`1��9�2�g�!3~����nl��f��Vh���.����aC���?���-�1�68>A��a�\r��y�0��i�J�}�������z:\r�)�S���@��h@���Y���mCEg�cyφ��<���h@�@�zh<W��`��:zO���\r��W���V08�f7�(Gy���`St#��f�#����C(9���؀d���8T:���0�� q���79��phAg�6�.��7Fr�b� �j��A5��a1��h�ZCh:�%��gU��D9��Ɉ�׹��0~vTi;�VvS��w��\r΃?��f�����n�ϛiY��a��3�·9�,\n��r��,/,@.:�Y>&��F�)�����}�b���iO�i��:d�A�n��c=�L9O�h{�� 8hY.������������\r��և�����1Q�U	�C�h��e�O���+2o����N�����zp�(�]�h��Z|�O�c�zD���;�T\0j�\0�8#�>Ύ�=bZ8Fj���;�޺T酡w��)���N`���ÅB{��z\r�c���|dTG�i�/��!i��0���'`Z:�CH�(8�`V������\0�ꧩ��W��Ǫ��zgG������-[��	i��N\rq��n���o	ƥfEJ��apb��}6���=o���,t�Y+��EC\r�Px4=����@���.��F��[�zq���X6:FG��#��\$@&�ab��hE:����`�S�1�1g1���2uhY��_:Bߡdc�*���\0�ƗFYF�:���n���=ۨH*Z�Mhk�/�냡�zٹ]��h@����1\0��ZK�������^+�,vf�s��>���O�|���s�\0֜5�X��ѯF��n�A�r]|�Ii4�� ��C� h@ع����cߥ�6smO������gX�V2�6g?~��Y�Ѱ�s�cl \\R�\0��c��A+�1������\n(����^368cz:=z��(�� ;裨�s�F�@`;�,>yT��&��d�Lן��%��-�CHL8\r��b�����Mj]4�Ym9����Z�B��P}<���X���̥�+g�^�M� + B_Fd�X���l�w�~�\r⽋�\":��qA1X������3�ΓE�h�4�ZZ��&����1~!N�f��o���\nMe�଄��XI΄�G@V*X��;�Y5{V�\n���T�z\rF�3}m��p1�[�>�t�e�w����@V�z#��2��	i���{�9��p̝�gh���+[elU���A�ٶӼi1�!��omm�*K���}��!�Ƴ����{me�f`��m��C�z=�n�:}g� T�mLu1F��}=8�Z���O��mFFMf��OO����������/����ޓ���V�oqj���n!+����Z��I�.�9!nG�\\��3a�~�O+��::�K@�\n�@���Hph��\\B��dm�fvC���P�\" ��.nW&��n��HY�+\r���z�i>Mfqۤ��Qc�[�H+��o��*�1'��#āEw�D_X�)>�s��-~\rT=�������- �y�m����{�h��j�M�)�^����'@V�+i�������;F��D[�b!����B	��:MP���ۭoC�vAE?�C�IiY��#�p�P\$k�J�q�.�07���x�l�sC|���bo�2�X�>M�\rl&��:2�~��cQ����o��d�-��U�Ro�Y�nM;�n�#��\0�P�f��Po׿(C�v<���[�o۸����fѿ���;�ẖ�[�Y�.o�Up���pU���.���B!'\0���<T�:1�������<���n��F���I�ǔ��V0�ǁRO8�w��,aF��ɥ�[�Ο��YO����/\0��ox���Q�?��:ً���`h@:�����/M�m�x:۰c1������v�;���^���@��@�����\n{�����;���B���8�� g坒�\\*g�yC)��E�^�O�h	���A�u>���@�D��Y�����`o�<>��p���ķ�q,Y1Q��߸��/qg�\0+\0���D���?�� ����k:�\$����ץ6~I��=@���!��v�zO񁚲�+���9�i����a������g������?��0Gn�q�]{Ҹ,F���O���� <_>f+��,��	���&�����·�y�ǩO�:�U¯�L�\n�úI:2��-;_Ģ�|%�崿!��f�\$���Xr\"Kni����\$8#�g�t-��r@L�圏�@S�<�rN\n�D/rLdQk࣓�����e����Э��\n=4)�B���ך�");
    } else {
        header('Content-Type: image/gif');
        switch ($_GET['file']) {
            case'plus.gif':echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0!�����M��*)�o��) q��e���#��L�\0;";
                break;
            case'cross.gif':echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0#�����#\na�Fo~y�.�_wa��1�J�G�L�6]\0\0;";
                break;
            case'up.gif':echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����MQN\n�}��a8�y�aŶ�\0��\0;";
                break;
            case'down.gif':echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����M��*)�[W�\\��L&ٜƶ�\0��\0;";
                break;
            case'arrow.gif':echo"GIF89a\0\n\0�\0\0������!�\0\0\0,\0\0\0\0\0\n\0\0�i������Ӳ޻\0\0;";
                break;
        }
    }
    exit;
} if ($_GET['script'] == 'version') {
    $p = file_open_lock(get_temp_dir().'/adminer.version');
    if ($p) {
        file_write_unlock($p, serialize(['signature' => $_POST['signature'], 'version' => $_POST['version']]));
    }
    exit;
}global$b,$e,$Ib,$Pb,$Zb,$k,$Bc,$Fc,$ba,$Vc,$y,$ca,$ld,$ge,$Ke,$Yf,$Jc,$T,$Gg,$Lg,$Sg,$ga;
if (! $_SERVER['REQUEST_URI']) {
    $_SERVER['REQUEST_URI'] = $_SERVER['ORIG_PATH_INFO'];
} if (! strpos($_SERVER['REQUEST_URI'], '?') && $_SERVER['QUERY_STRING'] != '') {
    $_SERVER['REQUEST_URI'] .= "?$_SERVER[QUERY_STRING]";
} if ($_SERVER['HTTP_X_FORWARDED_PREFIX']) {
    $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_FORWARDED_PREFIX'].$_SERVER['REQUEST_URI'];
}$ba = $_SERVER['HTTPS'] && strcasecmp($_SERVER['HTTPS'], 'off');
@ini_set('session.use_trans_sid', false);
if (! defined('SID')) {
    session_cache_limiter('');
    session_name('adminer_sid');
    $Be = [0, preg_replace('~\\?.*~', '', $_SERVER['REQUEST_URI']), '', $ba];
    if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
        $Be[] = true;
    }
    call_user_func_array('session_set_cookie_params', $Be);
    session_start();
}remove_slashes([&$_GET, &$_POST, &$_COOKIE], $tc);
if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(false);
}@set_time_limit(0);
@ini_set('zend.ze1_compatibility_mode', false);
@ini_set('precision', 15);
function get_lang()
{
    return'en';
}function lang($Fg, $Xd = null)
{
    if (is_array($Fg)) {
        $Ne = ($Xd == 1 ? 0 : 1);
        $Fg = $Fg[$Ne];
    }
    $Fg = str_replace('%d', '%s', $Fg);
    $Xd = format_number($Xd);

    return
    sprintf($Fg, $Xd);
} if (extension_loaded('pdo')) {
    class Min_PDO extends PDO
    {
        public $_result;

        public $server_infovar;

        public $affected_rowsvar;

        public $errnovar;

        public $errorvar;

        public function __construct()
        {
            global$b;
            $Ne = array_search('SQL', $b->operators);
            if ($Ne !== false) {
                unset($b->operators[$Ne]);
            }
        }

        public function dsn($Mb, $V, $G, $me = [])
        {
            try {
                parent::__construct($Mb, $V, $G, $me);
            } catch (Exception$ec) {
                auth_error(h($ec->getMessage()));
            }
            $this->setAttribute(13, ['Min_PDOStatement']);
            $this->server_info = @$this->getAttribute(4);
        }

        public function query($I, $Mg = false)
        {
            $J = parent::query($I);
            $this->error = '';
            if (! $J) {
                [, $this->errno, $this->error] = $this->errorInfo();

                return
                false;
            }
            $this->store_result($J);

            return$J;
        }

        public function multi_query($I)
        {
            return$this->_result = $this->query($I);
        }

        public function store_result($J = null)
        {
            if (! $J) {
                $J = $this->_result;
                if (! $J) {
                    return
                    false;
                }
            }
            if ($J->columnCount()) {
                $J->num_rows = $J->rowCount();

                return$J;
            }
            $this->affected_rows = $J->rowCount();

            return
            true;
        }

        public function next_result()
        {
            if (! $this->_result) {
                return
                false;
            }
            $this->_result->_offset = 0;

            return@$this->_result->nextRowset();
        }

        public function result($I, $l = 0)
        {
            $J = $this->query($I);
            if (! $J) {
                return
                false;
            }
            $L = $J->fetch();

            return$L[$l];
        }
    }
    class Min_PDOStatement extends PDOStatement
    {
        public $_offset = 0;

        public $num_rowsvar;

        public function fetch_assoc()
        {
            return$this->fetch(2);
        }

        public function fetch_row()
        {
            return$this->fetch(3);
        }

        public function fetch_field()
        {
            $L = (object) $this->getColumnMeta($this->_offset++);
            $L->orgtable = $L->table;
            $L->orgname = $L->name;
            $L->charsetnr = (in_array('blob', (array) $L->flags) ? 63 : 0);

            return$L;
        }
    }
}$Ib = [];
class Min_SQL
{
    public $_conn;

    public function __construct($e)
    {
        $this->_conn = $e;
    }

    public function select($Q, $N, $Z, $s, $oe = [], $_ = 1, $F = 0, $Ue = false)
    {
        global$b,$y;
        $cd = (count($s) < count($N));
        $I = $b->selectQueryBuild($N, $Z, $s, $oe, $_, $F);
        if (! $I) {
            $I = 'SELECT'.limit(($_GET['page'] != 'last' && $_ != '' && $s && $cd && $y == 'sql' ? 'SQL_CALC_FOUND_ROWS ' : '').implode(', ', $N)."\nFROM ".table($Q), ($Z ? "\nWHERE ".implode(' AND ', $Z) : '').($s && $cd ? "\nGROUP BY ".implode(', ', $s) : '').($oe ? "\nORDER BY ".implode(', ', $oe) : ''), ($_ != '' ? +$_ : null), ($F ? $_ * $F : 0), "\n");
        }
        $Tf = microtime(true);
        $K = $this->_conn->query($I);
        if ($Ue) {
            echo$b->selectQuery($I, $Tf, ! $K);
        }

        return$K;
    }

    public function delete($Q, $cf, $_ = 0)
    {
        $I = 'FROM '.table($Q);

        return
        queries('DELETE'.($_ ? limit1($Q, $I, $cf) : " $I$cf"));
    }

    public function update($Q, $P, $cf, $_ = 0, $Ff = "\n")
    {
        $bh = [];
        foreach ($P
        as$z => $X) {
            $bh[] = "$z = $X";
        }
        $I = table($Q)." SET$Ff".implode(",$Ff", $bh);

        return
        queries('UPDATE'.($_ ? limit1($Q, $I, $cf, $Ff) : " $I$cf"));
    }

    public function insert($Q, $P)
    {
        return
        queries('INSERT INTO '.table($Q).($P ? ' ('.implode(', ', array_keys($P)).")\nVALUES (".implode(', ', $P).')' : ' DEFAULT VALUES'));
    }

    public function insertUpdate($Q, $M, $Te)
    {
        return
        false;
    }

    public function begin()
    {
        return
        queries('BEGIN');
    }

    public function commit()
    {
        return
        queries('COMMIT');
    }

    public function rollback()
    {
        return
        queries('ROLLBACK');
    }

    public function convertSearch($Qc, $X, $l)
    {
        return$Qc;
    }

    public function value($X, $l)
    {
        return$X;
    }

    public function quoteBinary($xf)
    {
        return
        q($xf);
    }

    public function warnings()
    {
        return'';
    }

    public function tableHelp($E)
    {
    }
}$Ib = ['server' => 'MySQL'] + $Ib;
if (! defined('DRIVER')) {
    $Qe = ['MySQLi', 'MySQL', 'PDO_MySQL'];
    define('DRIVER', 'server');
    if (extension_loaded('mysqli')) {
        class Min_DB extends MySQLi
        {
            public $extension = 'MySQLi';

            public function __construct()
            {
                parent::init();
            }

            public function connect($O = '', $V = '', $G = '', $tb = null, $Me = null, $Mf = null)
            {
                global$b;
                mysqli_report(MYSQLI_REPORT_OFF);
                [$Nc, $Me] = explode(':', $O, 2);
                $Sf = $b->connectSsl();
                if ($Sf) {
                    $this->ssl_set($Sf['key'], $Sf['cert'], $Sf['ca'], '', '');
                }
                $K = @$this->real_connect(($O != '' ? $Nc : ini_get('mysqli.default_host')), ($O.$V != '' ? $V : ini_get('mysqli.default_user')), ($O.$V.$G != '' ? $G : ini_get('mysqli.default_pw')), $tb, (is_numeric($Me) ? $Me : ini_get('mysqli.default_port')), (! is_numeric($Me) ? $Me : $Mf), ($Sf ? 64 : 0));

                return$K;
            }

            public function set_charset($La)
            {
                if (parent::set_charset($La)) {
                    return
                    true;
                }
                parent::set_charset('utf8');

                return$this->query("SET NAMES $La");
            }

            public function result($I, $l = 0)
            {
                $J = $this->query($I);
                if (! $J) {
                    return
                    false;
                }
                $L = $J->fetch_array();

                return$L[$l];
            }

            public function quote($Xf)
            {
                return"'".$this->escape_string($Xf)."'";
            }
        }
    } elseif (extension_loaded('mysql') && ! (ini_get('sql.safe_mode') && extension_loaded('pdo_mysql'))) {
        class Min_DB
        {
            public $extension = 'MySQL';

            public $server_infovar;

            public $affected_rowsvar;

            public $errnovar;

            public $errorvar;

            public $_linkvar;

            public $_resultvar;

            public function connect($O, $V, $G)
            {
                $this->_link = @mysql_connect(($O != '' ? $O : ini_get('mysql.default_host')), ("$O$V" != '' ? $V : ini_get('mysql.default_user')), ("$O$V$G" != '' ? $G : ini_get('mysql.default_password')), true, 131072);
                if ($this->_link) {
                    $this->server_info = mysql_get_server_info($this->_link);
                } else {
                    $this->error = mysql_error();
                }

                return(bool) $this->_link;
            }

            public function set_charset($La)
            {
                if (function_exists('mysql_set_charset')) {
                    if (mysql_set_charset($La, $this->_link)) {
                        return
                        true;
                    }
                    mysql_set_charset('utf8', $this->_link);
                }

                return$this->query("SET NAMES $La");
            }

            public function quote($Xf)
            {
                return"'".mysql_real_escape_string($Xf, $this->_link)."'";
            }

            public function select_db($tb)
            {
                return
                mysql_select_db($tb, $this->_link);
            }

            public function query($I, $Mg = false)
            {
                $J = @($Mg ? mysql_unbuffered_query($I, $this->_link) : mysql_query($I, $this->_link));
                $this->error = '';
                if (! $J) {
                    $this->errno = mysql_errno($this->_link);
                    $this->error = mysql_error($this->_link);

                    return
                    false;
                }
                if ($J === true) {
                    $this->affected_rows = mysql_affected_rows($this->_link);
                    $this->info = mysql_info($this->_link);

                    return
                    true;
                }

                return
                                new
                                Min_Result($J);
            }

            public function multi_query($I)
            {
                return$this->_result = $this->query($I);
            }

            public function store_result()
            {
                return$this->_result;
            }

            public function next_result()
            {
                return
                false;
            }

            public function result($I, $l = 0)
            {
                $J = $this->query($I);
                if (! $J || ! $J->num_rows) {
                    return
                    false;
                }

                return
                                mysql_result($J->_result, 0, $l);
            }
        }
        class Min_Result
        {
            public $num_rows;

            public $_resultvar;

            public $_offsetvar = 0;

            public function __construct($J)
            {
                $this->_result = $J;
                $this->num_rows = mysql_num_rows($J);
            }

            public function fetch_assoc()
            {
                return
                mysql_fetch_assoc($this->_result);
            }

            public function fetch_row()
            {
                return
                mysql_fetch_row($this->_result);
            }

            public function fetch_field()
            {
                $K = mysql_fetch_field($this->_result, $this->_offset++);
                $K->orgtable = $K->table;
                $K->orgname = $K->name;
                $K->charsetnr = ($K->blob ? 63 : 0);

                return$K;
            }

            public function __destruct()
            {
                mysql_free_result($this->_result);
            }
        }
    } elseif (extension_loaded('pdo_mysql')) {
        class Min_DB extends Min_PDO
        {
            public $extension = 'PDO_MySQL';

            public function connect($O, $V, $G)
            {
                global$b;
                $me = [];
                $Sf = $b->connectSsl();
                if ($Sf) {
                    $me = [PDO::MYSQL_ATTR_SSL_KEY => $Sf['key'], PDO::MYSQL_ATTR_SSL_CERT => $Sf['cert'], PDO::MYSQL_ATTR_SSL_CA => $Sf['ca']];
                }
                $this->dsn('mysql:charset=utf8;host='.str_replace(':', ';unix_socket=', preg_replace('~:(\\d)~', ';port=\\1', $O)), $V, $G, $me);

                return
                true;
            }

            public function set_charset($La)
            {
                $this->query("SET NAMES $La");
            }

            public function select_db($tb)
            {
                return$this->query('USE '.idf_escape($tb));
            }

            public function query($I, $Mg = false)
            {
                $this->setAttribute(1000, ! $Mg);

                return
                parent::query($I, $Mg);
            }
        }
    }
    class Min_Driver extends Min_SQL
    {
        public function insert($Q, $P)
        {
            return $P ? parent::insert($Q, $P) : queries('INSERT INTO '.table($Q)." ()\nVALUES ()");
        }

        public function insertUpdate($Q, $M, $Te)
        {
            $d = array_keys(reset($M));
            $Re = 'INSERT INTO '.table($Q).' ('.implode(', ', $d).") VALUES\n";
            $bh = [];
            foreach ($d
            as$z) {
                $bh[$z] = "$z = VALUES($z)";
            }
            $bg = "\nON DUPLICATE KEY UPDATE ".implode(', ', $bh);
            $bh = [];
            $sd = 0;
            foreach ($M
            as$P) {
                $Y = '('.implode(', ', $P).')';
                if ($bh && (strlen($Re) + $sd + strlen($Y) + strlen($bg) > 1e6)) {
                    if (! queries($Re.implode(",\n", $bh).$bg)) {
                        return
                        false;
                    }
                    $bh = [];
                    $sd = 0;
                }
                $bh[] = $Y;
                $sd += strlen($Y) + 2;
            }

            return
                        queries($Re.implode(",\n", $bh).$bg);
        }

        public function convertSearch($Qc, $X, $l)
        {
            return preg_match('~char|text|enum|set~', $l['type']) && ! preg_match('~^utf8~', $l['collation']) ? "CONVERT($Qc USING ".charset($this->_conn).')' : $Qc;
        }

        public function warnings()
        {
            $J = $this->_conn->query('SHOW WARNINGS');
            if ($J && $J->num_rows) {
                ob_start();
                select($J);

                return
                ob_get_clean();
            }
        }

        public function tableHelp($E)
        {
            $yd = preg_match('~MariaDB~', $this->_conn->server_info);
            if (information_schema(DB)) {
                return
                strtolower(($yd ? "information-schema-$E-table/" : str_replace('_', '-', $E).'-table.html'));
            }
            if (DB == 'mysql') {
                return $yd ? "mysql$E-table/" : 'system-database.html';
            }
        }
    }
    function idf_escape($Qc)
    {
        return'`'.str_replace('`', '``', $Qc).'`';
    }
    function table($Qc)
    {
        return
        idf_escape($Qc);
    }
    function connect()
    {
        global$b,$Lg,$Yf;
        $e = new
        Min_DB;
        $mb = $b->credentials();
        if ($e->connect($mb[0], $mb[1], $mb[2])) {
            $e->set_charset(charset($e));
            $e->query('SET sql_quote_show_create = 1, autocommit = 1');
            if (min_version('5.7.8', 10.2, $e)) {
                $Yf['Strings'][] = 'json';
                $Lg['json'] = 4294967295;
            }

            return$e;
        }
        $K = $e->error;
        if (function_exists('iconv') && ! is_utf8($K) && strlen($xf = iconv('windows-1250', 'utf-8', $K)) > strlen($K)) {
            $K = $xf;
        }

        return$K;
    }
    function get_databases($vc)
    {
        $K = get_session('dbs');
        if ($K === null) {
            $I = (min_version(5) ? 'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA' : 'SHOW DATABASES');
            $K = ($vc ? slow_query($I) : get_vals($I));
            restart_session();
            set_session('dbs', $K);
            stop_session();
        }

        return$K;
    }
    function limit($I, $Z, $_, $Zd = 0, $Ff = ' ')
    {
        return" $I$Z".($_ !== null ? $Ff."LIMIT $_".($Zd ? " OFFSET $Zd" : '') : '');
    }
    function limit1($Q, $I, $Z, $Ff = "\n")
    {
        return
        limit($I, $Z, 1, 0, $Ff);
    }
    function db_collation($i, $Xa)
    {
        global$e;
        $K = null;
        $g = $e->result('SHOW CREATE DATABASE '.idf_escape($i), 1);
        if (preg_match('~ COLLATE ([^ ]+)~', $g, $C)) {
            $K = $C[1];
        } elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $g, $C)) {
            $K = $Xa[$C[1]][-1];
        }

        return$K;
    }
    function engines()
    {
        $K = [];
        foreach (get_rows('SHOW ENGINES')as$L) {
            if (preg_match('~YES|DEFAULT~', $L['Support'])) {
                $K[] = $L['Engine'];
            }
        }

        return$K;
    }
    function logged_user()
    {
        global$e;

        return$e->result('SELECT USER()');
    }
    function tables_list()
    {
        return
        get_key_vals(min_version(5) ? 'SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME' : 'SHOW TABLES');
    }
    function count_tables($h)
    {
        $K = [];
        foreach ($h
        as$i) {
            $K[$i] = count(get_vals('SHOW TABLES IN '.idf_escape($i)));
        }

        return$K;
    }
    function table_status($E = '', $oc = false)
    {
        $K = [];
        foreach (get_rows($oc && min_version(5) ? 'SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() '.($E != '' ? 'AND TABLE_NAME = '.q($E) : 'ORDER BY Name') : 'SHOW TABLE STATUS'.($E != '' ? ' LIKE '.q(addcslashes($E, '%_\\')) : ''))as$L) {
            if ($L['Engine'] == 'InnoDB') {
                $L['Comment'] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\\1', $L['Comment']);
            }
            if (! isset($L['Engine'])) {
                $L['Comment'] = '';
            }
            if ($E != '') {
                return$L;
            }
            $K[$L['Name']] = $L;
        }

        return$K;
    }
    function is_view($R)
    {
        return$R['Engine'] === null;
    }
    function fk_support($R)
    {
        return
        preg_match('~InnoDB|IBMDB2I~i', $R['Engine']) || (preg_match('~NDB~i', $R['Engine']) && min_version(5.6));
    }
    function fields($Q)
    {
        $K = [];
        foreach (get_rows('SHOW FULL COLUMNS FROM '.table($Q))as$L) {
            preg_match('~^([^( ]+)(?:\\((.+)\\))?( unsigned)?( zerofill)?$~', $L['Type'], $C);
            $K[$L['Field']] = ['field' => $L['Field'], 'full_type' => $L['Type'], 'type' => $C[1], 'length' => $C[2], 'unsigned' => ltrim($C[3].$C[4]), 'default' => ($L['Default'] != '' || preg_match('~char|set~', $C[1]) ? $L['Default'] : null), 'null' => ($L['Null'] == 'YES'), 'auto_increment' => ($L['Extra'] == 'auto_increment'), 'on_update' => (preg_match('~^on update (.+)~i', $L['Extra'], $C) ? $C[1] : ''), 'collation' => $L['Collation'], 'privileges' => array_flip(preg_split('~, *~', $L['Privileges'])), 'comment' => $L['Comment'], 'primary' => ($L['Key'] == 'PRI')];
        }

        return$K;
    }
    function indexes($Q, $f = null)
    {
        $K = [];
        foreach (get_rows('SHOW INDEX FROM '.table($Q), $f)as$L) {
            $E = $L['Key_name'];
            $K[$E]['type'] = ($E == 'PRIMARY' ? 'PRIMARY' : ($L['Index_type'] == 'FULLTEXT' ? 'FULLTEXT' : ($L['Non_unique'] ? ($L['Index_type'] == 'SPATIAL' ? 'SPATIAL' : 'INDEX') : 'UNIQUE')));
            $K[$E]['columns'][] = $L['Column_name'];
            $K[$E]['lengths'][] = ($L['Index_type'] == 'SPATIAL' ? null : $L['Sub_part']);
            $K[$E]['descs'][] = null;
        }

        return$K;
    }
    function foreign_keys($Q)
    {
        global$e,$ge;
        static$Je = '`(?:[^`]|``)+`';
        $K = [];
        $kb = $e->result('SHOW CREATE TABLE '.table($Q), 1);
        if ($kb) {
            preg_match_all("~CONSTRAINT ($Je) FOREIGN KEY ?\\(((?:$Je,? ?)+)\\) REFERENCES ($Je)(?:\\.($Je))? \\(((?:$Je,? ?)+)\\)(?: ON DELETE ($ge))?(?: ON UPDATE ($ge))?~", $kb, $_d, PREG_SET_ORDER);
            foreach ($_d
            as$C) {
                preg_match_all("~$Je~", $C[2], $Nf);
                preg_match_all("~$Je~", $C[5], $ng);
                $K[idf_unescape($C[1])] = ['db' => idf_unescape($C[4] != '' ? $C[3] : $C[4]), 'table' => idf_unescape($C[4] != '' ? $C[4] : $C[3]), 'source' => array_map('idf_unescape', $Nf[0]), 'target' => array_map('idf_unescape', $ng[0]), 'on_delete' => ($C[6] ? $C[6] : 'RESTRICT'), 'on_update' => ($C[7] ? $C[7] : 'RESTRICT')];
            }
        }

        return$K;
    }
    function view($E)
    {
        global$e;

        return
        ['select' => preg_replace('~^(?:[^`]|`[^`]*`)*\\s+AS\\s+~isU', '', $e->result('SHOW CREATE VIEW '.table($E), 1))];
    }
    function collations()
    {
        $K = [];
        foreach (get_rows('SHOW COLLATION')as$L) {
            if ($L['Default']) {
                $K[$L['Charset']][-1] = $L['Collation'];
            } else {
                $K[$L['Charset']][] = $L['Collation'];
            }
        }
        ksort($K);
        foreach ($K
        as$z => $X) {
            asort($K[$z]);
        }

        return$K;
    }
    function information_schema($i)
    {
        return(min_version(5) && $i == 'information_schema') || (min_version(5.5) && $i == 'performance_schema');
    }
    function error()
    {
        global$e;

        return
        h(preg_replace('~^You have an error.*syntax to use~U', 'Syntax error', $e->error));
    }
    function create_database($i, $Wa)
    {
        return
        queries('CREATE DATABASE '.idf_escape($i).($Wa ? ' COLLATE '.q($Wa) : ''));
    }
    function drop_databases($h)
    {
        $K = apply_queries('DROP DATABASE', $h, 'idf_escape');
        restart_session();
        set_session('dbs', null);

        return$K;
    }
    function rename_database($E, $Wa)
    {
        $K = false;
        if (create_database($E, $Wa)) {
            $nf = [];
            foreach (tables_list()as$Q => $U) {
                $nf[] = table($Q).' TO '.idf_escape($E).'.'.table($Q);
            }
            $K = (! $nf || queries('RENAME TABLE '.implode(', ', $nf)));
            if ($K) {
                queries('DROP DATABASE '.idf_escape(DB));
            }
            restart_session();
            set_session('dbs', null);
        }

        return$K;
    }
    function auto_increment()
    {
        $za = ' PRIMARY KEY';
        if ($_GET['create'] != '' && $_POST['auto_increment_col']) {
            foreach (indexes($_GET['create'])as$v) {
                if (in_array($_POST['fields'][$_POST['auto_increment_col']]['orig'], $v['columns'], true)) {
                    $za = '';
                    break;
                }
                if ($v['type'] == 'PRIMARY') {
                    $za = ' UNIQUE';
                }
            }
        }

        return" AUTO_INCREMENT$za";
    }
    function alter_table($Q, $E, $m, $wc, $bb, $Xb, $Wa, $ya, $Fe)
    {
        $sa = [];
        foreach ($m
        as$l) {
            $sa[] = ($l[1] ? ($Q != '' ? ($l[0] != '' ? 'CHANGE '.idf_escape($l[0]) : 'ADD') : ' ').' '.implode($l[1]).($Q != '' ? $l[2] : '') : 'DROP '.idf_escape($l[0]));
        }
        $sa = array_merge($sa, $wc);
        $Uf = ($bb !== null ? ' COMMENT='.q($bb) : '').($Xb ? ' ENGINE='.q($Xb) : '').($Wa ? ' COLLATE '.q($Wa) : '').($ya != '' ? " AUTO_INCREMENT=$ya" : '');
        if ($Q == '') {
            return
            queries('CREATE TABLE '.table($E)." (\n".implode(",\n", $sa)."\n)$Uf$Fe");
        }
        if ($Q != $E) {
            $sa[] = 'RENAME TO '.table($E);
        }
        if ($Uf) {
            $sa[] = ltrim($Uf);
        }

        return $sa || $Fe ? queries('ALTER TABLE '.table($Q)."\n".implode(",\n", $sa).$Fe) : true;
    }
    function alter_indexes($Q, $sa)
    {
        foreach ($sa
        as$z => $X) {
            $sa[$z] = ($X[2] == 'DROP' ? "\nDROP INDEX ".idf_escape($X[1]) : "\nADD $X[0] ".($X[0] == 'PRIMARY' ? 'KEY ' : '').($X[1] != '' ? idf_escape($X[1]).' ' : '').'('.implode(', ', $X[2]).')');
        }

        return
                queries('ALTER TABLE '.table($Q).implode(',', $sa));
    }
    function truncate_tables($S)
    {
        return
        apply_queries('TRUNCATE TABLE', $S);
    }
    function drop_views($gh)
    {
        return
        queries('DROP VIEW '.implode(', ', array_map('table', $gh)));
    }
    function drop_tables($S)
    {
        return
        queries('DROP TABLE '.implode(', ', array_map('table', $S)));
    }
    function move_tables($S, $gh, $ng)
    {
        $nf = [];
        foreach (array_merge($S, $gh)as$Q) {
            $nf[] = table($Q).' TO '.idf_escape($ng).'.'.table($Q);
        }

        return
                queries('RENAME TABLE '.implode(', ', $nf));
    }
    function copy_tables($S, $gh, $ng)
    {
        queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
        foreach ($S
        as$Q) {
            $E = ($ng == DB ? table("copy_$Q") : idf_escape($ng).'.'.table($Q));
            if (! queries("\nDROP TABLE IF EXISTS $E") || ! queries("CREATE TABLE $E LIKE ".table($Q)) || ! queries("INSERT INTO $E SELECT * FROM ".table($Q))) {
                return
                false;
            }
        }
        foreach ($gh
        as$Q) {
            $E = ($ng == DB ? table("copy_$Q") : idf_escape($ng).'.'.table($Q));
            $fh = view($Q);
            if (! queries("DROP VIEW IF EXISTS $E") || ! queries("CREATE VIEW $E AS $fh[select]")) {
                return
                false;
            }
        }

        return
                true;
    }
    function trigger($E)
    {
        if ($E == '') {
            return
            [];
        }
        $M = get_rows('SHOW TRIGGERS WHERE `Trigger` = '.q($E));

        return
        reset($M);
    }
    function triggers($Q)
    {
        $K = [];
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($Q, '%_\\')))as$L) {
            $K[$L['Trigger']] = [$L['Timing'], $L['Event']];
        }

        return$K;
    }
    function trigger_options()
    {
        return
        ['Timing' => ['BEFORE', 'AFTER'], 'Event' => ['INSERT', 'UPDATE', 'DELETE'], 'Type' => ['FOR EACH ROW']];
    }
    function routine($E, $U)
    {
        global$e,$Zb,$Vc,$Lg;
        $qa = ['bool', 'boolean', 'integer', 'double precision', 'real', 'dec', 'numeric', 'fixed', 'national char', 'national varchar'];
        $Of = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        $Kg = '(('.implode('|', array_merge(array_keys($Lg), $qa)).")\\b(?:\\s*\\(((?:[^'\")]|$Zb)++)\\))?\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";
        $Je = "$Of*(".($U == 'FUNCTION' ? '' : $Vc).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Kg";
        $g = $e->result("SHOW CREATE $U ".idf_escape($E), 2);
        preg_match("~\\(((?:$Je\\s*,?)*)\\)\\s*".($U == 'FUNCTION' ? "RETURNS\\s+$Kg\\s+" : '').'(.*)~is', $g, $C);
        $m = [];
        preg_match_all("~$Je\\s*,?~is", $C[1], $_d, PREG_SET_ORDER);
        foreach ($_d
        as$Ae) {
            $E = str_replace('``', '`', $Ae[2]).$Ae[3];
            $m[] = ['field' => $E, 'type' => strtolower($Ae[5]), 'length' => preg_replace_callback("~$Zb~s", 'normalize_enum', $Ae[6]), 'unsigned' => strtolower(preg_replace('~\\s+~', ' ', trim("$Ae[8] $Ae[7]"))), 'null' => 1, 'full_type' => $Ae[4], 'inout' => strtoupper($Ae[1]), 'collation' => strtolower($Ae[9])];
        }
        if ($U != 'FUNCTION') {
            return
            ['fields' => $m, 'definition' => $C[11]];
        }

        return
                ['fields' => $m, 'returns' => ['type' => $C[12], 'length' => $C[13], 'unsigned' => $C[15], 'collation' => $C[16]], 'definition' => $C[17], 'language' => 'SQL'];
    }
    function routines()
    {
        return
        get_rows('SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '.q(DB));
    }
    function routine_languages()
    {
        return
        [];
    }
    function routine_id($E, $L)
    {
        return
        idf_escape($E);
    }
    function last_id()
    {
        global$e;

        return$e->result('SELECT LAST_INSERT_ID()');
    }
    function explain($e, $I)
    {
        return$e->query('EXPLAIN '.(min_version(5.1) ? 'PARTITIONS ' : '').$I);
    }
    function found_rows($R, $Z)
    {
        return $Z || $R['Engine'] != 'InnoDB' ? null : $R['Rows'];
    }
    function types()
    {
        return
        [];
    }
    function schemas()
    {
        return
        [];
    }
    function get_schema()
    {
        return'';
    }
    function set_schema($zf)
    {
        return
        true;
    }
    function create_sql($Q, $ya, $Zf)
    {
        global$e;
        $K = $e->result('SHOW CREATE TABLE '.table($Q), 1);
        if (! $ya) {
            $K = preg_replace('~ AUTO_INCREMENT=\\d+~', '', $K);
        }

        return$K;
    }
    function truncate_sql($Q)
    {
        return'TRUNCATE '.table($Q);
    }
    function use_sql($tb)
    {
        return'USE '.idf_escape($tb);
    }
    function trigger_sql($Q)
    {
        $K = '';
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($Q, '%_\\')), null, '-- ')as$L) {
            $K .= "\nCREATE TRIGGER ".idf_escape($L['Trigger'])." $L[Timing] $L[Event] ON ".table($L['Table'])." FOR EACH ROW\n$L[Statement];;\n";
        }

        return$K;
    }
    function show_variables()
    {
        return
        get_key_vals('SHOW VARIABLES');
    }
    function process_list()
    {
        return
        get_rows('SHOW FULL PROCESSLIST');
    }
    function show_status()
    {
        return
        get_key_vals('SHOW STATUS');
    }
    function convert_field($l)
    {
        if (preg_match('~binary~', $l['type'])) {
            return'HEX('.idf_escape($l['field']).')';
        }
        if ($l['type'] == 'bit') {
            return'BIN('.idf_escape($l['field']).' + 0)';
        }
        if (preg_match('~geometry|point|linestring|polygon~', $l['type'])) {
            return(min_version(8) ? 'ST_' : '').'AsWKT('.idf_escape($l['field']).')';
        }
    }
    function unconvert_field($l, $K)
    {
        if (preg_match('~binary~', $l['type'])) {
            $K = "UNHEX($K)";
        }
        if ($l['type'] == 'bit') {
            $K = "CONV($K, 2, 10) + 0";
        }
        if (preg_match('~geometry|point|linestring|polygon~', $l['type'])) {
            $K = (min_version(8) ? 'ST_' : '')."GeomFromText($K)";
        }

        return$K;
    }
    function support($pc)
    {
        return! preg_match('~scheme|sequence|type|view_trigger|materializedview'.(min_version(5.1) ? '' : '|event|partitioning'.(min_version(5) ? '' : '|routine|trigger|view')).'~', $pc);
    }
    function kill_process($X)
    {
        return
        queries('KILL '.number($X));
    }
    function connection_id()
    {
        return'SELECT CONNECTION_ID()';
    }
    function max_connections()
    {
        global$e;

        return$e->result('SELECT @@max_connections');
    }
    $y = 'sql';
    $Lg = [];
    $Yf = [];
    foreach (['Numbers' => ['tinyint' => 3, 'smallint' => 5, 'mediumint' => 8, 'int' => 10, 'bigint' => 20, 'decimal' => 66, 'float' => 12, 'double' => 21], 'Date and time' => ['date' => 10, 'datetime' => 19, 'timestamp' => 19, 'time' => 10, 'year' => 4], 'Strings' => ['char' => 255, 'varchar' => 65535, 'tinytext' => 255, 'text' => 65535, 'mediumtext' => 16777215, 'longtext' => 4294967295], 'Lists' => ['enum' => 65535, 'set' => 64], 'Binary' => ['bit' => 20, 'binary' => 255, 'varbinary' => 65535, 'tinyblob' => 255, 'blob' => 65535, 'mediumblob' => 16777215, 'longblob' => 4294967295], 'Geometry' => ['geometry' => 0, 'point' => 0, 'linestring' => 0, 'polygon' => 0, 'multipoint' => 0, 'multilinestring' => 0, 'multipolygon' => 0, 'geometrycollection' => 0]]as$z => $X) {
        $Lg += $X;
        $Yf[$z] = array_keys($X);
    }
    $Sg = ['unsigned', 'zerofill', 'unsigned zerofill'];
    $ke = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'REGEXP', 'IN', 'FIND_IN_SET', 'IS NULL', 'NOT LIKE', 'NOT REGEXP', 'NOT IN', 'IS NOT NULL', 'SQL'];
    $Bc = ['char_length', 'date', 'from_unixtime', 'lower', 'round', 'floor', 'ceil', 'sec_to_time', 'time_to_sec', 'upper'];
    $Fc = ['avg', 'count', 'count distinct', 'group_concat', 'max', 'min', 'sum'];
    $Pb = [['char' => 'md5/sha1/password/encrypt/uuid', 'binary' => 'md5/sha1', 'date|time' => 'now'], [number_type() => '+/-', 'date' => '+ interval/- interval', 'time' => 'addtime/subtime', 'char|text' => 'concat']];
}define('SERVER', $_GET[DRIVER]);
define('DB', $_GET['db']);
define('ME', preg_replace('~^[^?]*/([^?]*).*~', '\\1', $_SERVER['REQUEST_URI']).'?'.(sid() ? SID.'&' : '').(SERVER !== null ? DRIVER.'='.urlencode(SERVER).'&' : '').(isset($_GET['username']) ? 'username='.urlencode($_GET['username']).'&' : '').(DB != '' ? 'db='.urlencode(DB).'&'.(isset($_GET['ns']) ? 'ns='.urlencode($_GET['ns']).'&' : '') : ''));
$ga = '4.6.2';
class Adminer
{
    public $operators;

    public function name()
    {
        return"<a href='https://www.adminer.org/'".target_blank()." id='h1'>Adminer</a>";
    }

    public function credentials()
    {
        return
        [SERVER, $_GET['username'], get_password()];
    }

    public function connectSsl()
    {
    }

    public function permanentLogin($g = false)
    {
        return
        password_file($g);
    }

    public function bruteForceKey()
    {
        return$_SERVER['REMOTE_ADDR'];
    }

    public function serverName($O)
    {
        return
        h($O);
    }

    public function database()
    {
        return
        DB;
    }

    public function databases($vc = true)
    {
        return
        get_databases($vc);
    }

    public function schemas()
    {
        return
        schemas();
    }

    public function queryTimeout()
    {
        return
        5;
    }

    public function headers()
    {
    }

    public function csp()
    {
        return
        csp();
    }

    public function head()
    {
        return
        true;
    }

    public function css()
    {
        $K = [];
        $sc = 'adminer.css';
        if (file_exists($sc)) {
            $K[] = $sc;
        }

        return$K;
    }

    public function loginForm()
    {
        global$Ib; ?>
<table cellspacing="0">
<tr><th>System<td><input type='hidden' name='auth[driver]' value='server'>MySQL
<tr><th>Server<td><input name="auth[server]" value="<?php echo
        h(SERVER),'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">
<tr><th>Username<td><input name="auth[username]" id="username" value="',h($_GET['username']),'" autocapitalize="off">
<tr><th>Password<td><input type="password" name="auth[password]">
<tr><th>Database<td><input name="auth[db]" value="',h($_GET['db']),'" autocapitalize="off">
</table>
',script("focus(qs('#username'));"),"<p><input type='submit' value='".'Login'."'>\n",checkbox('auth[permanent]', 1, $_COOKIE['adminer_permanent'], 'Permanent login')."\n";
    }

    public function login($wd, $G)
    {
        global$y;
        if ($y == 'sqlite') {
            return
            sprintf('<a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to use SQLite.', target_blank(), '<code>login()</code>');
        }

        return
                true;
    }

    public function tableName($fg)
    {
        return
        h($fg['Name']);
    }

    public function fieldName($l, $oe = 0)
    {
        return'<span title="'.h($l['full_type']).'">'.h($l['field']).'</span>';
    }

    public function selectLinks($fg, $P = '')
    {
        global$y,$j;
        echo'<p class="links">';
        $vd = ['select' => 'Select data'];
        if (support('table') || support('indexes')) {
            $vd['table'] = 'Show structure';
        }
        if (support('table')) {
            if (is_view($fg)) {
                $vd['view'] = 'Alter view';
            } else {
                $vd['create'] = 'Alter table';
            }
        }
        if ($P !== null) {
            $vd['edit'] = 'New item';
        }
        $E = $fg['Name'];
        foreach ($vd
        as$z => $X) {
            echo" <a href='".h(ME)."$z=".urlencode($E).($z == 'edit' ? $P : '')."'".bold(isset($_GET[$z])).">$X</a>";
        }
        echo
        doc_link([$y => $j->tableHelp($E)], '?'),"\n";
    }

    public function foreignKeys($Q)
    {
        return
        foreign_keys($Q);
    }

    public function backwardKeys($Q, $eg)
    {
        return
        [];
    }

    public function backwardKeysPrint($Aa, $L)
    {
    }

    public function selectQuery($I, $Tf, $nc = false)
    {
        global$y,$j;
        $K = "</p>\n";
        if (! $nc && ($jh = $j->warnings())) {
            $u = 'warnings';
            $K = ", <a href='#$u'>".'Warnings'.'</a>'.script("qsl('a').onclick = partial(toggle, '$u');", '')."$K<div id='$u' class='hidden'>\n$jh</div>\n";
        }

        return"<p><code class='jush-$y'>".h(str_replace("\n", ' ', $I))."</code> <span class='time'>(".format_time($Tf).')</span>'.(support('sql') ? " <a href='".h(ME).'sql='.urlencode($I)."'>".'Edit'.'</a>' : '').$K;
    }

    public function sqlCommandQuery($I)
    {
        return
        shorten_utf8(trim($I), 1000);
    }

    public function rowDescription($Q)
    {
        return'';
    }

    public function rowDescriptions($M, $xc)
    {
        return$M;
    }

    public function selectLink($X, $l)
    {
    }

    public function selectVal($X, $A, $l, $we)
    {
        $K = ($X === null ? '<i>NULL</i>' : (preg_match('~char|binary|boolean~', $l['type']) && ! preg_match('~var~', $l['type']) ? "<code>$X</code>" : $X));
        if (preg_match('~blob|bytea|raw|file~', $l['type']) && ! is_utf8($X)) {
            $K = '<i>'.lang(['%d byte', '%d bytes'], strlen($we)).'</i>';
        }
        if (preg_match('~json~', $l['type'])) {
            $K = "<code class='jush-js'>$K</code>";
        }

        return $A ? "<a href='".h($A)."'".(is_url($A) ? target_blank() : '').">$K</a>" : $K;
    }

    public function editVal($X, $l)
    {
        return$X;
    }

    public function tableStructurePrint($m)
    {
        echo"<table cellspacing='0' class='nowrap'>\n",'<thead><tr><th>'.'Column'.'<td>'.'Type'.(support('comment') ? '<td>'.'Comment' : '')."</thead>\n";
        foreach ($m
        as$l) {
            echo'<tr'.odd().'><th>'.h($l['field']),"<td><span title='".h($l['collation'])."'>".h($l['full_type']).'</span>',($l['null'] ? ' <i>NULL</i>' : ''),($l['auto_increment'] ? ' <i>'.'Auto Increment'.'</i>' : ''),(isset($l['default']) ? " <span title='".'Default value'."'>[<b>".h($l['default']).'</b>]</span>' : ''),(support('comment') ? '<td>'.nbsp($l['comment']) : ''),"\n";
        }
        echo"</table>\n";
    }

    public function tableIndexesPrint($w)
    {
        echo"<table cellspacing='0'>\n";
        foreach ($w
        as$E => $v) {
            ksort($v['columns']);
            $Ue = [];
            foreach ($v['columns']as$z => $X) {
                $Ue[] = '<i>'.h($X).'</i>'.($v['lengths'][$z] ? '('.$v['lengths'][$z].')' : '').($v['descs'][$z] ? ' DESC' : '');
            }
            echo"<tr title='".h($E)."'><th>$v[type]<td>".implode(', ', $Ue)."\n";
        }
        echo"</table>\n";
    }

    public function selectColumnsPrint($N, $d)
    {
        global$Bc,$Fc;
        print_fieldset('select', 'Select', $N);
        $t = 0;
        $N[''] = [];
        foreach ($N
        as$z => $X) {
            $X = $_GET['columns'][$z];
            $c = select_input(" name='columns[$t][col]'", $d, $X['col'], ($z !== '' ? 'selectFieldChange' : 'selectAddRow'));
            echo'<div>'.($Bc || $Fc ? "<select name='columns[$t][fun]'>".optionlist([-1 => ''] + array_filter(['Functions' => $Bc, 'Aggregation' => $Fc]), $X['fun']).'</select>'.on_help("getTarget(event).value && getTarget(event).value.replace(/ |\$/, '(') + ')'", 1).script("qsl('select').onchange = function () { helpClose();".($z !== '' ? '' : " qsl('select, input', this.parentNode).onchange();").' };', '')."($c)" : $c)."</div>\n";
            $t++;
        }
        echo"</div></fieldset>\n";
    }

    public function selectSearchPrint($Z, $d, $w)
    {
        print_fieldset('search', 'Search', $Z);
        foreach ($w
        as$t => $v) {
            if ($v['type'] == 'FULLTEXT') {
                echo'<div>(<i>'.implode('</i>, <i>', array_map('h', $v['columns'])).'</i>) AGAINST'," <input type='search' name='fulltext[$t]' value='".h($_GET['fulltext'][$t])."'>",script("qsl('input').oninput = selectFieldChange;", ''),checkbox("boolean[$t]", 1, isset($_GET['boolean'][$t]), 'BOOL'),"</div>\n";
            }
        }
        $Ka = 'this.parentNode.firstChild.onchange();';
        foreach (array_merge((array) $_GET['where'], [[]])as$t => $X) {
            if (! $X || ("$X[col]$X[val]" != '' && in_array($X['op'], $this->operators))) {
                echo'<div>'.select_input(" name='where[$t][col]'", $d, $X['col'], ($X ? 'selectFieldChange' : 'selectAddRow'), '('.'anywhere'.')'),html_select("where[$t][op]", $this->operators, $X['op'], $Ka),"<input type='search' name='where[$t][val]' value='".h($X['val'])."'>",script("mixin(qsl('input'), {oninput: function () { $Ka }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ''),"</div>\n";
            }
        }
        echo"</div></fieldset>\n";
    }

    public function selectOrderPrint($oe, $d, $w)
    {
        print_fieldset('sort', 'Sort', $oe);
        $t = 0;
        foreach ((array) $_GET['order']as$z => $X) {
            if ($X != '') {
                echo'<div>'.select_input(" name='order[$t]'", $d, $X, 'selectFieldChange'),checkbox("desc[$t]", 1, isset($_GET['desc'][$z]), 'descending')."</div>\n";
                $t++;
            }
        }
        echo'<div>'.select_input(" name='order[$t]'", $d, '', 'selectAddRow'),checkbox("desc[$t]", 1, false, 'descending')."</div>\n","</div></fieldset>\n";
    }

    public function selectLimitPrint($_)
    {
        echo'<fieldset><legend>'.'Limit'.'</legend><div>';
        echo"<input type='number' name='limit' class='size' value='".h($_)."'>",script("qsl('input').oninput = selectFieldChange;", ''),"</div></fieldset>\n";
    }

    public function selectLengthPrint($sg)
    {
        if ($sg !== null) {
            echo'<fieldset><legend>'.'Text length'.'</legend><div>',"<input type='number' name='text_length' class='size' value='".h($sg)."'>","</div></fieldset>\n";
        }
    }

    public function selectActionPrint($w)
    {
        echo'<fieldset><legend>'.'Action'.'</legend><div>',"<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>",'<script'.nonce().">\n",'var indexColumns = ';
        $d = [];
        foreach ($w
        as$v) {
            $qb = reset($v['columns']);
            if ($v['type'] != 'FULLTEXT' && $qb) {
                $d[$qb] = 1;
            }
        }
        $d[''] = 1;
        foreach ($d
        as$z => $X) {
            json_row($z);
        }
        echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";
    }

    public function selectCommandPrint()
    {
        return! information_schema(DB);
    }

    public function selectImportPrint()
    {
        return! information_schema(DB);
    }

    public function selectEmailPrint($Ub, $d)
    {
    }

    public function selectColumnsProcess($d, $w)
    {
        global$Bc,$Fc;
        $N = [];
        $s = [];
        foreach ((array) $_GET['columns']as$z => $X) {
            if ($X['fun'] == 'count' || ($X['col'] != '' && (! $X['fun'] || in_array($X['fun'], $Bc) || in_array($X['fun'], $Fc)))) {
                $N[$z] = apply_sql_function($X['fun'], ($X['col'] != '' ? idf_escape($X['col']) : '*'));
                if (! in_array($X['fun'], $Fc)) {
                    $s[] = $N[$z];
                }
            }
        }

        return
                [$N, $s];
    }

    public function selectSearchProcess($m, $w)
    {
        global$e,$j;
        $K = [];
        foreach ($w
        as$t => $v) {
            if ($v['type'] == 'FULLTEXT' && $_GET['fulltext'][$t] != '') {
                $K[] = 'MATCH ('.implode(', ', array_map('idf_escape', $v['columns'])).') AGAINST ('.q($_GET['fulltext'][$t]).(isset($_GET['boolean'][$t]) ? ' IN BOOLEAN MODE' : '').')';
            }
        }
        foreach ((array) $_GET['where']as$z => $X) {
            if ("$X[col]$X[val]" != '' && in_array($X['op'], $this->operators)) {
                $Re = '';
                $db = " $X[op]";
                if (preg_match('~IN$~', $X['op'])) {
                    $Sc = process_length($X['val']);
                    $db .= ' '.($Sc != '' ? $Sc : '(NULL)');
                } elseif ($X['op'] == 'SQL') {
                    $db = " $X[val]";
                } elseif ($X['op'] == 'LIKE %%') {
                    $db = ' LIKE '.$this->processInput($m[$X['col']], "%$X[val]%");
                } elseif ($X['op'] == 'ILIKE %%') {
                    $db = ' ILIKE '.$this->processInput($m[$X['col']], "%$X[val]%");
                } elseif ($X['op'] == 'FIND_IN_SET') {
                    $Re = "$X[op](".q($X['val']).', ';
                    $db = ')';
                } elseif (! preg_match('~NULL$~', $X['op'])) {
                    $db .= ' '.$this->processInput($m[$X['col']], $X['val']);
                }
                if ($X['col'] != '') {
                    $K[] = $Re.$j->convertSearch(idf_escape($X['col']), $X, $m[$X['col']]).$db;
                } else {
                    $Ya = [];
                    foreach ($m
                    as$E => $l) {
                        if ((is_numeric($X['val']) || ! preg_match('~'.number_type().'|bit~', $l['type'])) && (! preg_match("~[\x80-\xFF]~", $X['val']) || preg_match('~char|text|enum|set~', $l['type']))) {
                            $Ya[] = $Re.$j->convertSearch(idf_escape($E), $X, $l).$db;
                        }
                    }
                    $K[] = ($Ya ? '('.implode(' OR ', $Ya).')' : '1 = 0');
                }
            }
        }

        return$K;
    }

    public function selectOrderProcess($m, $w)
    {
        $K = [];
        foreach ((array) $_GET['order']as$z => $X) {
            if ($X != '') {
                $K[] = (preg_match('~^((COUNT\\(DISTINCT |[A-Z0-9_]+\\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\\)|COUNT\\(\\*\\))$~', $X) ? $X : idf_escape($X)).(isset($_GET['desc'][$z]) ? ' DESC' : '');
            }
        }

        return$K;
    }

    public function selectLimitProcess()
    {
        return isset($_GET['limit']) ? $_GET['limit'] : '50';
    }

    public function selectLengthProcess()
    {
        return isset($_GET['text_length']) ? $_GET['text_length'] : '100';
    }

    public function selectEmailProcess($Z, $xc)
    {
        return
        false;
    }

    public function selectQueryBuild($N, $Z, $s, $oe, $_, $F)
    {
        return'';
    }

    public function messageQuery($I, $tg, $nc = false)
    {
        global$y,$j;
        restart_session();
        $Lc = &get_session('queries');
        if (! $Lc[$_GET['db']]) {
            $Lc[$_GET['db']] = [];
        }
        if (strlen($I) > 1e6) {
            $I = preg_replace('~[\x80-\xFF]+$~', '', substr($I, 0, 1e6))."\n...";
        }
        $Lc[$_GET['db']][] = [$I, time(), $tg];
        $Rf = 'sql-'.count($Lc[$_GET['db']]);
        $K = "<a href='#$Rf' class='toggle'>".'SQL command'."</a>\n";
        if (! $nc && ($jh = $j->warnings())) {
            $u = 'warnings-'.count($Lc[$_GET['db']]);
            $K = "<a href='#$u' class='toggle'>".'Warnings'."</a>, $K<div id='$u' class='hidden'>\n$jh</div>\n";
        }

        return" <span class='time'>".@date('H:i:s').'</span>'." $K<div id='$Rf' class='hidden'><pre><code class='jush-$y'>".shorten_utf8($I, 1000).'</code></pre>'.($tg ? " <span class='time'>($tg)</span>" : '').(support('sql') ? '<p><a href="'.h(str_replace('db='.urlencode(DB), 'db='.urlencode($_GET['db']), ME).'sql=&history='.(count($Lc[$_GET['db']]) - 1)).'">'.'Edit'.'</a>' : '').'</div>';
    }

    public function editFunctions($l)
    {
        global$Pb;
        $K = ($l['null'] ? 'NULL/' : '');
        foreach ($Pb
        as$z => $Bc) {
            if (! $z || (! isset($_GET['call']) && (isset($_GET['select']) || where($_GET)))) {
                foreach ($Bc
                as$Je => $X) {
                    if (! $Je || preg_match("~$Je~", $l['type'])) {
                        $K .= "/$X";
                    }
                }
                if ($z && ! preg_match('~set|blob|bytea|raw|file~', $l['type'])) {
                    $K .= '/SQL';
                }
            }
        }
        if ($l['auto_increment'] && ! isset($_GET['select']) && ! where($_GET)) {
            $K = 'Auto Increment';
        }

        return
                explode('/', $K);
    }

    public function editInput($Q, $l, $wa, $Y)
    {
        if ($l['type'] == 'enum') {
            return(isset($_GET['select']) ? "<label><input type='radio'$wa value='-1' checked><i>".'original'.'</i></label> ' : '').($l['null'] ? "<label><input type='radio'$wa value=''".($Y !== null || isset($_GET['select']) ? '' : ' checked').'><i>NULL</i></label> ' : '').enum_input('radio', $wa, $l, $Y, 0);
        }

        return'';
    }

    public function editHint($Q, $l, $Y)
    {
        return'';
    }

    public function processInput($l, $Y, $q = '')
    {
        if ($q == 'SQL') {
            return$Y;
        }
        $E = $l['field'];
        $K = q($Y);
        if (preg_match('~^(now|getdate|uuid)$~', $q)) {
            $K = "$q()";
        } elseif (preg_match('~^current_(date|timestamp)$~', $q)) {
            $K = $q;
        } elseif (preg_match('~^([+-]|\\|\\|)$~', $q)) {
            $K = idf_escape($E)." $q $K";
        } elseif (preg_match('~^[+-] interval$~', $q)) {
            $K = idf_escape($E)." $q ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) ? $Y : $K);
        } elseif (preg_match('~^(addtime|subtime|concat)$~', $q)) {
            $K = "$q(".idf_escape($E).", $K)";
        } elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $q)) {
            $K = "$q($K)";
        }

        return
                unconvert_field($l, $K);
    }

    public function dumpOutput()
    {
        $K = ['text' => 'open', 'file' => 'save'];
        if (function_exists('gzencode')) {
            $K['gz'] = 'gzip';
        }

        return$K;
    }

    public function dumpFormat()
    {
        return
        ['sql' => 'SQL', 'csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV'];
    }

    public function dumpDatabase($i)
    {
    }

    public function dumpTable($Q, $Zf, $ed = 0)
    {
        if ($_POST['format'] != 'sql') {
            echo"\xef\xbb\xbf";
            if ($Zf) {
                dump_csv(array_keys(fields($Q)));
            }
        } else {
            if ($ed == 2) {
                $m = [];
                foreach (fields($Q)as$E => $l) {
                    $m[] = idf_escape($E)." $l[full_type]";
                }
                $g = 'CREATE TABLE '.table($Q).' ('.implode(', ', $m).')';
            } else {
                $g = create_sql($Q, $_POST['auto_increment'], $Zf);
            }
            set_utf8mb4($g);
            if ($Zf && $g) {
                if ($Zf == 'DROP+CREATE' || $ed == 1) {
                    echo'DROP '.($ed == 2 ? 'VIEW' : 'TABLE').' IF EXISTS '.table($Q).";\n";
                }
                if ($ed == 1) {
                    $g = remove_definer($g);
                }
                echo"$g;\n\n";
            }
        }
    }

    public function dumpData($Q, $Zf, $I)
    {
        global$e,$y;
        $Bd = ($y == 'sqlite' ? 0 : 1048576);
        if ($Zf) {
            if ($_POST['format'] == 'sql') {
                if ($Zf == 'TRUNCATE+INSERT') {
                    echo
                    truncate_sql($Q).";\n";
                }
                $m = fields($Q);
            }
            $J = $e->query($I, 1);
            if ($J) {
                $Xc = '';
                $Ia = '';
                $gd = [];
                $bg = '';
                $qc = ($Q != '' ? 'fetch_assoc' : 'fetch_row');
                while ($L = $J->$qc()) {
                    if (! $gd) {
                        $bh = [];
                        foreach ($L
                        as$X) {
                            $l = $J->fetch_field();
                            $gd[] = $l->name;
                            $z = idf_escape($l->name);
                            $bh[] = "$z = VALUES($z)";
                        }
                        $bg = ($Zf == 'INSERT+UPDATE' ? "\nON DUPLICATE KEY UPDATE ".implode(', ', $bh) : '').";\n";
                    }
                    if ($_POST['format'] != 'sql') {
                        if ($Zf == 'table') {
                            dump_csv($gd);
                            $Zf = 'INSERT';
                        }
                        dump_csv($L);
                    } else {
                        if (! $Xc) {
                            $Xc = 'INSERT INTO '.table($Q).' ('.implode(', ', array_map('idf_escape', $gd)).') VALUES';
                        }
                        foreach ($L
                        as$z => $X) {
                            $l = $m[$z];
                            $L[$z] = ($X !== null ? unconvert_field($l, preg_match(number_type(), $l['type']) && $X != '' ? $X : q($X)) : 'NULL');
                        }
                        $xf = ($Bd ? "\n" : ' ').'('.implode(",\t", $L).')';
                        if (! $Ia) {
                            $Ia = $Xc.$xf;
                        } elseif (strlen($Ia) + 4 + strlen($xf) + strlen($bg) < $Bd) {
                            $Ia .= ",$xf";
                        } else {
                            echo$Ia.$bg;
                            $Ia = $Xc.$xf;
                        }
                    }
                }
                if ($Ia) {
                    echo$Ia.$bg;
                }
            } elseif ($_POST['format'] == 'sql') {
                echo'-- '.str_replace("\n", ' ', $e->error)."\n";
            }
        }
    }

    public function dumpFilename($Pc)
    {
        return
        friendly_url($Pc != '' ? $Pc : (SERVER != '' ? SERVER : 'localhost'));
    }

    public function dumpHeaders($Pc, $Nd = false)
    {
        $ye = $_POST['output'];
        $kc = (preg_match('~sql~', $_POST['format']) ? 'sql' : ($Nd ? 'tar' : 'csv'));
        header('Content-Type: '.($ye == 'gz' ? 'application/x-gzip' : ($kc == 'tar' ? 'application/x-tar' : ($kc == 'sql' || $ye != 'file' ? 'text/plain' : 'text/csv').'; charset=utf-8')));
        if ($ye == 'gz') {
            ob_start('ob_gzencode', 1e6);
        }

        return$kc;
    }

    public function importServerPath()
    {
        return'adminer.sql';
    }

    public function homepage()
    {
        echo'<p class="links">'.($_GET['ns'] == '' && support('database') ? '<a href="'.h(ME).'database=">'.'Alter database'."</a>\n" : ''),(support('scheme') ? "<a href='".h(ME)."scheme='>".($_GET['ns'] != '' ? 'Alter schema' : 'Create schema')."</a>\n" : ''),($_GET['ns'] !== '' ? '<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n" : ''),(support('privileges') ? "<a href='".h(ME)."privileges='>".'Privileges'."</a>\n" : '');

        return
        true;
    }

    public function navigation($Md)
    {
        global$ga,$y,$Ib,$e;
        echo'<h1>
',$this->name(),' <span class="version">',$ga,'</span>
<a href="https://www.adminer.org/#download"',target_blank(),' id="version">',(version_compare($ga, $_COOKIE['adminer_version']) < 0 ? h($_COOKIE['adminer_version']) : ''),'</a>
</h1>
';
        if ($Md == 'auth') {
            $uc = true;
            foreach ((array) $_SESSION['pwds']as$dh => $Hf) {
                foreach ($Hf
                as$O => $Zg) {
                    foreach ($Zg
                    as$V => $G) {
                        if ($G !== null) {
                            if ($uc) {
                                echo"<p id='logins'>".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
                                $uc = false;
                            }
                            $wb = $_SESSION['db'][$dh][$O][$V];
                            foreach (($wb ? array_keys($wb) : [''])as$i) {
                                echo"<a href='".h(auth_url($dh, $O, $V, $i))."'>($Ib[$dh]) ".h($V.($O != '' ? '@'.$this->serverName($O) : '').($i != '' ? " - $i" : ''))."</a><br>\n";
                            }
                        }
                    }
                }
            }
        } else {
            if ($_GET['ns'] !== '' && ! $Md && DB != '') {
                $e->select_db(DB);
                $S = table_status('', true);
            }
            echo
            script_src(preg_replace('~\\?.*~', '', ME).'?file=jush.js&version=4.6.2&driver=mysql');
            if (support('sql')) {
                echo'<script',nonce(),'>
';
                if ($S) {
                    $vd = [];
                    foreach ($S
                    as$Q => $U) {
                        $vd[] = preg_quote($Q, '/');
                    }
                    echo"var jushLinks = { $y: [ '".js_escape(ME).(support('table') ? 'table=' : 'select=')."\$&', /\\b(".implode('|', $vd).")\\b/g ] };\n";
                    foreach (['bac', 'bra', 'sqlite_quo', 'mssql_bra']as$X) {
                        echo"jushLinks.$X = jushLinks.$y;\n";
                    }
                }
                $Gf = $e->server_info;
                echo'bodyLoad(\'',(is_object($e) ? preg_replace('~^(\\d\\.?\\d).*~s', '\\1', $Gf) : ''),'\'',(preg_match('~MariaDB~', $Gf) ? ', true' : ''),');
</script>
';
            }
            $this->databasesPrint($Md);
            if (DB == '' || ! $Md) {
                echo"<p class='links'>".(support('sql') ? "<a href='".h(ME)."sql='".bold(isset($_GET['sql']) && ! isset($_GET['import'])).'>'.'SQL command'."</a>\n<a href='".h(ME)."import='".bold(isset($_GET['import'])).'>'.'Import'."</a>\n" : '').'';
                if (support('dump')) {
                    echo"<a href='".h(ME).'dump='.urlencode(isset($_GET['table']) ? $_GET['table'] : $_GET['select'])."' id='dump'".bold(isset($_GET['dump'])).'>'.'Export'."</a>\n";
                }
            }
            if ($_GET['ns'] !== '' && ! $Md && DB != '') {
                echo'<a href="'.h(ME).'create="'.bold($_GET['create'] === '').'>'.'Create table'."</a>\n";
                if (! $S) {
                    echo"<p class='message'>".'No tables.'."\n";
                } else {
                    $this->tablesPrint($S);
                }
            }
        }
    }

    public function databasesPrint($Md)
    {
        global$b,$e;
        $h = $this->databases();
        echo'<form action="">
<p id="dbs">
';
        hidden_fields_get();
        $ub = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
        echo"<span title='".'database'."'>".'DB'.'</span>: '.($h ? "<select name='db'>".optionlist(['' => ''] + $h, DB)."</select>$ub" : "<input name='db' value='".h(DB)."' autocapitalize='off'>\n"),"<input type='submit' value='".'Use'."'".($h ? " class='hidden'" : '').">\n";
        if ($Md != 'db' && DB != '' && $e->select_db(DB)) {
        }
        echo(isset($_GET['sql']) ? '<input type="hidden" name="sql" value="">' : (isset($_GET['schema']) ? '<input type="hidden" name="schema" value="">' : (isset($_GET['dump']) ? '<input type="hidden" name="dump" value="">' : (isset($_GET['privileges']) ? '<input type="hidden" name="privileges" value="">' : '')))),"</p></form>\n";
    }

    public function tablesPrint($S)
    {
        echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
        foreach ($S
        as$Q => $Uf) {
            $E = $this->tableName($Uf);
            if ($E != '') {
                echo'<li><a href="'.h(ME).'select='.urlencode($Q).'"'.bold($_GET['select'] == $Q || $_GET['edit'] == $Q, 'select').'>'.'select'.'</a> ',(support('table') || support('indexes') ? '<a href="'.h(ME).'table='.urlencode($Q).'"'.bold(in_array($Q, [$_GET['table'], $_GET['create'], $_GET['indexes'], $_GET['foreign'], $_GET['trigger']]), (is_view($Uf) ? 'view' : 'structure'))." title='".'Show structure'."'>$E</a>" : "<span>$E</span>")."\n";
            }
        }
        echo"</ul>\n";
    }
}$b = (function_exists('adminer_object') ? adminer_object() : new
Adminer);
if ($b->operators === null) {
    $b->operators = $ke;
}function page_header($wg, $k = '', $Ha = [], $xg = '')
{
    global$ca,$ga,$b,$Ib,$y;
    page_headers();
    if (is_ajax() && $k) {
        page_messages($k);
        exit;
    }
    $yg = $wg.($xg != '' ? ": $xg" : '');
    $zg = strip_tags($yg.(SERVER != '' && SERVER != 'localhost' ? h(' - '.SERVER) : '').' - '.$b->name());
    echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<title>',$zg,'</title>
<link rel="stylesheet" type="text/css" href="',h(preg_replace('~\\?.*~', '', ME).'?file=default.css&version=4.6.2&driver=mysql'),'">
',script_src(preg_replace('~\\?.*~', '', ME).'?file=functions.js&version=4.6.2&driver=mysql');
    if ($b->head()) {
        echo'<link rel="shortcut icon" type="image/x-icon" href="',h(preg_replace('~\\?.*~', '', ME).'?file=favicon.ico&version=4.6.2&driver=mysql'),'">
<link rel="apple-touch-icon" href="',h(preg_replace('~\\?.*~', '', ME).'?file=favicon.ico&version=4.6.2&driver=mysql'),'">
';
        foreach ($b->css()as$ob) {
            echo'<link rel="stylesheet" type="text/css" href="',h($ob),'">
';
        }
    }
    echo'
<body class="ltr nojs">
';
    $sc = get_temp_dir().'/adminer.version';
    if (! $_COOKIE['adminer_version'] && function_exists('openssl_verify') && file_exists($sc) && filemtime($sc) + 86400 > time()) {
        $eh = unserialize(file_get_contents($sc));
        $af = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
';
        if (openssl_verify($eh['version'], base64_decode($eh['signature']), $af) == 1) {
            $_COOKIE['adminer_version'] = $eh['version'];
        }
    }
    echo'<script',nonce(),'>
mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick',(isset($_COOKIE['adminer_version']) ? '' : ", onload: partial(verifyVersion, '$ga', '".js_escape(ME)."', '".get_token()."')"); ?>});
document.body.className = document.body.className.replace(/ nojs/, ' js');
var offlineMessage = '<?php echo
    js_escape('You are offline.'),'\';
var thousandsSeparator = \'',js_escape(','),'\';
</script>

<div id="help" class="jush-',$y,' jsonly hidden"></div>
',script("mixin(qs('#help'), {onmouseover: function () { helpOpen = 1; }, onmouseout: helpMouseout});"),'
<div id="content">
';
    if ($Ha !== null) {
        $A = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
        echo'<p id="breadcrumb"><a href="'.h($A ? $A : '.').'">'.$Ib[DRIVER].'</a> &raquo; ';
        $A = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
        $O = $b->serverName(SERVER);
        $O = ($O != '' ? $O : 'Server');
        if ($Ha === false) {
            echo"$O\n";
        } else {
            echo"<a href='".($A ? h($A) : '.')."' accesskey='1' title='Alt+Shift+1'>$O</a> &raquo; ";
            if ($_GET['ns'] != '' || (DB != '' && is_array($Ha))) {
                echo'<a href="'.h($A.'&db='.urlencode(DB).(support('scheme') ? '&ns=' : '')).'">'.h(DB).'</a> &raquo; ';
            }
            if (is_array($Ha)) {
                if ($_GET['ns'] != '') {
                    echo'<a href="'.h(substr(ME, 0, -1)).'">'.h($_GET['ns']).'</a> &raquo; ';
                }
                foreach ($Ha
                as$z => $X) {
                    $Ab = (is_array($X) ? $X[1] : h($X));
                    if ($Ab != '') {
                        echo"<a href='".h(ME."$z=").urlencode(is_array($X) ? $X[0] : $X)."'>$Ab</a> &raquo; ";
                    }
                }
            }
            echo"$wg\n";
        }
    }
    echo"<h2>$yg</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";
    restart_session();
    page_messages($k);
    $h = &get_session('dbs');
    if (DB != '' && $h && ! in_array(DB, $h, true)) {
        $h = null;
    }
    stop_session();
    define('PAGE_HEADER', 1);
}function page_headers()
{
    global$b;
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache');
    header('X-Frame-Options: deny');
    header('X-XSS-Protection: 0');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: origin-when-cross-origin');
    foreach ($b->csp()as$nb) {
        $Kc = [];
        foreach ($nb
        as$z => $X) {
            $Kc[] = "$z $X";
        }
        header('Content-Security-Policy: '.implode('; ', $Kc));
    }
    $b->headers();
}function csp()
{
    return
    [['script-src' => "'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'", 'connect-src' => "'self'", 'frame-src' => 'https://www.adminer.org', 'object-src' => "'none'", 'base-uri' => "'none'", 'form-action' => "'self'"]];
}function get_nonce()
{
    static$Vd;
    if (! $Vd) {
        $Vd = base64_encode(rand_string());
    }

    return$Vd;
}function page_messages($k)
{
    $Ug = preg_replace('~^[^?]*~', '', $_SERVER['REQUEST_URI']);
    $Kd = $_SESSION['messages'][$Ug];
    if ($Kd) {
        echo"<div class='message'>".implode("</div>\n<div class='message'>", $Kd).'</div>'.script('messagesPrint();');
        unset($_SESSION['messages'][$Ug]);
    }
    if ($k) {
        echo"<div class='error'>$k</div>\n";
    }
}function page_footer($Md = '')
{
    global$b,$T;
    echo'</div>

';
    if ($Md != 'auth') {
        echo'<form action="" method="post">
<p class="logout">
<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="',$T,'">
</p>
</form>
';
    }
    echo'<div id="menu">
';
    $b->navigation($Md);
    echo'</div>
',script('setupSubmitHighlight(document);');
}function int32($Pd)
{
    while ($Pd >= 2147483648) {
        $Pd -= 4294967296;
    }
    while ($Pd <= -2147483649) {
        $Pd += 4294967296;
    }

    return(int) $Pd;
}function long2str($W, $ih)
{
    $xf = '';
    foreach ($W
    as$X) {
        $xf .= pack('V', $X);
    }
    if ($ih) {
        return
        substr($xf, 0, end($W));
    }

    return$xf;
}function str2long($xf, $ih)
{
    $W = array_values(unpack('V*', str_pad($xf, 4 * ceil(strlen($xf) / 4), "\0")));
    if ($ih) {
        $W[] = strlen($xf);
    }

    return$W;
}function xxtea_mx($ph, $oh, $cg, $fd)
{
    return
    int32((($ph >> 5 & 0x7FFFFFF) ^ $oh << 2) + (($oh >> 3 & 0x1FFFFFFF) ^ $ph << 4)) ^ int32(($cg ^ $oh) + ($fd ^ $ph));
}function encrypt_string($Wf, $z)
{
    if ($Wf == '') {
        return'';
    }
    $z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($Wf, true);
    $Pd = count($W) - 1;
    $ph = $W[$Pd];
    $oh = $W[0];
    $H = floor(6 + 52 / ($Pd + 1));
    $cg = 0;
    while ($H-- > 0) {
        $cg = int32($cg + 0x9E3779B9);
        $Ob = $cg >> 2 & 3;
        for ($ze = 0; $ze < $Pd; $ze++) {
            $oh = $W[$ze + 1];
            $Od = xxtea_mx($ph, $oh, $cg, $z[$ze & 3 ^ $Ob]);
            $ph = int32($W[$ze] + $Od);
            $W[$ze] = $ph;
        }
        $oh = $W[0];
        $Od = xxtea_mx($ph, $oh, $cg, $z[$ze & 3 ^ $Ob]);
        $ph = int32($W[$Pd] + $Od);
        $W[$Pd] = $ph;
    }

    return
        long2str($W, false);
}function decrypt_string($Wf, $z)
{
    if ($Wf == '') {
        return'';
    }
    if (! $z) {
        return
        false;
    }
    $z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($Wf, false);
    $Pd = count($W) - 1;
    $ph = $W[$Pd];
    $oh = $W[0];
    $H = floor(6 + 52 / ($Pd + 1));
    $cg = int32($H * 0x9E3779B9);
    while ($cg) {
        $Ob = $cg >> 2 & 3;
        for ($ze = $Pd; $ze > 0; $ze--) {
            $ph = $W[$ze - 1];
            $Od = xxtea_mx($ph, $oh, $cg, $z[$ze & 3 ^ $Ob]);
            $oh = int32($W[$ze] - $Od);
            $W[$ze] = $oh;
        }
        $ph = $W[$Pd];
        $Od = xxtea_mx($ph, $oh, $cg, $z[$ze & 3 ^ $Ob]);
        $oh = int32($W[0] - $Od);
        $W[0] = $oh;
        $cg = int32($cg - 0x9E3779B9);
    }

    return
        long2str($W, true);
}$e = '';
$Jc = $_SESSION['token'];
if (! $Jc) {
    $_SESSION['token'] = rand(1, 1e6);
}$T = get_token();
$Ke = [];
if ($_COOKIE['adminer_permanent']) {
    foreach (explode(' ', $_COOKIE['adminer_permanent'])as$X) {
        [$z] = explode(':', $X);
        $Ke[$z] = $X;
    }
}function add_invalid_login()
{
    global$b;
    $p = file_open_lock(get_temp_dir().'/adminer.invalid');
    if (! $p) {
        return;
    }
    $ad = unserialize(stream_get_contents($p));
    $tg = time();
    if ($ad) {
        foreach ($ad
        as$bd => $X) {
            if ($X[0] < $tg) {
                unset($ad[$bd]);
            }
        }
    }
    $Zc = &$ad[$b->bruteForceKey()];
    if (! $Zc) {
        $Zc = [$tg + 30 * 60, 0];
    }
    $Zc[1]++;
    file_write_unlock($p, serialize($ad));
}function check_invalid_login()
{
    global$b;
    $ad = unserialize(@file_get_contents(get_temp_dir().'/adminer.invalid'));
    $Zc = $ad[$b->bruteForceKey()];
    $Ud = ($Zc[1] > 29 ? $Zc[0] - time() : 0);
    if ($Ud > 0) {
        auth_error(lang(['Too many unsuccessful logins, try again in %d minute.', 'Too many unsuccessful logins, try again in %d minutes.'], ceil($Ud / 60)));
    }
}$xa = $_POST['auth'];
if ($xa) {
    session_regenerate_id();
    $dh = $xa['driver'];
    $O = $xa['server'];
    $V = $xa['username'];
    $G = (string) $xa['password'];
    $i = $xa['db'];
    set_password($dh, $O, $V, $G);
    $_SESSION['db'][$dh][$O][$V][$i] = true;
    if ($xa['permanent']) {
        $z = base64_encode($dh).'-'.base64_encode($O).'-'.base64_encode($V).'-'.base64_encode($i);
        $Ve = $b->permanentLogin(true);
        $Ke[$z] = "$z:".base64_encode($Ve ? encrypt_string($G, $Ve) : '');
        cookie('adminer_permanent', implode(' ', $Ke));
    }
    if (count($_POST) == 1 || DRIVER != $dh || SERVER != $O || $_GET['username'] !== $V || DB != $i) {
        redirect(auth_url($dh, $O, $V, $i));
    }
} elseif ($_POST['logout']) {
    if ($Jc && ! verify_token()) {
        page_header('Logout', 'Invalid CSRF token. Send the form again.');
        page_footer('db');
        exit;
    } else {
        foreach (['pwds', 'db', 'dbs', 'queries']as$z) {
            set_session($z, null);
        }
        unset_permanent();
        redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), 'Logout successful.'.' '.sprintf('Thanks for using Adminer, consider <a href="%s">donating</a>.', 'https://sourceforge.net/donate/index.php?group_id=264133'));
    }
} elseif ($Ke && ! $_SESSION['pwds']) {
    session_regenerate_id();
    $Ve = $b->permanentLogin();
    foreach ($Ke
    as$z => $X) {
        [, $Qa] = explode(':', $X);
        [$dh, $O, $V, $i] = array_map('base64_decode', explode('-', $z));
        set_password($dh, $O, $V, decrypt_string(base64_decode($Qa), $Ve));
        $_SESSION['db'][$dh][$O][$V][$i] = true;
    }
}function unset_permanent()
{
    global$Ke;
    foreach ($Ke
    as$z => $X) {
        [$dh, $O, $V, $i] = array_map('base64_decode', explode('-', $z));
        if ($dh == DRIVER && $O == SERVER && $V == $_GET['username'] && $i == DB) {
            unset($Ke[$z]);
        }
    }
    cookie('adminer_permanent', implode(' ', $Ke));
}function auth_error($k)
{
    global$b,$Jc;
    $If = session_name();
    if (isset($_GET['username'])) {
        header('HTTP/1.1 403 Forbidden');
        if (($_COOKIE[$If] || $_GET[$If]) && ! $Jc) {
            $k = 'Session expired, please login again.';
        } else {
            add_invalid_login();
            $G = get_password();
            if ($G !== null) {
                if ($G === false) {
                    $k .= '<br>'.sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.', target_blank(), '<code>permanentLogin()</code>');
                }
                set_password(DRIVER, SERVER, $_GET['username'], null);
            }
            unset_permanent();
        }
    }
    if (! $_COOKIE[$If] && $_GET[$If] && ini_bool('session.use_only_cookies')) {
        $k = 'Session support must be enabled.';
    }
    $Be = session_get_cookie_params();
    cookie('adminer_key', ($_COOKIE['adminer_key'] ? $_COOKIE['adminer_key'] : rand_string()), $Be['lifetime']);
    page_header('Login', $k, null);
    echo"<form action='' method='post'>\n",'<div>';
    if (hidden_fields($_POST, ['auth'])) {
        echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";
    }
    echo"</div>\n";
    $b->loginForm();
    echo"</form>\n";
    page_footer('auth');
    exit;
} if (isset($_GET['username'])) {
    if (! class_exists('Min_DB')) {
        unset($_SESSION['pwds'][DRIVER]);
        unset_permanent();
        page_header('No extension', sprintf('None of the supported PHP extensions (%s) are available.', implode(', ', $Qe)), false);
        page_footer('auth');
        exit;
    }
    [$Nc, $Me] = explode(':', SERVER, 2);
    if (is_numeric($Me) && $Me < 1024) {
        auth_error('Connecting to privileged ports is not allowed.');
    }
    check_invalid_login();
    $e = connect();
    $j = new
    Min_Driver($e);
}$wd = null;
if (! is_object($e) || ($wd = $b->login($_GET['username'], get_password())) !== true) {
    auth_error((is_string($e) ? h($e) : (is_string($wd) ? $wd : 'Invalid credentials.')));
} if ($xa && $_POST['token']) {
    $_POST['token'] = $T;
}$k = '';
if ($_POST) {
    if (! verify_token()) {
        $Uc = 'max_input_vars';
        $Fd = ini_get($Uc);
        if (extension_loaded('suhosin')) {
            foreach (['suhosin.request.max_vars', 'suhosin.post.max_vars']as$z) {
                $X = ini_get($z);
                if ($X && (! $Fd || $X < $Fd)) {
                    $Uc = $z;
                    $Fd = $X;
                }
            }
        }
        $k = (! $_POST['token'] && $Fd ? sprintf('Maximum number of allowed fields exceeded. Please increase %s.', "'$Uc'") : 'Invalid CSRF token. Send the form again.'.' '.'If you did not send this request from Adminer then close this page.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $k = sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.', "'post_max_size'");
    if (isset($_GET['sql'])) {
        $k .= ' '.'You can upload a big SQL file via FTP and import it from server.';
    }
} if (! ini_bool('session.use_cookies') || @ini_set('session.use_cookies', false) !== false) {
    session_write_close();
}function select($J, $f = null, $re = [], $_ = 0)
{
    global$y;
    $vd = [];
    $w = [];
    $d = [];
    $Fa = [];
    $Lg = [];
    $K = [];
    odd('');
    for ($t = 0; (! $_ || $t < $_) && ($L = $J->fetch_row()); $t++) {
        if (! $t) {
            echo"<table cellspacing='0' class='nowrap'>\n",'<thead><tr>';
            for ($x = 0; $x < count($L); $x++) {
                $l = $J->fetch_field();
                $E = $l->name;
                $qe = $l->orgtable;
                $pe = $l->orgname;
                $K[$l->table] = $qe;
                if ($re && $y == 'sql') {
                    $vd[$x] = ($E == 'table' ? 'table=' : ($E == 'possible_keys' ? 'indexes=' : null));
                } elseif ($qe != '') {
                    if (! isset($w[$qe])) {
                        $w[$qe] = [];
                        foreach (indexes($qe, $f)as$v) {
                            if ($v['type'] == 'PRIMARY') {
                                $w[$qe] = array_flip($v['columns']);
                                break;
                            }
                        }
                        $d[$qe] = $w[$qe];
                    }
                    if (isset($d[$qe][$pe])) {
                        unset($d[$qe][$pe]);
                        $w[$qe][$pe] = $x;
                        $vd[$x] = $qe;
                    }
                }
                if ($l->charsetnr == 63) {
                    $Fa[$x] = true;
                }
                $Lg[$x] = $l->type;
                echo'<th'.($qe != '' || $l->name != $pe ? " title='".h(($qe != '' ? "$qe." : '').$pe)."'" : '').'>'.h($E).($re ? doc_link(['sql' => 'explain-output.html#explain_'.strtolower($E), 'mariadb' => 'explain/#the-columns-in-explain-select']) : '');
            }
            echo"</thead>\n";
        }
        echo'<tr'.odd().'>';
        foreach ($L
        as$z => $X) {
            if ($X === null) {
                $X = '<i>NULL</i>';
            } elseif ($Fa[$z] && ! is_utf8($X)) {
                $X = '<i>'.lang(['%d byte', '%d bytes'], strlen($X)).'</i>';
            } elseif (! strlen($X)) {
                $X = '&nbsp;';
            } else {
                $X = h($X);
                if ($Lg[$z] == 254) {
                    $X = "<code>$X</code>";
                }
            }
            if (isset($vd[$z]) && ! $d[$vd[$z]]) {
                if ($re && $y == 'sql') {
                    $Q = $L[array_search('table=', $vd)];
                    $A = $vd[$z].urlencode($re[$Q] != '' ? $re[$Q] : $Q);
                } else {
                    $A = 'edit='.urlencode($vd[$z]);
                    foreach ($w[$vd[$z]]as$Ua => $x) {
                        $A .= '&where'.urlencode('['.bracket_escape($Ua).']').'='.urlencode($L[$x]);
                    }
                }
                $X = "<a href='".h(ME.$A)."'>$X</a>";
            }
            echo"<td>$X";
        }
    }
    echo($t ? '</table>' : "<p class='message'>".'No rows.')."\n";

    return$K;
}function referencable_primary($Df)
{
    $K = [];
    foreach (table_status('', true)as$gg => $Q) {
        if ($gg != $Df && fk_support($Q)) {
            foreach (fields($gg)as$l) {
                if ($l['primary']) {
                    if ($K[$gg]) {
                        unset($K[$gg]);
                        break;
                    }
                    $K[$gg] = $l;
                }
            }
        }
    }

    return$K;
}function textarea($E, $Y, $M = 10, $Ya = 80)
{
    global$y;
    echo"<textarea name='$E' rows='$M' cols='$Ya' class='sqlarea jush-$y' spellcheck='false' wrap='off'>";
    if (is_array($Y)) {
        foreach ($Y
        as$X) {
            echo
            h($X[0])."\n\n\n";
        }
    } else {
        echo
        h($Y);
    }
    echo'</textarea>';
}function edit_type($z, $l, $Xa, $o = [], $mc = [])
{
    global$Yf,$Lg,$Sg,$ge;
    $U = $l['type'];
    echo'<td><select name="',h($z),'[type]" class="type" aria-labelledby="label-type">';
    if ($U && ! isset($Lg[$U]) && ! isset($o[$U]) && ! in_array($U, $mc)) {
        $mc[] = $U;
    }
    if ($o) {
        $Yf['Foreign keys'] = $o;
    }
    echo
    optionlist(array_merge($mc, $Yf), $U),'</select>
',on_help('getTarget(event).value', 1),script("mixin(qsl('select'), {onfocus: function () { lastType = selectValue(this); }, onchange: editingTypeChange});", ''),'<td><input name="',h($z),'[length]" value="',h($l['length']),'" size="3"',(! $l['length'] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : ''),' aria-labelledby="label-length">',script("mixin(qsl('input'), {onfocus: editingLengthFocus, oninput: editingLengthChange});", ''),'<td class="options">';
    echo"<select name='".h($z)."[collation]'".(preg_match('~(char|text|enum|set)$~', $U) ? '' : " class='hidden'").'><option value="">('.'collation'.')'.optionlist($Xa, $l['collation']).'</select>',($Sg ? "<select name='".h($z)."[unsigned]'".(! $U || preg_match(number_type(), $U) ? '' : " class='hidden'").'><option>'.optionlist($Sg, $l['unsigned']).'</select>' : ''),(isset($l['on_update']) ? "<select name='".h($z)."[on_update]'".(preg_match('~timestamp|datetime~', $U) ? '' : " class='hidden'").'>'.optionlist(['' => '('.'ON UPDATE'.')', 'CURRENT_TIMESTAMP'], $l['on_update']).'</select>' : ''),($o ? "<select name='".h($z)."[on_delete]'".(preg_match('~`~', $U) ? '' : " class='hidden'")."><option value=''>(".'ON DELETE'.')'.optionlist(explode('|', $ge), $l['on_delete']).'</select> ' : ' ');
}function process_length($sd)
{
    global$Zb;

    return preg_match("~^\\s*\\(?\\s*$Zb(?:\\s*,\\s*$Zb)*+\\s*\\)?\\s*\$~", $sd) && preg_match_all("~$Zb~", $sd, $_d) ? '('.implode(',', $_d[0]).')' : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $sd));
}function process_type($l, $Va = 'COLLATE')
{
    global$Sg;

    return" $l[type]".process_length($l['length']).(preg_match(number_type(), $l['type']) && in_array($l['unsigned'], $Sg) ? " $l[unsigned]" : '').(preg_match('~char|text|enum|set~', $l['type']) && $l['collation'] ? " $Va ".q($l['collation']) : '');
}function process_field($l, $Jg)
{
    return
    [idf_escape(trim($l['field'])), process_type($Jg), ($l['null'] ? ' NULL' : ' NOT NULL'), default_value($l), (preg_match('~timestamp|datetime~', $l['type']) && $l['on_update'] ? " ON UPDATE $l[on_update]" : ''), (support('comment') && $l['comment'] != '' ? ' COMMENT '.q($l['comment']) : ''), ($l['auto_increment'] ? auto_increment() : null)];
}function default_value($l)
{
    $yb = $l['default'];

    return $yb === null ? '' : ' DEFAULT '.(preg_match('~char|binary|text|enum|set~', $l['type']) || preg_match('~^(?![a-z])~i', $yb) ? q($yb) : $yb);
}function type_class($U)
{
    foreach (['char' => 'text', 'date' => 'time|year', 'binary' => 'blob', 'enum' => 'set']as$z => $X) {
        if (preg_match("~$z|$X~", $U)) {
            return" class='$z'";
        }
    }
}function edit_fields($m, $Xa, $U = 'TABLE', $o = [], $cb = false)
{
    global$Vc;
    $m = array_values($m);
    echo'<thead><tr>
';
    if ($U == 'PROCEDURE') {
        echo'<td>&nbsp;';
    }
    echo'<th id="label-name">',($U == 'TABLE' ? 'Column name' : 'Parameter name'),'<td id="label-type">Type<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;"></textarea>',script("qs('#enum-edit').onblur = editingLengthBlur;"),'<td id="label-length">Length
<td>','Options';
    if ($U == 'TABLE') {
        echo'<td id="label-null">NULL
<td><input type="radio" name="auto_increment_col" value=""><acronym id="label-ai" title="Auto Increment">AI</acronym>',doc_link(['sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/', 'sqlite' => 'autoinc.html', 'pgsql' => 'datatype.html#DATATYPE-SERIAL', 'mssql' => 'ms186775.aspx']),'<td id="label-default">Default value
',(support('comment') ? "<td id='label-comment'".($cb ? '' : " class='hidden'").'>'.'Comment' : '');
    }
    echo'<td>',"<input type='image' class='icon' name='add[".(support('move_col') ? 0 : count($m))."]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=4.6.2&driver=mysql')."' alt='+' title='".'Add next'."'>".script('row_count = '.count($m).';'),'</thead>
<tbody>
',script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
    foreach ($m
    as$t => $l) {
        $t++;
        $se = $l[($_POST ? 'orig' : 'field')];
        $Eb = (isset($_POST['add'][$t - 1]) || (isset($l['field']) && ! $_POST['drop_col'][$t])) && (support('drop_col') || $se == '');
        echo'<tr',($Eb ? '' : " style='display: none;'"),'>
',($U == 'PROCEDURE' ? '<td>'.html_select("fields[$t][inout]", explode('|', $Vc), $l['inout']) : ''),'<th>';
        if ($Eb) {
            echo'<input name="fields[',$t,'][field]" value="',h($l['field']),'" maxlength="64" autocapitalize="off" aria-labelledby="label-name">',script("qsl('input').oninput = function () { editingNameChange.call(this);".($l['field'] != '' || count($m) > 1 ? '' : ' editingAddRow.call(this);').' };', '');
        }
        echo'<input type="hidden" name="fields[',$t,'][orig]" value="',h($se),'">
';
        edit_type("fields[$t]", $l, $Xa, $o);
        if ($U == 'TABLE') {
            echo'<td>',checkbox("fields[$t][null]", 1, $l['null'], '', '', 'block', 'label-null'),'<td><label class="block"><input type="radio" name="auto_increment_col" value="',$t,'"';
            if ($l['auto_increment']) {
                echo' checked';
            }
            echo' aria-labelledby="label-ai"></label><td>',checkbox("fields[$t][has_default]", 1, $l['has_default'], '', '', '', 'label-default'),'<input name="fields[',$t,'][default]" value="',h($l['default']),'" aria-labelledby="label-default">',(support('comment') ? '<td'.($cb ? '' : " class='hidden'")."><input name='fields[$t][comment]' value='".h($l['comment'])."' maxlength='".(min_version(5.5) ? 1024 : 255)."' aria-labelledby='label-comment'>" : '');
        }
        echo'<td>',(support('move_col') ? "<input type='image' class='icon' name='add[$t]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=4.6.2&driver=mysql')."' alt='+' title='".'Add next'."'>&nbsp;"."<input type='image' class='icon' name='up[$t]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=up.gif&version=4.6.2&driver=mysql')."' alt='↑' title='".'Move up'."'>&nbsp;"."<input type='image' class='icon' name='down[$t]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=down.gif&version=4.6.2&driver=mysql')."' alt='↓' title='".'Move down'."'>&nbsp;" : ''),($se == '' || support('drop_col') ? "<input type='image' class='icon' name='drop_col[$t]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=cross.gif&version=4.6.2&driver=mysql')."' alt='x' title='".'Remove'."'>" : '');
    }
}function process_fields(&$m)
{
    $Zd = 0;
    if ($_POST['up']) {
        $md = 0;
        foreach ($m
        as$z => $l) {
            if (key($_POST['up']) == $z) {
                unset($m[$z]);
                array_splice($m, $md, 0, [$l]);
                break;
            }
            if (isset($l['field'])) {
                $md = $Zd;
            }
            $Zd++;
        }
    } elseif ($_POST['down']) {
        $zc = false;
        foreach ($m
        as$z => $l) {
            if (isset($l['field']) && $zc) {
                unset($m[key($_POST['down'])]);
                array_splice($m, $Zd, 0, [$zc]);
                break;
            }
            if (key($_POST['down']) == $z) {
                $zc = $l;
            }
            $Zd++;
        }
    } elseif ($_POST['add']) {
        $m = array_values($m);
        array_splice($m, key($_POST['add']), 0, [[]]);
    } elseif (! $_POST['drop_col']) {
        return
        false;
    }

    return
        true;
}function normalize_enum($C)
{
    return"'".str_replace("'", "''", addcslashes(stripcslashes(str_replace($C[0][0].$C[0][0], $C[0][0], substr($C[0], 1, -1))), '\\'))."'";
}function grant($r, $Xe, $d, $fe)
{
    if (! $Xe) {
        return
        true;
    }
    if ($Xe == ['ALL PRIVILEGES', 'GRANT OPTION']) {
        return $r == 'GRANT' ? queries("$r ALL PRIVILEGES$fe WITH GRANT OPTION") : queries("$r ALL PRIVILEGES$fe") && queries("$r GRANT OPTION$fe");
    }

    return
        queries("$r ".preg_replace('~(GRANT OPTION)\\([^)]*\\)~', '\\1', implode("$d, ", $Xe).$d).$fe);
}function drop_create($Jb, $g, $Kb, $qg, $Lb, $B, $Jd, $Hd, $Id, $ce, $Sd)
{
    if ($_POST['drop']) {
        query_redirect($Jb, $B, $Jd);
    } elseif ($ce == '') {
        query_redirect($g, $B, $Id);
    } elseif ($ce != $Sd) {
        $lb = queries($g);
        queries_redirect($B, $Hd, $lb && queries($Jb));
        if ($lb) {
            queries($Kb);
        }
    } else {
        queries_redirect($B, $Hd, queries($qg) && queries($Lb) && queries($Jb) && queries($g));
    }
}function create_trigger($fe, $L)
{
    global$y;
    $vg = " $L[Timing] $L[Event]".($L['Event'] == 'UPDATE OF' ? ' '.idf_escape($L['Of']) : '');

    return'CREATE TRIGGER '.idf_escape($L['Trigger']).($y == 'mssql' ? $fe.$vg : $vg.$fe).rtrim(" $L[Type]\n$L[Statement]", ';').';';
}function create_routine($uf, $L)
{
    global$Vc,$y;
    $P = [];
    $m = (array) $L['fields'];
    ksort($m);
    foreach ($m
    as$l) {
        if ($l['field'] != '') {
            $P[] = (preg_match("~^($Vc)\$~", $l['inout']) ? "$l[inout] " : '').idf_escape($l['field']).process_type($l, 'CHARACTER SET');
        }
    }
    $zb = rtrim("\n$L[definition]", ';');

    return"CREATE $uf ".idf_escape(trim($L['name'])).' ('.implode(', ', $P).')'.(isset($_GET['function']) ? ' RETURNS'.process_type($L['returns'], 'CHARACTER SET') : '').($L['language'] ? " LANGUAGE $L[language]" : '').($y == 'pgsql' ? ' AS '.q($zb) : "$zb;");
}function remove_definer($I)
{
    return
    preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~', '`@`(%|\\1)', logged_user()).'`~', '\\1', $I);
}function format_foreign_key($n)
{
    global$ge;

    return' FOREIGN KEY ('.implode(', ', array_map('idf_escape', $n['source'])).') REFERENCES '.table($n['table']).' ('.implode(', ', array_map('idf_escape', $n['target'])).')'.(preg_match("~^($ge)\$~", $n['on_delete']) ? " ON DELETE $n[on_delete]" : '').(preg_match("~^($ge)\$~", $n['on_update']) ? " ON UPDATE $n[on_update]" : '');
}function tar_file($sc, $_g)
{
    $K = pack('a100a8a8a8a12a12', $sc, 644, 0, 0, decoct($_g->size), decoct(time()));
    $Pa = 8 * 32;
    for ($t = 0; $t < strlen($K); $t++) {
        $Pa += ord($K[$t]);
    }
    $K .= sprintf('%06o', $Pa)."\0 ";
    echo$K,str_repeat("\0", 512 - strlen($K));
    $_g->send();
    echo
    str_repeat("\0", 511 - ($_g->size + 511) % 512);
}function ini_bytes($Uc)
{
    $X = ini_get($Uc);
    switch (strtolower(substr($X, -1))) {
        case'g':$X *= 1024;
        case'm':$X *= 1024;
        case'k':$X *= 1024;
    }

    return$X;
}function doc_link($Ie, $rg = '<sup>?</sup>')
{
    global$y,$e;
    $Gf = $e->server_info;
    $eh = preg_replace('~^(\\d\\.?\\d).*~s', '\\1', $Gf);
    $Wg = ['sql' => "https://dev.mysql.com/doc/refman/$eh/en/", 'sqlite' => 'https://www.sqlite.org/', 'pgsql' => "https://www.postgresql.org/docs/$eh/static/", 'mssql' => 'https://msdn.microsoft.com/library/', 'oracle' => 'https://download.oracle.com/docs/cd/B19306_01/server.102/b14200/'];
    if (preg_match('~MariaDB~', $Gf)) {
        $Wg['sql'] = 'https://mariadb.com/kb/en/library/';
        $Ie['sql'] = (isset($Ie['mariadb']) ? $Ie['mariadb'] : str_replace('.html', '/', $Ie['sql']));
    }

    return $Ie[$y] ? "<a href='$Wg[$y]$Ie[$y]'".target_blank().">$rg</a>" : '';
}function ob_gzencode($Xf)
{
    return
    gzencode($Xf);
}function db_size($i)
{
    global$e;
    if (! $e->select_db($i)) {
        return'?';
    }
    $K = 0;
    foreach (table_status()as$R) {
        $K += $R['Data_length'] + $R['Index_length'];
    }

    return
        format_number($K);
}function set_utf8mb4($g)
{
    global$e;
    static$P = false;
    if (! $P && preg_match('~\butf8mb4~i', $g)) {
        $P = true;
        echo'SET NAMES '.charset($e).";\n\n";
    }
}function connect_error()
{
    global$b,$e,$T,$k,$Ib;
    if (DB != '') {
        header('HTTP/1.1 404 Not Found');
        page_header('Database'.': '.h(DB), 'Invalid database.', true);
    } else {
        if ($_POST['db'] && ! $k) {
            queries_redirect(substr(ME, 0, -1), 'Databases have been dropped.', drop_databases($_POST['db']));
        }
        page_header('Select database', $k, false);
        echo"<p class='links'>\n";
        foreach (['database' => 'Create database', 'privileges' => 'Privileges', 'processlist' => 'Process list', 'variables' => 'Variables', 'status' => 'Status']as$z => $X) {
            if (support($z)) {
                echo"<a href='".h(ME)."$z='>$X</a>\n";
            }
        }
        echo'<p>'.sprintf('%s version: %s through PHP extension %s', $Ib[DRIVER], '<b>'.h($e->server_info).'</b>', "<b>$e->extension</b>")."\n",'<p>'.sprintf('Logged as: %s', '<b>'.h(logged_user()).'</b>')."\n";
        $h = $b->databases();
        if ($h) {
            $_f = support('scheme');
            $Xa = collations();
            echo"<form action='' method='post'>\n","<table cellspacing='0' class='checkable'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr>'.(support('database') ? '<td>&nbsp;' : '').'<th>'.'Database'." - <a href='".h(ME)."refresh=1'>".'Refresh'.'</a>'.'<td>'.'Collation'.'<td>'.'Tables'.'<td>'.'Size'." - <a href='".h(ME)."dbsize=1'>".'Compute'.'</a>'.script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');", '')."</thead>\n";
            $h = ($_GET['dbsize'] ? count_tables($h) : array_flip($h));
            foreach ($h
            as$i => $S) {
                $tf = h(ME).'db='.urlencode($i);
                $u = h('Db-'.$i);
                echo'<tr'.odd().'>'.(support('database') ? '<td>'.checkbox('db[]', $i, in_array($i, (array) $_POST['db']), '', '', '', $u) : ''),"<th><a href='$tf' id='$u'>".h($i).'</a>';
                $Wa = nbsp(db_collation($i, $Xa));
                echo'<td>'.(support('database') ? "<a href='$tf".($_f ? '&amp;ns=' : '')."&amp;database=' title='".'Alter database'."'>$Wa</a>" : $Wa),"<td align='right'><a href='$tf&amp;schema=' id='tables-".h($i)."' title='".'Database schema'."'>".($_GET['dbsize'] ? $S : '?').'</a>',"<td align='right' id='size-".h($i)."'>".($_GET['dbsize'] ? db_size($i) : '?'),"\n";
            }
            echo"</table>\n",(support('database') ? "<div class='footer'><div>\n".'<fieldset><legend>'.'Selected'." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''>".script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'Drop'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n" : ''),"<input type='hidden' name='token' value='$T'>\n","</form>\n",script('tableCheck();');
        }
    }
    page_footer('db');
} if (isset($_GET['status'])) {
    $_GET['variables'] = $_GET['status'];
} if (isset($_GET['import'])) {
    $_GET['sql'] = $_GET['import'];
} if (! (DB != '' ? $e->select_db(DB) : isset($_GET['sql']) || isset($_GET['dump']) || isset($_GET['database']) || isset($_GET['processlist']) || isset($_GET['privileges']) || isset($_GET['user']) || isset($_GET['variables']) || $_GET['script'] == 'connect' || $_GET['script'] == 'kill')) {
    if (DB != '' || $_GET['refresh']) {
        restart_session();
        set_session('dbs', null);
    }
    connect_error();
    exit;
}$ge = 'RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT';
class TmpFile
{
    public $handler;

    public $size;

    public function __construct()
    {
        $this->handler = tmpfile();
    }

    public function write($gb)
    {
        $this->size += strlen($gb);
        fwrite($this->handler, $gb);
    }

    public function send()
    {
        fseek($this->handler, 0);
        fpassthru($this->handler);
        fclose($this->handler);
    }
}$Zb = "'(?:''|[^'\\\\]|\\\\.)*'";
$Vc = 'IN|OUT|INOUT';
if (isset($_GET['select']) && ($_POST['edit'] || $_POST['clone']) && ! $_POST['save']) {
    $_GET['edit'] = $_GET['select'];
} if (isset($_GET['callf'])) {
    $_GET['call'] = $_GET['callf'];
} if (isset($_GET['function'])) {
    $_GET['procedure'] = $_GET['function'];
} if (isset($_GET['download'])) {
    $a = $_GET['download'];
    $m = fields($a);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.friendly_url("$a-".implode('_', $_GET['where'])).'.'.friendly_url($_GET['field']));
    $N = [idf_escape($_GET['field'])];
    $J = $j->select($a, $N, [where($_GET, $m)], $N);
    $L = ($J ? $J->fetch_row() : []);
    echo$j->value($L[0], $m[$_GET['field']]);
    exit;
} elseif (isset($_GET['table'])) {
    $a = $_GET['table'];
    $m = fields($a);
    if (! $m) {
        $k = error();
    }
    $R = table_status1($a, true);
    $E = $b->tableName($R);
    page_header(($m && is_view($R) ? $R['Engine'] == 'materialized view' ? 'Materialized view' : 'View' : 'Table').': '.($E != '' ? $E : h($a)), $k);
    $b->selectLinks($R);
    $bb = $R['Comment'];
    if ($bb != '') {
        echo"<p class='nowrap'>".'Comment'.': '.h($bb)."\n";
    }
    if ($m) {
        $b->tableStructurePrint($m);
    }
    if (! is_view($R)) {
        if (support('indexes')) {
            echo"<h3 id='indexes'>".'Indexes'."</h3>\n";
            $w = indexes($a);
            if ($w) {
                $b->tableIndexesPrint($w);
            }
            echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";
        }
        if (fk_support($R)) {
            echo"<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";
            $o = foreign_keys($a);
            if ($o) {
                echo"<table cellspacing='0'>\n",'<thead><tr><th>'.'Source'.'<td>'.'Target'.'<td>'.'ON DELETE'.'<td>'.'ON UPDATE'."<td>&nbsp;</thead>\n";
                foreach ($o
                as$E => $n) {
                    echo"<tr title='".h($E)."'>",'<th><i>'.implode('</i>, <i>', array_map('h', $n['source'])).'</i>',"<td><a href='".h($n['db'] != '' ? preg_replace('~db=[^&]*~', 'db='.urlencode($n['db']), ME) : ($n['ns'] != '' ? preg_replace('~ns=[^&]*~', 'ns='.urlencode($n['ns']), ME) : ME)).'table='.urlencode($n['table'])."'>".($n['db'] != '' ? '<b>'.h($n['db']).'</b>.' : '').($n['ns'] != '' ? '<b>'.h($n['ns']).'</b>.' : '').h($n['table']).'</a>','(<i>'.implode('</i>, <i>', array_map('h', $n['target'])).'</i>)','<td>'.nbsp($n['on_delete'])."\n",'<td>'.nbsp($n['on_update'])."\n",'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($E)).'">'.'Alter'.'</a>';
                }
                echo"</table>\n";
            }
            echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";
        }
    }
    if (support(is_view($R) ? 'view_trigger' : 'trigger')) {
        echo"<h3 id='triggers'>".'Triggers'."</h3>\n";
        $Ig = triggers($a);
        if ($Ig) {
            echo"<table cellspacing='0'>\n";
            foreach ($Ig
            as$z => $X) {
                echo"<tr valign='top'><td>".h($X[0]).'<td>'.h($X[1]).'<th>'.h($z)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($z))."'>".'Alter'."</a>\n";
            }
            echo"</table>\n";
        }
        echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";
    }
} elseif (isset($_GET['schema'])) {
    page_header('Database schema', '', [], h(DB.($_GET['ns'] ? ".$_GET[ns]" : '')));
    $hg = [];
    $ig = [];
    $ea = ($_GET['schema'] ? $_GET['schema'] : $_COOKIE['adminer_schema-'.str_replace('.', '_', DB)]);
    preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $ea, $_d, PREG_SET_ORDER);
    foreach ($_d
    as$t => $C) {
        $hg[$C[1]] = [$C[2], $C[3]];
        $ig[] = "\n\t'".js_escape($C[1])."': [ $C[2], $C[3] ]";
    }
    $Bg = 0;
    $Ca = -1;
    $zf = [];
    $kf = [];
    $qd = [];
    foreach (table_status('', true)as$Q => $R) {
        if (is_view($R)) {
            continue;
        }
        $Ne = 0;
        $zf[$Q]['fields'] = [];
        foreach (fields($Q)as$E => $l) {
            $Ne += 1.25;
            $l['pos'] = $Ne;
            $zf[$Q]['fields'][$E] = $l;
        }
        $zf[$Q]['pos'] = ($hg[$Q] ? $hg[$Q] : [$Bg, 0]);
        foreach ($b->foreignKeys($Q)as$X) {
            if (! $X['db']) {
                $od = $Ca;
                if ($hg[$Q][1] || $hg[$X['table']][1]) {
                    $od = min(floatval($hg[$Q][1]), floatval($hg[$X['table']][1])) - 1;
                } else {
                    $Ca -= .1;
                }
                while ($qd[(string) $od]) {
                    $od -= .0001;
                }
                $zf[$Q]['references'][$X['table']][(string) $od] = [$X['source'], $X['target']];
                $kf[$X['table']][$Q][(string) $od] = $X['target'];
                $qd[(string) $od] = true;
            }
        }
        $Bg = max($Bg, $zf[$Q]['pos'][0] + 2.5 + $Ne);
    }
    echo'<div id="schema" style="height: ',$Bg,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = function () { return false; };
var tablePos = {',implode(',', $ig)."\n",'};
var em = qs(\'#schema\').offsetHeight / ',$Bg,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';
    foreach ($zf
    as$E => $Q) {
        echo"<div class='table' style='top: ".$Q['pos'][0].'em; left: '.$Q['pos'][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($E).'"><b>'.h($E).'</b></a>',script("qsl('div').onmousedown = schemaMousedown;");
        foreach ($Q['fields']as$l) {
            $X = '<span'.type_class($l['type']).' title="'.h($l['full_type'].($l['null'] ? ' NULL' : '')).'">'.h($l['field']).'</span>';
            echo'<br>'.($l['primary'] ? "<i>$X</i>" : $X);
        }
        foreach ((array) $Q['references']as$og => $lf) {
            foreach ($lf
            as$od => $hf) {
                $pd = $od - $hg[$E][1];
                $t = 0;
                foreach ($hf[0]as$Nf) {
                    echo"\n<div class='references' title='".h($og)."' id='refs$od-".($t++)."' style='left: $pd".'em; top: '.$Q['fields'][$Nf]['pos']."em; padding-top: .5em;'><div style='border-top: 1px solid Gray; width: ".(-$pd)."em;'></div></div>";
                }
            }
        }
        foreach ((array) $kf[$E]as$og => $lf) {
            foreach ($lf
            as$od => $d) {
                $pd = $od - $hg[$E][1];
                $t = 0;
                foreach ($d
                as$ng) {
                    echo"\n<div class='references' title='".h($og)."' id='refd$od-".($t++)."' style='left: $pd".'em; top: '.$Q['fields'][$ng]['pos'].'em; height: 1.25em; background: url('.h(preg_replace('~\\?.*~', '', ME).'?file=arrow.gif) no-repeat right center;&version=4.6.2&driver=mysql')."'><div style='height: .5em; border-bottom: 1px solid Gray; width: ".(-$pd)."em;'></div></div>";
                }
            }
        }
        echo"\n</div>\n";
    }
    foreach ($zf
    as$E => $Q) {
        foreach ((array) $Q['references']as$og => $lf) {
            foreach ($lf
            as$od => $hf) {
                $Ld = $Bg;
                $Dd = -10;
                foreach ($hf[0]as$z => $Nf) {
                    $Oe = $Q['pos'][0] + $Q['fields'][$Nf]['pos'];
                    $Pe = $zf[$og]['pos'][0] + $zf[$og]['fields'][$hf[1][$z]]['pos'];
                    $Ld = min($Ld, $Oe, $Pe);
                    $Dd = max($Dd, $Oe, $Pe);
                }
                echo"<div class='references' id='refl$od' style='left: $od"."em; top: $Ld"."em; padding: .5em 0;'><div style='border-right: 1px solid Gray; margin-top: 1px; height: ".($Dd - $Ld)."em;'></div></div>\n";
            }
        }
    }
    echo'</div>
<p class="links"><a href="',h(ME.'schema='.urlencode($ea)),'" id="schema-link">Permanent link</a>
';
} elseif (isset($_GET['dump'])) {
    $a = $_GET['dump'];
    if ($_POST && ! $k) {
        $jb = '';
        foreach (['output', 'format', 'db_style', 'routines', 'events', 'table_style', 'auto_increment', 'triggers', 'data_style']as$z) {
            $jb .= "&$z=".urlencode($_POST[$z]);
        }
        cookie('adminer_export', substr($jb, 1));
        $S = array_flip((array) $_POST['tables']) + array_flip((array) $_POST['data']);
        $kc = dump_headers((count($S) == 1 ? key($S) : DB), (DB == '' || count($S) > 1));
        $dd = preg_match('~sql~', $_POST['format']);
        if ($dd) {
            echo"-- Adminer $ga ".$Ib[DRIVER]." dump\n\n";
            if ($y == 'sql') {
                echo"SET NAMES utf8;
SET time_zone = '+00:00';
".($_POST['data_style'] ? "SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
" : '').'
';
                $e->query("SET time_zone = '+00:00';");
            }
        }
        $Zf = $_POST['db_style'];
        $h = [DB];
        if (DB == '') {
            $h = $_POST['databases'];
            if (is_string($h)) {
                $h = explode("\n", rtrim(str_replace("\r", '', $h), "\n"));
            }
        }
        foreach ((array) $h
        as$i) {
            $b->dumpDatabase($i);
            if ($e->select_db($i)) {
                if ($dd && preg_match('~CREATE~', $Zf) && ($g = $e->result('SHOW CREATE DATABASE '.idf_escape($i), 1))) {
                    set_utf8mb4($g);
                    if ($Zf == 'DROP+CREATE') {
                        echo'DROP DATABASE IF EXISTS '.idf_escape($i).";\n";
                    }
                    echo"$g;\n";
                }
                if ($dd) {
                    if ($Zf) {
                        echo
                        use_sql($i).";\n\n";
                    }
                    $xe = '';
                    if ($_POST['routines']) {
                        foreach (['FUNCTION', 'PROCEDURE']as$uf) {
                            foreach (get_rows("SHOW $uf STATUS WHERE Db = ".q($i), null, '-- ')as$L) {
                                $g = remove_definer($e->result("SHOW CREATE $uf ".idf_escape($L['Name']), 2));
                                set_utf8mb4($g);
                                $xe .= ($Zf != 'DROP+CREATE' ? "DROP $uf IF EXISTS ".idf_escape($L['Name']).";;\n" : '')."$g;;\n\n";
                            }
                        }
                    }
                    if ($_POST['events']) {
                        foreach (get_rows('SHOW EVENTS', null, '-- ')as$L) {
                            $g = remove_definer($e->result('SHOW CREATE EVENT '.idf_escape($L['Name']), 3));
                            set_utf8mb4($g);
                            $xe .= ($Zf != 'DROP+CREATE' ? 'DROP EVENT IF EXISTS '.idf_escape($L['Name']).";;\n" : '')."$g;;\n\n";
                        }
                    }
                    if ($xe) {
                        echo"DELIMITER ;;\n\n$xe"."DELIMITER ;\n\n";
                    }
                }
                if ($_POST['table_style'] || $_POST['data_style']) {
                    $gh = [];
                    foreach (table_status('', true)as$E => $R) {
                        $Q = (DB == '' || in_array($E, (array) $_POST['tables']));
                        $rb = (DB == '' || in_array($E, (array) $_POST['data']));
                        if ($Q || $rb) {
                            if ($kc == 'tar') {
                                $_g = new
                                TmpFile;
                                ob_start([$_g, 'write'], 1e5);
                            }
                            $b->dumpTable($E, ($Q ? $_POST['table_style'] : ''), (is_view($R) ? 2 : 0));
                            if (is_view($R)) {
                                $gh[] = $E;
                            } elseif ($rb) {
                                $m = fields($E);
                                $b->dumpData($E, $_POST['data_style'], 'SELECT *'.convert_fields($m, $m).' FROM '.table($E));
                            }
                            if ($dd && $_POST['triggers'] && $Q && ($Ig = trigger_sql($E))) {
                                echo"\nDELIMITER ;;\n$Ig\nDELIMITER ;\n";
                            }
                            if ($kc == 'tar') {
                                ob_end_flush();
                                tar_file((DB != '' ? '' : "$i/")."$E.csv", $_g);
                            } elseif ($dd) {
                                echo"\n";
                            }
                        }
                    }
                    foreach ($gh
                    as$fh) {
                        $b->dumpTable($fh, $_POST['table_style'], 1);
                    }
                    if ($kc == 'tar') {
                        echo
                        pack('x512');
                    }
                }
            }
        }
        if ($dd) {
            echo'-- '.$e->result('SELECT NOW()')."\n";
        }
        exit;
    }
    page_header('Export', $k, ($_GET['export'] != '' ? ['table' => $_GET['export']] : []), h(DB));
    echo'
<form action="" method="post">
<table cellspacing="0">
';
    $vb = ['', 'USE', 'DROP+CREATE', 'CREATE'];
    $jg = ['', 'DROP+CREATE', 'CREATE'];
    $sb = ['', 'TRUNCATE+INSERT', 'INSERT'];
    if ($y == 'sql') {
        $sb[] = 'INSERT+UPDATE';
    }
    parse_str($_COOKIE['adminer_export'], $L);
    if (! $L) {
        $L = ['output' => 'text', 'format' => 'sql', 'db_style' => (DB != '' ? '' : 'CREATE'), 'table_style' => 'DROP+CREATE', 'data_style' => 'INSERT'];
    }
    if (! isset($L['events'])) {
        $L['routines'] = $L['events'] = ($_GET['dump'] == '');
        $L['triggers'] = $L['table_style'];
    }
    echo'<tr><th>'.'Output'.'<td>'.html_select('output', $b->dumpOutput(), $L['output'], 0)."\n";
    echo'<tr><th>'.'Format'.'<td>'.html_select('format', $b->dumpFormat(), $L['format'], 0)."\n";
    echo($y == 'sqlite' ? '' : '<tr><th>'.'Database'.'<td>'.html_select('db_style', $vb, $L['db_style']).(support('routine') ? checkbox('routines', 1, $L['routines'], 'Routines') : '').(support('event') ? checkbox('events', 1, $L['events'], 'Events') : '')),'<tr><th>'.'Tables'.'<td>'.html_select('table_style', $jg, $L['table_style']).checkbox('auto_increment', 1, $L['auto_increment'], 'Auto Increment').(support('trigger') ? checkbox('triggers', 1, $L['triggers'], 'Triggers') : ''),'<tr><th>'.'Data'.'<td>'.html_select('data_style', $sb, $L['data_style']),'</table>
<p><input type="submit" value="Export">
<input type="hidden" name="token" value="',$T,'">

<table cellspacing="0">
',script("qsl('table').onclick = dumpClick;");
    $Se = [];
    if (DB != '') {
        $Na = ($a != '' ? '' : ' checked');
        echo'<thead><tr>',"<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Na>".'Tables'.'</label>'.script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);", ''),"<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$Na></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);", ''),"</thead>\n";
        $gh = '';
        $kg = tables_list();
        foreach ($kg
        as$E => $U) {
            $Re = preg_replace('~_.*~', '', $E);
            $Na = ($a == '' || $a == (substr($a, -1) == '%' ? "$Re%" : $E));
            $Ue = '<tr><td>'.checkbox('tables[]', $E, $Na, $E, '', 'block');
            if ($U !== null && ! preg_match('~table~i', $U)) {
                $gh .= "$Ue\n";
            } else {
                echo"$Ue<td align='right'><label class='block'><span id='Rows-".h($E)."'></span>".checkbox('data[]', $E, $Na)."</label>\n";
            }
            $Se[$Re]++;
        }
        echo$gh;
        if ($kg) {
            echo
            script("ajaxSetHtml('".js_escape(ME)."script=db');");
        }
    } else {
        echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a == '' ? ' checked' : '').'>'.'Database'.'</label>',script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);", ''),"</thead>\n";
        $h = $b->databases();
        if ($h) {
            foreach ($h
            as$i) {
                if (! information_schema($i)) {
                    $Re = preg_replace('~_.*~', '', $i);
                    echo'<tr><td>'.checkbox('databases[]', $i, $a == '' || $a == "$Re%", $i, '', 'block')."\n";
                    $Se[$Re]++;
                }
            }
        } else {
            echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";
        }
    }
    echo'</table>
</form>
';
    $uc = true;
    foreach ($Se
    as$z => $X) {
        if ($z != '' && $X > 1) {
            echo($uc ? '<p>' : ' ')."<a href='".h(ME).'dump='.urlencode("$z%")."'>".h($z).'</a>';
            $uc = false;
        }
    }
} elseif (isset($_GET['privileges'])) {
    page_header('Privileges');
    echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'.'</a>';
    $J = $e->query('SELECT User, Host FROM mysql.'.(DB == '' ? 'user' : 'db WHERE '.q(DB).' LIKE Db').' ORDER BY Host, User');
    $r = $J;
    if (! $J) {
        $J = $e->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
    }
    echo"<form action=''><p>\n";
    hidden_fields_get();
    echo"<input type='hidden' name='db' value='".h(DB)."'>\n",($r ? '' : "<input type='hidden' name='grant' value=''>\n"),"<table cellspacing='0'>\n",'<thead><tr><th>'.'Username'.'<th>'.'Server'."<th>&nbsp;</thead>\n";
    while ($L = $J->fetch_assoc()) {
        echo'<tr'.odd().'><td>'.h($L['User']).'<td>'.h($L['Host']).'<td><a href="'.h(ME.'user='.urlencode($L['User']).'&host='.urlencode($L['Host'])).'">'.'Edit'."</a>\n";
    }
    if (! $r || DB != '') {
        echo'<tr'.odd()."><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'Edit'."'>\n";
    }
    echo"</table>\n","</form>\n";
} elseif (isset($_GET['sql'])) {
    if (! $k && $_POST['export']) {
        dump_headers('sql');
        $b->dumpTable('', '');
        $b->dumpData('', 'table', $_POST['query']);
        exit;
    }
    restart_session();
    $Mc = &get_session('queries');
    $Lc = &$Mc[DB];
    if (! $k && $_POST['clear']) {
        $Lc = [];
        redirect(remove_from_uri('history'));
    }
    page_header((isset($_GET['import']) ? 'Import' : 'SQL command'), $k);
    if (! $k && $_POST) {
        $p = false;
        if (! isset($_GET['import'])) {
            $I = $_POST['query'];
        } elseif ($_POST['webfile']) {
            $Qf = $b->importServerPath();
            $p = @fopen((file_exists($Qf) ? $Qf : "compress.zlib://$Qf.gz"), 'rb');
            $I = ($p ? fread($p, 1e6) : false);
        } else {
            $I = get_file('sql_file', true);
        }
        if (is_string($I)) {
            if (function_exists('memory_get_usage')) {
                @ini_set('memory_limit', max(ini_bytes('memory_limit'), 2 * strlen($I) + memory_get_usage() + 8e6));
            }
            if ($I != '' && strlen($I) < 1e6) {
                $H = $I.(preg_match("~;[ \t\r\n]*\$~", $I) ? '' : ';');
                if (! $Lc || reset(end($Lc)) != $H) {
                    restart_session();
                    $Lc[] = [$H, time()];
                    set_session('queries', $Mc);
                    stop_session();
                }
            }
            $Of = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
            $_b = ';';
            $Zd = 0;
            $Wb = true;
            $f = connect();
            if (is_object($f) && DB != '') {
                $f->select_db(DB);
            }
            $ab = 0;
            $bc = [];
            $Ce = '[\'"'.($y == 'sql' ? '`#' : ($y == 'sqlite' ? '`[' : ($y == 'mssql' ? '[' : ''))).']|/\\*|-- |$'.($y == 'pgsql' ? '|\\$[^$]*\\$' : '');
            $Cg = microtime(true);
            parse_str($_COOKIE['adminer_export'], $la);
            $Nb = $b->dumpFormat();
            unset($Nb['sql']);
            while ($I != '') {
                if (! $Zd && preg_match("~^$Of*+DELIMITER\\s+(\\S+)~i", $I, $C)) {
                    $_b = $C[1];
                    $I = substr($I, strlen($C[0]));
                } else {
                    preg_match('('.preg_quote($_b)."\\s*|$Ce)", $I, $C, PREG_OFFSET_CAPTURE, $Zd);
                    [$zc, $Ne] = $C[0];
                    if (! $zc && $p && ! feof($p)) {
                        $I .= fread($p, 1e5);
                    } else {
                        if (! $zc && rtrim($I) == '') {
                            break;
                        }
                        $Zd = $Ne + strlen($zc);
                        if ($zc && rtrim($zc) != $_b) {
                            while (preg_match('('.($zc == '/*' ? '\\*/' : ($zc == '[' ? ']' : (preg_match('~^-- |^#~', $zc) ? "\n" : preg_quote($zc).'|\\\\.'))).'|$)s', $I, $C, PREG_OFFSET_CAPTURE, $Zd)) {
                                $xf = $C[0][0];
                                if (! $xf && $p && ! feof($p)) {
                                    $I .= fread($p, 1e5);
                                } else {
                                    $Zd = $C[0][1] + strlen($xf);
                                    if ($xf[0] != '\\') {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $Wb = false;
                            $H = substr($I, 0, $Ne);
                            $ab++;
                            $Ue = "<pre id='sql-$ab'><code class='jush-$y'>".$b->sqlCommandQuery($H)."</code></pre>\n";
                            if ($y == 'sqlite' && preg_match("~^$Of*+ATTACH\\b~i", $H, $C)) {
                                echo$Ue,"<p class='error'>".'ATTACH queries are not supported.'."\n";
                                $bc[] = " <a href='#sql-$ab'>$ab</a>";
                                if ($_POST['error_stops']) {
                                    break;
                                }
                            } else {
                                if (! $_POST['only_errors']) {
                                    echo$Ue;
                                    ob_flush();
                                    flush();
                                }
                                $Tf = microtime(true);
                                if ($e->multi_query($H) && is_object($f) && preg_match("~^$Of*+USE\\b~i", $H)) {
                                    $f->query($H);
                                }
                                do {
                                    $J = $e->store_result();
                                    if ($e->error) {
                                        echo($_POST['only_errors'] ? $Ue : ''),"<p class='error'>".'Error in query'.($e->errno ? " ($e->errno)" : '').': '.error()."\n";
                                        $bc[] = " <a href='#sql-$ab'>$ab</a>";
                                        if ($_POST['error_stops']) {
                                            break
                                            2;
                                        }
                                    } else {
                                        $tg = " <span class='time'>(".format_time($Tf).')</span>'.(strlen($H) < 1000 ? " <a href='".h(ME).'sql='.urlencode(trim($H))."'>".'Edit'.'</a>' : '');
                                        $na = $e->affected_rows;
                                        $jh = ($_POST['only_errors'] ? '' : $j->warnings());
                                        $kh = "warnings-$ab";
                                        if ($jh) {
                                            $tg .= ", <a href='#$kh'>".'Warnings'.'</a>'.script("qsl('a').onclick = partial(toggle, '$kh');", '');
                                        }
                                        $ic = null;
                                        $jc = "explain-$ab";
                                        if (is_object($J)) {
                                            $_ = $_POST['limit'];
                                            $re = select($J, $f, [], $_);
                                            if (! $_POST['only_errors']) {
                                                echo"<form action='' method='post'>\n";
                                                $Wd = $J->num_rows;
                                                echo'<p>'.($Wd ? ($_ && $Wd > $_ ? sprintf('%d / ', $_) : '').lang(['%d row', '%d rows'], $Wd) : ''),$tg;
                                                if ($f && preg_match("~^($Of|\\()*+SELECT\\b~i", $H) && ($ic = explain($f, $H))) {
                                                    echo", <a href='#$jc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$jc');", '');
                                                }
                                                $u = "export-$ab";
                                                echo", <a href='#$u'>".'Export'.'</a>'.script("qsl('a').onclick = partial(toggle, '$u');", '')."<span id='$u' class='hidden'>: ".html_select('output', $b->dumpOutput(), $la['output']).' '.html_select('format', $Nb, $la['format'])."<input type='hidden' name='query' value='".h($H)."'>"." <input type='submit' name='export' value='".'Export'."'><input type='hidden' name='token' value='$T'></span>\n"."</form>\n";
                                            }
                                        } else {
                                            if (preg_match("~^$Of*+(CREATE|DROP|ALTER)$Of++(DATABASE|SCHEMA)\\b~i", $H)) {
                                                restart_session();
                                                set_session('dbs', null);
                                                stop_session();
                                            }
                                            if (! $_POST['only_errors']) {
                                                echo"<p class='message' title='".h($e->info)."'>".lang(['Query executed OK, %d row affected.', 'Query executed OK, %d rows affected.'], $na)."$tg\n";
                                            }
                                        }
                                        echo $jh ? "<div id='$kh' class='hidden'>\n$jh</div>\n" : '';
                                        if ($ic) {
                                            echo"<div id='$jc' class='hidden'>\n";
                                            select($ic, $f, $re);
                                            echo"</div>\n";
                                        }
                                    }
                                    $Tf = microtime(true);
                                } while ($e->next_result());
                            }
                            $I = substr($I, $Zd);
                            $Zd = 0;
                        }
                    }
                }
            }
            if ($Wb) {
                echo"<p class='message'>".'No commands to execute.'."\n";
            } elseif ($_POST['only_errors']) {
                echo"<p class='message'>".lang(['%d query executed OK.', '%d queries executed OK.'], $ab - count($bc))," <span class='time'>(".format_time($Cg).")</span>\n";
            } elseif ($bc && $ab > 1) {
                echo"<p class='error'>".'Error in query'.': '.implode('', $bc)."\n";
            }
        } else {
            echo"<p class='error'>".upload_error($I)."\n";
        }
    }
    echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';
    $gc = "<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";
    if (! isset($_GET['import'])) {
        $H = $_GET['sql'];
        if ($_POST) {
            $H = $_POST['query'];
        } elseif ($_GET['history'] == 'all') {
            $H = $Lc;
        } elseif ($_GET['history'] != '') {
            $H = $Lc[$_GET['history']][0];
        }
        echo'<p>';
        textarea('query', $H, 20);
        echo($_POST ? '' : script("qs('textarea').focus();")),"<p>$gc\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST ? $_POST['limit'] : $_GET['limit'])."'>\n";
    } else {
        echo'<fieldset><legend>'.'File upload'.'</legend><div>',(ini_bool('file_uploads') ? 'SQL (&lt; '.ini_get('upload_max_filesize')."B): <input type='file' name='sql_file[]' multiple>\n$gc" : 'File uploads are disabled.'),"</div></fieldset>\n",'<fieldset><legend>'.'From server'.'</legend><div>',sprintf('Webserver file %s', '<code>'.h($b->importServerPath()).(extension_loaded('zlib') ? '[.gz]' : '').'</code>'),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n",'<p>';
    }
    echo
    checkbox('error_stops', 1, ($_POST ? $_POST['error_stops'] : isset($_GET['import'])), 'Stop on error')."\n",checkbox('only_errors', 1, ($_POST ? $_POST['only_errors'] : isset($_GET['import'])), 'Show only errors')."\n","<input type='hidden' name='token' value='$T'>\n";
    if (! isset($_GET['import']) && $Lc) {
        print_fieldset('history', 'History', $_GET['history'] != '');
        for ($X = end($Lc); $X; $X = prev($Lc)) {
            $z = key($Lc);
            [$H, $tg, $Rb] = $X;
            echo'<a href="'.h(ME."sql=&history=$z").'">'.'Edit'.'</a>'." <span class='time' title='".@date('Y-m-d', $tg)."'>".@date('H:i:s', $tg).'</span>'." <code class='jush-$y'>".shorten_utf8(ltrim(str_replace("\n", ' ', str_replace("\r", '', preg_replace('~^(#|-- ).*~m', '', $H)))), 80, '</code>').($Rb ? " <span class='time'>($Rb)</span>" : '')."<br>\n";
        }
        echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME.'sql=&history=all')."'>".'Edit all'."</a>\n","</div></fieldset>\n";
    }
    echo'</form>
';
} elseif (isset($_GET['edit'])) {
    $a = $_GET['edit'];
    $m = fields($a);
    $Z = (isset($_GET['select']) ? ($_POST['check'] && count($_POST['check']) == 1 ? where_check($_POST['check'][0], $m) : '') : where($_GET, $m));
    $Tg = (isset($_GET['select']) ? $_POST['edit'] : $Z);
    foreach ($m
    as$E => $l) {
        if (! isset($l['privileges'][$Tg ? 'update' : 'insert']) || $b->fieldName($l) == '') {
            unset($m[$E]);
        }
    }
    if ($_POST && ! $k && ! isset($_GET['select'])) {
        $B = $_POST['referer'];
        if ($_POST['insert']) {
            $B = ($Tg ? null : $_SERVER['REQUEST_URI']);
        } elseif (! preg_match('~^.+&select=.+$~', $B)) {
            $B = ME.'select='.urlencode($a);
        }
        $w = indexes($a);
        $Og = unique_array($_GET['where'], $w);
        $df = "\nWHERE $Z";
        if (isset($_POST['delete'])) {
            queries_redirect($B, 'Item has been deleted.', $j->delete($a, $df, ! $Og));
        } else {
            $P = [];
            foreach ($m
            as$E => $l) {
                $X = process_input($l);
                if ($X !== false && $X !== null) {
                    $P[idf_escape($E)] = $X;
                }
            }
            if ($Tg) {
                if (! $P) {
                    redirect($B);
                }
                queries_redirect($B, 'Item has been updated.', $j->update($a, $P, $df, ! $Og));
                if (is_ajax()) {
                    page_headers();
                    page_messages($k);
                    exit;
                }
            } else {
                $J = $j->insert($a, $P);
                $nd = ($J ? last_id() : 0);
                queries_redirect($B, sprintf('Item%s has been inserted.', ($nd ? " $nd" : '')), $J);
            }
        }
    }
    $L = null;
    if ($_POST['save']) {
        $L = (array) $_POST['fields'];
    } elseif ($Z) {
        $N = [];
        foreach ($m
        as$E => $l) {
            if (isset($l['privileges']['select'])) {
                $ua = convert_field($l);
                if ($_POST['clone'] && $l['auto_increment']) {
                    $ua = "''";
                }
                if ($y == 'sql' && preg_match('~enum|set~', $l['type'])) {
                    $ua = '1*'.idf_escape($E);
                }
                $N[] = ($ua ? "$ua AS " : '').idf_escape($E);
            }
        }
        $L = [];
        if (! support('table')) {
            $N = ['*'];
        }
        if ($N) {
            $J = $j->select($a, $N, [$Z], $N, [], (isset($_GET['select']) ? 2 : 1));
            if (! $J) {
                $k = error();
            } else {
                $L = $J->fetch_assoc();
                if (! $L) {
                    $L = false;
                }
            }
            if (isset($_GET['select']) && (! $L || $J->fetch_assoc())) {
                $L = null;
            }
        }
    }
    if (! support('table') && ! $m) {
        if (! $Z) {
            $J = $j->select($a, ['*'], $Z, ['*']);
            $L = ($J ? $J->fetch_assoc() : false);
            if (! $L) {
                $L = [$j->primary => ''];
            }
        }
        if ($L) {
            foreach ($L
            as$z => $X) {
                if (! $Z) {
                    $L[$z] = null;
                }
                $m[$z] = ['field' => $z, 'null' => ($z != $j->primary), 'auto_increment' => ($z == $j->primary)];
            }
        }
    }
    edit_form($a, $m, $L, $Tg);
} elseif (isset($_GET['create'])) {
    $a = $_GET['create'];
    $De = [];
    foreach (['HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST']as$z) {
        $De[$z] = $z;
    }
    $jf = referencable_primary($a);
    $o = [];
    foreach ($jf
    as$gg => $l) {
        $o[str_replace('`', '``', $gg).'`'.str_replace('`', '``', $l['field'])] = $gg;
    }
    $ue = [];
    $R = [];
    if ($a != '') {
        $ue = fields($a);
        $R = table_status($a);
        if (! $R) {
            $k = 'No tables.';
        }
    }
    $L = $_POST;
    $L['fields'] = (array) $L['fields'];
    if ($L['auto_increment_col']) {
        $L['fields'][$L['auto_increment_col']]['auto_increment'] = true;
    }
    if ($_POST && ! process_fields($L['fields']) && ! $k) {
        if ($_POST['drop']) {
            queries_redirect(substr(ME, 0, -1), 'Table has been dropped.', drop_tables([$a]));
        } else {
            $m = [];
            $ra = [];
            $Xg = false;
            $wc = [];
            $te = reset($ue);
            $pa = ' FIRST';
            foreach ($L['fields']as$z => $l) {
                $n = $o[$l['type']];
                $Jg = ($n !== null ? $jf[$n] : $l);
                if ($l['field'] != '') {
                    if (! $l['has_default']) {
                        $l['default'] = null;
                    }
                    if ($z == $L['auto_increment_col']) {
                        $l['auto_increment'] = true;
                    }
                    $Ze = process_field($l, $Jg);
                    $ra[] = [$l['orig'], $Ze, $pa];
                    if ($Ze != process_field($te, $te)) {
                        $m[] = [$l['orig'], $Ze, $pa];
                        if ($l['orig'] != '' || $pa) {
                            $Xg = true;
                        }
                    }
                    if ($n !== null) {
                        $wc[idf_escape($l['field'])] = ($a != '' && $y != 'sqlite' ? 'ADD' : ' ').format_foreign_key(['table' => $o[$l['type']], 'source' => [$l['field']], 'target' => [$Jg['field']], 'on_delete' => $l['on_delete']]);
                    }
                    $pa = ' AFTER '.idf_escape($l['field']);
                } elseif ($l['orig'] != '') {
                    $Xg = true;
                    $m[] = [$l['orig']];
                }
                if ($l['orig'] != '') {
                    $te = next($ue);
                    if (! $te) {
                        $pa = '';
                    }
                }
            }
            $Fe = '';
            if ($De[$L['partition_by']]) {
                $Ge = [];
                if ($L['partition_by'] == 'RANGE' || $L['partition_by'] == 'LIST') {
                    foreach (array_filter($L['partition_names'])as$z => $X) {
                        $Y = $L['partition_values'][$z];
                        $Ge[] = "\n  PARTITION ".idf_escape($X).' VALUES '.($L['partition_by'] == 'RANGE' ? 'LESS THAN' : 'IN').($Y != '' ? " ($Y)" : ' MAXVALUE');
                    }
                }
                $Fe .= "\nPARTITION BY $L[partition_by]($L[partition])".($Ge ? ' ('.implode(',', $Ge)."\n)" : ($L['partitions'] ? ' PARTITIONS '.(+$L['partitions']) : ''));
            } elseif (support('partitioning') && preg_match('~partitioned~', $R['Create_options'])) {
                $Fe .= "\nREMOVE PARTITIONING";
            }
            $D = 'Table has been altered.';
            if ($a == '') {
                cookie('adminer_engine', $L['Engine']);
                $D = 'Table has been created.';
            }
            $E = trim($L['name']);
            queries_redirect(ME.(support('table') ? 'table=' : 'select=').urlencode($E), $D, alter_table($a, $E, ($y == 'sqlite' && ($Xg || $wc) ? $ra : $m), $wc, ($L['Comment'] != $R['Comment'] ? $L['Comment'] : null), ($L['Engine'] && $L['Engine'] != $R['Engine'] ? $L['Engine'] : ''), ($L['Collation'] && $L['Collation'] != $R['Collation'] ? $L['Collation'] : ''), ($L['Auto_increment'] != '' ? number($L['Auto_increment']) : ''), $Fe));
        }
    }
    page_header(($a != '' ? 'Alter table' : 'Create table'), $k, ['table' => $a], h($a));
    if (! $_POST) {
        $L = ['Engine' => $_COOKIE['adminer_engine'], 'fields' => [['field' => '', 'type' => (isset($Lg['int']) ? 'int' : (isset($Lg['integer']) ? 'integer' : '')), 'on_update' => '']], 'partition_names' => ['']];
        if ($a != '') {
            $L = $R;
            $L['name'] = $a;
            $L['fields'] = [];
            if (! $_GET['auto_increment']) {
                $L['Auto_increment'] = '';
            }
            foreach ($ue
            as$l) {
                $l['has_default'] = isset($l['default']);
                $L['fields'][] = $l;
            }
            if (support('partitioning')) {
                $Ac = 'FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = '.q(DB).' AND TABLE_NAME = '.q($a);
                $J = $e->query("SELECT PARTITION_METHOD, PARTITION_ORDINAL_POSITION, PARTITION_EXPRESSION $Ac ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
                [$L['partition_by'], $L['partitions'], $L['partition']] = $J->fetch_row();
                $Ge = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Ac AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
                $Ge[''] = '';
                $L['partition_names'] = array_keys($Ge);
                $L['partition_values'] = array_values($Ge);
            }
        }
    }
    $Xa = collations();
    $Yb = engines();
    foreach ($Yb
    as$Xb) {
        if (! strcasecmp($Xb, $L['Engine'])) {
            $L['Engine'] = $Xb;
            break;
        }
    }
    echo'
<form action="" method="post" id="form">
<p>
';
    if (support('columns') || $a == '') {
        echo'Table name: <input name="name" maxlength="64" value="',h($L['name']),'" autocapitalize="off">
';
        if ($a == '' && ! $_POST) {
            echo
            script("focus(qs('#form')['name']);");
        }
        echo($Yb ? "<select name='Engine'>".optionlist(['' => '('.'engine'.')'] + $Yb, $L['Engine']).'</select>'.on_help('getTarget(event).value', 1).script("qsl('select').onchange = helpClose;") : ''),' ',($Xa && ! preg_match('~sqlite|mssql~', $y) ? html_select('Collation', ['' => '('.'collation'.')'] + $Xa, $L['Collation']) : ''),' <input type="submit" value="Save">
';
    }
    echo'
';
    if (support('columns')) {
        echo'<table cellspacing="0" id="edit-fields" class="nowrap">
';
        $cb = ($_POST ? $_POST['comments'] : $L['Comment'] != '');
        if (! $_POST && ! $cb) {
            foreach ($L['fields']as$l) {
                if ($l['comment'] != '') {
                    $cb = true;
                    break;
                }
            }
        }
        edit_fields($L['fields'], $Xa, 'TABLE', $o, $cb);
        echo'</table>
<p>
Auto Increment: <input type="number" name="Auto_increment" size="6" value="',h($L['Auto_increment']),'">
',checkbox('defaults', 1, ! $_POST || $_POST['defaults'], 'Default values', 'columnShow(this.checked, 5)', 'jsonly'),($_POST ? '' : script('editingHideDefaults();')),(support('comment') ? "<label><input type='checkbox' name='comments' value='1' class='jsonly'".($cb ? ' checked' : '').'>'.'Comment'.'</label>'.script("qsl('input').onclick = partial(editingCommentsClick, true);").' <input name="Comment" value="'.h($L['Comment']).'" maxlength="'.(min_version(5.5) ? 2048 : 60).'"'.($cb ? '' : ' class="hidden"').'>' : ''),'<p>
<input type="submit" value="Save">
';
    }
    echo'
';
    if ($a != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $a));
    }
    if (support('partitioning')) {
        $Ee = preg_match('~RANGE|LIST~', $L['partition_by']);
        print_fieldset('partition', 'Partition by', $L['partition_by']);
        echo'<p>
',"<select name='partition_by'>".optionlist(['' => ''] + $De, $L['partition_by']).'</select>'.on_help("getTarget(event).value.replace(/./, 'PARTITION BY \$&')", 1).script("qsl('select').onchange = partitionByChange;"),'(<input name="partition" value="',h($L['partition']),'">)
Partitions: <input type="number" name="partitions" class="size',($Ee || ! $L['partition_by'] ? ' hidden' : ''),'" value="',h($L['partitions']),'">
<table cellspacing="0" id="partition-table"',($Ee ? '' : " class='hidden'"),'>
<thead><tr><th>Partition name<th>Values</thead>
';
        foreach ($L['partition_names']as$z => $X) {
            echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($z == count($L['partition_names']) - 1 ? script("qsl('input').oninput = partitionNameChange;") : ''),'<td><input name="partition_values[]" value="'.h($L['partition_values'][$z]).'">';
        }
        echo'</table>
</div></fieldset>
';
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
',script("qs('#form')['defaults'].onclick();".(support('comment') ? " editingCommentsClick.call(qs('#form')['comments']);" : ''));
} elseif (isset($_GET['indexes'])) {
    $a = $_GET['indexes'];
    $Tc = ['PRIMARY', 'UNIQUE', 'INDEX'];
    $R = table_status($a, true);
    if (preg_match('~MyISAM|M?aria'.(min_version(5.6, '10.0.5') ? '|InnoDB' : '').'~i', $R['Engine'])) {
        $Tc[] = 'FULLTEXT';
    }
    if (preg_match('~MyISAM|M?aria'.(min_version(5.7, '10.2.2') ? '|InnoDB' : '').'~i', $R['Engine'])) {
        $Tc[] = 'SPATIAL';
    }
    $w = indexes($a);
    $Te = [];
    if ($y == 'mongo') {
        $Te = $w['_id_'];
        unset($Tc[0]);
        unset($w['_id_']);
    }
    $L = $_POST;
    if ($_POST && ! $k && ! $_POST['add'] && ! $_POST['drop_col']) {
        $sa = [];
        foreach ($L['indexes']as$v) {
            $E = $v['name'];
            if (in_array($v['type'], $Tc)) {
                $d = [];
                $td = [];
                $Bb = [];
                $P = [];
                ksort($v['columns']);
                foreach ($v['columns']as$z => $c) {
                    if ($c != '') {
                        $sd = $v['lengths'][$z];
                        $Ab = $v['descs'][$z];
                        $P[] = idf_escape($c).($sd ? '('.(+$sd).')' : '').($Ab ? ' DESC' : '');
                        $d[] = $c;
                        $td[] = ($sd ? $sd : null);
                        $Bb[] = $Ab;
                    }
                }
                if ($d) {
                    $hc = $w[$E];
                    if ($hc) {
                        ksort($hc['columns']);
                        ksort($hc['lengths']);
                        ksort($hc['descs']);
                        if ($v['type'] == $hc['type'] && array_values($hc['columns']) === $d && (! $hc['lengths'] || array_values($hc['lengths']) === $td) && array_values($hc['descs']) === $Bb) {
                            unset($w[$E]);

                            continue;
                        }
                    }
                    $sa[] = [$v['type'], $E, $P];
                }
            }
        }
        foreach ($w
        as$E => $hc) {
            $sa[] = [$hc['type'], $E, 'DROP'];
        }
        if (! $sa) {
            redirect(ME.'table='.urlencode($a));
        }
        queries_redirect(ME.'table='.urlencode($a), 'Indexes have been altered.', alter_indexes($a, $sa));
    }
    page_header('Indexes', $k, ['table' => $a], h($a));
    $m = array_keys(fields($a));
    if ($_POST['add']) {
        foreach ($L['indexes']as$z => $v) {
            if ($v['columns'][count($v['columns'])] != '') {
                $L['indexes'][$z]['columns'][] = '';
            }
        }
        $v = end($L['indexes']);
        if ($v['type'] || array_filter($v['columns'], 'strlen')) {
            $L['indexes'][] = ['columns' => [1 => '']];
        }
    }
    if (! $L) {
        foreach ($w
        as$z => $v) {
            $w[$z]['name'] = $z;
            $w[$z]['columns'][] = '';
        }
        $w[] = ['columns' => [1 => '']];
        $L['indexes'] = $w;
    } ?>

<form action="" method="post">
<table cellspacing="0" class="nowrap">
<thead><tr>
<th id="label-type">Index Type
<th><input type="submit" class="wayoff">Column (length)
<th id="label-name">Name
<th><noscript><input type='image' class='icon' name='add[0]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=4.6.2&driver=mysql") . "' alt='+' title='Add next'></noscript>&nbsp;
</thead>
<?php
if ($Te) {
    echo'<tr><td>PRIMARY<td>';
    foreach ($Te['columns']as$z => $c) {
        echo
        select_input(' disabled', $m, $c),"<label><input disabled type='checkbox'>".'descending'.'</label> ';
    }
    echo"<td><td>\n";
}
    $x = 1;
    foreach ($L['indexes']as$v) {
        if (! $_POST['drop_col'] || $x != key($_POST['drop_col'])) {
            echo'<tr><td>'.html_select("indexes[$x][type]", [-1 => ''] + $Tc, $v['type'], ($x == count($L['indexes']) ? 'indexesAddRow.call(this);' : 1), 'label-type'),'<td>';
            ksort($v['columns']);
            $t = 1;
            foreach ($v['columns']as$z => $c) {
                echo'<span>'.select_input(" name='indexes[$x][columns][$t]' title='".'Column'."'", ($m ? array_combine($m, $m) : $m), $c, 'partial('.($t == count($v['columns']) ? 'indexesAddColumn' : 'indexesChangeColumn').", '".js_escape($y == 'sql' ? '' : $_GET['indexes'].'_')."')"),($y == 'sql' || $y == 'mssql' ? "<input type='number' name='indexes[$x][lengths][$t]' class='size' value='".h($v['lengths'][$z])."' title='".'Length'."'>" : ''),($y != 'sql' ? checkbox("indexes[$x][descs][$t]", 1, $v['descs'][$z], 'descending') : ''),' </span>';
                $t++;
            }
            echo"<td><input name='indexes[$x][name]' value='".h($v['name'])."' autocapitalize='off' aria-labelledby='label-name'>\n","<td><input type='image' class='icon' name='drop_col[$x]' src='".h(preg_replace('~\\?.*~', '', ME).'?file=cross.gif&version=4.6.2&driver=mysql')."' alt='x' title='".'Remove'."'>".script("qsl('input').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");
        }
        $x++;
    }
    echo'</table>
<p>
<input type="submit" value="Save">
<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['database'])) {
    $L = $_POST;
    if ($_POST && ! $k && ! isset($_POST['add_x'])) {
        $E = trim($L['name']);
        if ($_POST['drop']) {
            $_GET['db'] = '';
            queries_redirect(remove_from_uri('db|database'), 'Database has been dropped.', drop_databases([DB]));
        } elseif (DB !== $E) {
            if (DB != '') {
                $_GET['db'] = $E;
                queries_redirect(preg_replace('~\bdb=[^&]*&~', '', ME).'db='.urlencode($E), 'Database has been renamed.', rename_database($E, $L['collation']));
            } else {
                $h = explode("\n", str_replace("\r", '', $E));
                $ag = true;
                $md = '';
                foreach ($h
                as$i) {
                    if (count($h) == 1 || $i != '') {
                        if (! create_database($i, $L['collation'])) {
                            $ag = false;
                        }
                        $md = $i;
                    }
                }
                restart_session();
                set_session('dbs', null);
                queries_redirect(ME.'db='.urlencode($md), 'Database has been created.', $ag);
            }
        } else {
            if (! $L['collation']) {
                redirect(substr(ME, 0, -1));
            }
            query_redirect('ALTER DATABASE '.idf_escape($E).(preg_match('~^[a-z0-9_]+$~i', $L['collation']) ? " COLLATE $L[collation]" : ''), substr(ME, 0, -1), 'Database has been altered.');
        }
    }
    page_header(DB != '' ? 'Alter database' : 'Create database', $k, [], h(DB));
    $Xa = collations();
    $E = DB;
    if ($_POST) {
        $E = $L['name'];
    } elseif (DB != '') {
        $L['collation'] = db_collation(DB, $Xa);
    } elseif ($y == 'sql') {
        foreach (get_vals('SHOW GRANTS')as$r) {
            if (preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\\.\\*)?~', $r, $C) && $C[1]) {
                $E = stripcslashes(idf_unescape("`$C[2]`"));
                break;
            }
        }
    }
    echo'
<form action="" method="post">
<p>
',($_POST['add_x'] || strpos($E, "\n") ? '<textarea id="name" name="name" rows="10" cols="40">'.h($E).'</textarea><br>' : '<input name="name" id="name" value="'.h($E).'" maxlength="64" autocapitalize="off">')."\n".($Xa ? html_select('collation', ['' => '('.'collation'.')'] + $Xa, $L['collation']).doc_link(['sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/', 'mssql' => 'ms187963.aspx']) : ''),script("focus(qs('#name'));"),'<input type="submit" value="Save">
';
    if (DB != '') {
        echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?', DB))."\n";
    } elseif (! $_POST['add_x'] && $_GET['db'] == '') {
        echo"<input type='image' class='icon' name='add' src='".h(preg_replace('~\\?.*~', '', ME).'?file=plus.gif&version=4.6.2&driver=mysql')."' alt='+' title='".'Add next'."'>\n";
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['call'])) {
    $da = ($_GET['name'] ? $_GET['name'] : $_GET['call']);
    page_header('Call'.': '.h($da), $k);
    $uf = routine($_GET['call'], (isset($_GET['callf']) ? 'FUNCTION' : 'PROCEDURE'));
    $Sc = [];
    $xe = [];
    foreach ($uf['fields']as$t => $l) {
        if (substr($l['inout'], -3) == 'OUT') {
            $xe[$t] = '@'.idf_escape($l['field']).' AS '.idf_escape($l['field']);
        }
        if (! $l['inout'] || substr($l['inout'], 0, 2) == 'IN') {
            $Sc[] = $t;
        }
    }
    if (! $k && $_POST) {
        $Ja = [];
        foreach ($uf['fields']as$z => $l) {
            if (in_array($z, $Sc)) {
                $X = process_input($l);
                if ($X === false) {
                    $X = "''";
                }
                if (isset($xe[$z])) {
                    $e->query('SET @'.idf_escape($l['field'])." = $X");
                }
            }
            $Ja[] = (isset($xe[$z]) ? '@'.idf_escape($l['field']) : $X);
        }
        $I = (isset($_GET['callf']) ? 'SELECT' : 'CALL').' '.table($da).'('.implode(', ', $Ja).')';
        $Tf = microtime(true);
        $J = $e->multi_query($I);
        $na = $e->affected_rows;
        echo$b->selectQuery($I, $Tf, ! $J);
        if (! $J) {
            echo"<p class='error'>".error()."\n";
        } else {
            $f = connect();
            if (is_object($f)) {
                $f->select_db(DB);
            }
            do {
                $J = $e->store_result();
                if (is_object($J)) {
                    select($J, $f);
                } else {
                    echo"<p class='message'>".lang(['Routine has been called, %d row affected.', 'Routine has been called, %d rows affected.'], $na)."\n";
                }
            } while ($e->next_result());
            if ($xe) {
                select($e->query('SELECT '.implode(', ', $xe)));
            }
        }
    }
    echo'
<form action="" method="post">
';
    if ($Sc) {
        echo"<table cellspacing='0'>\n";
        foreach ($Sc
        as$z) {
            $l = $uf['fields'][$z];
            $E = $l['field'];
            echo'<tr><th>'.$b->fieldName($l);
            $Y = $_POST['fields'][$E];
            if ($Y != '') {
                if ($l['type'] == 'enum') {
                    $Y = +$Y;
                }
                if ($l['type'] == 'set') {
                    $Y = array_sum($Y);
                }
            }
            input($l, $Y, (string) $_POST['function'][$E]);
            echo"\n";
        }
        echo"</table>\n";
    }
    echo'<p>
<input type="submit" value="Call">
<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['foreign'])) {
    $a = $_GET['foreign'];
    $E = $_GET['name'];
    $L = $_POST;
    if ($_POST && ! $k && ! $_POST['add'] && ! $_POST['change'] && ! $_POST['change-js']) {
        $D = ($_POST['drop'] ? 'Foreign key has been dropped.' : ($E != '' ? 'Foreign key has been altered.' : 'Foreign key has been created.'));
        $B = ME.'table='.urlencode($a);
        if (! $_POST['drop']) {
            $L['source'] = array_filter($L['source'], 'strlen');
            ksort($L['source']);
            $ng = [];
            foreach ($L['source']as$z => $X) {
                $ng[$z] = $L['target'][$z];
            }
            $L['target'] = $ng;
        }
        if ($y == 'sqlite') {
            queries_redirect($B, $D, recreate_table($a, $a, [], [], [" $E" => ($_POST['drop'] ? '' : ' '.format_foreign_key($L))]));
        } else {
            $sa = 'ALTER TABLE '.table($a);
            $Jb = "\nDROP ".($y == 'sql' ? 'FOREIGN KEY ' : 'CONSTRAINT ').idf_escape($E);
            if ($_POST['drop']) {
                query_redirect($sa.$Jb, $B, $D);
            } else {
                query_redirect($sa.($E != '' ? "$Jb," : '')."\nADD".format_foreign_key($L), $B, $D);
                $k = 'Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.'."<br>$k";
            }
        }
    }
    page_header('Foreign key', $k, ['table' => $a], h($a));
    if ($_POST) {
        ksort($L['source']);
        if ($_POST['add']) {
            $L['source'][] = '';
        } elseif ($_POST['change'] || $_POST['change-js']) {
            $L['target'] = [];
        }
    } elseif ($E != '') {
        $o = foreign_keys($a);
        $L = $o[$E];
        $L['source'][] = '';
    } else {
        $L['table'] = $a;
        $L['source'] = [''];
    }
    $Nf = array_keys(fields($a));
    $ng = ($a === $L['table'] ? $Nf : array_keys(fields($L['table'])));
    $if = array_keys(array_filter(table_status('', true), 'fk_support'));
    echo'
<form action="" method="post">
<p>
';
    if ($L['db'] == '' && $L['ns'] == '') {
        echo'Target table:
',html_select('table', $if, $L['table'], "this.form['change-js'].value = '1'; this.form.submit();"),'<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table cellspacing="0">
<thead><tr><th id="label-source">Source<th id="label-target">Target</thead>
';
        $x = 0;
        foreach ($L['source']as$z => $X) {
            echo'<tr>','<td>'.html_select('source['.(+$z).']', [-1 => ''] + $Nf, $X, ($x == count($L['source']) - 1 ? 'foreignAddRow.call(this);' : 1), 'label-source'),'<td>'.html_select('target['.(+$z).']', $ng, $L['target'][$z], 1, 'label-target');
            $x++;
        }
        echo'</table>
<p>
ON DELETE: ',html_select('on_delete', [-1 => ''] + explode('|', $ge), $L['on_delete']),' ON UPDATE: ',html_select('on_update', [-1 => ''] + explode('|', $ge), $L['on_update']),doc_link(['sql' => 'innodb-foreign-key-constraints.html', 'mariadb' => 'foreign-keys/', 'pgsql' => 'sql-createtable.html#SQL-CREATETABLE-REFERENCES', 'mssql' => 'ms174979.aspx', 'oracle' => 'clauses002.htm#sthref2903']),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';
    }
    if ($E != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $E));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['view'])) {
    $a = $_GET['view'];
    $L = $_POST;
    $ve = 'VIEW';
    if ($y == 'pgsql' && $a != '') {
        $Uf = table_status($a);
        $ve = strtoupper($Uf['Engine']);
    }
    if ($_POST && ! $k) {
        $E = trim($L['name']);
        $ua = " AS\n$L[select]";
        $B = ME.'table='.urlencode($E);
        $D = 'View has been altered.';
        $U = ($_POST['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW');
        if (! $_POST['drop'] && $a == $E && $y != 'sqlite' && $U == 'VIEW' && $ve == 'VIEW') {
            query_redirect(($y == 'mssql' ? 'ALTER' : 'CREATE OR REPLACE').' VIEW '.table($E).$ua, $B, $D);
        } else {
            $pg = $E.'_adminer_'.uniqid();
            drop_create("DROP $ve ".table($a), "CREATE $U ".table($E).$ua, "DROP $U ".table($E), "CREATE $U ".table($pg).$ua, "DROP $U ".table($pg), ($_POST['drop'] ? substr(ME, 0, -1) : $B), 'View has been dropped.', $D, 'View has been created.', $a, $E);
        }
    }
    if (! $_POST && $a != '') {
        $L = view($a);
        $L['name'] = $a;
        $L['materialized'] = ($ve != 'VIEW');
        if (! $k) {
            $k = error();
        }
    }
    page_header(($a != '' ? 'Alter view' : 'Create view'), $k, ['table' => $a], h($a));
    echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($L['name']),'" maxlength="64" autocapitalize="off">
',(support('materializedview') ? ' '.checkbox('materialized', 1, $L['materialized'], 'Materialized view') : ''),'<p>';
    textarea('select', $L['select']);
    echo'<p>
<input type="submit" value="Save">
';
    if ($a != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $a));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['event'])) {
    $aa = $_GET['event'];
    $Yc = ['YEAR', 'QUARTER', 'MONTH', 'DAY', 'HOUR', 'MINUTE', 'WEEK', 'SECOND', 'YEAR_MONTH', 'DAY_HOUR', 'DAY_MINUTE', 'DAY_SECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'MINUTE_SECOND'];
    $Vf = ['ENABLED' => 'ENABLE', 'DISABLED' => 'DISABLE', 'SLAVESIDE_DISABLED' => 'DISABLE ON SLAVE'];
    $L = $_POST;
    if ($_POST && ! $k) {
        if ($_POST['drop']) {
            query_redirect('DROP EVENT '.idf_escape($aa), substr(ME, 0, -1), 'Event has been dropped.');
        } elseif (in_array($L['INTERVAL_FIELD'], $Yc) && isset($Vf[$L['STATUS']])) {
            $yf = "\nON SCHEDULE ".($L['INTERVAL_VALUE'] ? 'EVERY '.q($L['INTERVAL_VALUE'])." $L[INTERVAL_FIELD]".($L['STARTS'] ? ' STARTS '.q($L['STARTS']) : '').($L['ENDS'] ? ' ENDS '.q($L['ENDS']) : '') : 'AT '.q($L['STARTS'])).' ON COMPLETION'.($L['ON_COMPLETION'] ? '' : ' NOT').' PRESERVE';
            queries_redirect(substr(ME, 0, -1), ($aa != '' ? 'Event has been altered.' : 'Event has been created.'), queries(($aa != '' ? 'ALTER EVENT '.idf_escape($aa).$yf.($aa != $L['EVENT_NAME'] ? "\nRENAME TO ".idf_escape($L['EVENT_NAME']) : '') : 'CREATE EVENT '.idf_escape($L['EVENT_NAME']).$yf)."\n".$Vf[$L['STATUS']].' COMMENT '.q($L['EVENT_COMMENT']).rtrim(" DO\n$L[EVENT_DEFINITION]", ';').';'));
        }
    }
    page_header(($aa != '' ? 'Alter event'.': '.h($aa) : 'Create event'), $k);
    if (! $L && $aa != '') {
        $M = get_rows('SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = '.q(DB).' AND EVENT_NAME = '.q($aa));
        $L = reset($M);
    }
    echo'
<form action="" method="post">
<table cellspacing="0">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($L['EVENT_NAME']),'" maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$L[EXECUTE_AT]$L[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($L['ENDS']),'">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="',h($L['INTERVAL_VALUE']),'" class="size"> ',html_select('INTERVAL_FIELD', $Yc, $L['INTERVAL_FIELD']),'<tr><th>Status<td>',html_select('STATUS', $Vf, $L['STATUS']),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($L['EVENT_COMMENT']),'" maxlength="64">
<tr><th>&nbsp;<td>',checkbox('ON_COMPLETION', 'PRESERVE', $L['ON_COMPLETION'] == 'PRESERVE', 'On completion preserve'),'</table>
<p>';
    textarea('EVENT_DEFINITION', $L['EVENT_DEFINITION']);
    echo'<p>
<input type="submit" value="Save">
';
    if ($aa != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $aa));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['procedure'])) {
    $da = ($_GET['name'] ? $_GET['name'] : $_GET['procedure']);
    $uf = (isset($_GET['function']) ? 'FUNCTION' : 'PROCEDURE');
    $L = $_POST;
    $L['fields'] = (array) $L['fields'];
    if ($_POST && ! process_fields($L['fields']) && ! $k) {
        $se = routine($_GET['procedure'], $uf);
        $pg = "$L[name]_adminer_".uniqid();
        drop_create("DROP $uf ".routine_id($da, $se), create_routine($uf, $L), "DROP $uf ".routine_id($L['name'], $L), create_routine($uf, ['name' => $pg] + $L), "DROP $uf ".routine_id($pg, $L), substr(ME, 0, -1), 'Routine has been dropped.', 'Routine has been altered.', 'Routine has been created.', $da, $L['name']);
    }
    page_header(($da != '' ? (isset($_GET['function']) ? 'Alter function' : 'Alter procedure').': '.h($da) : (isset($_GET['function']) ? 'Create function' : 'Create procedure')), $k);
    if (! $_POST && $da != '') {
        $L = routine($_GET['procedure'], $uf);
        $L['name'] = $da;
    }
    $Xa = get_vals('SHOW CHARACTER SET');
    sort($Xa);
    $vf = routine_languages();
    echo'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($L['name']),'" maxlength="64" autocapitalize="off">
',($vf ? 'Language'.': '.html_select('language', $vf, $L['language'])."\n" : ''),'<input type="submit" value="Save">
<table cellspacing="0" class="nowrap">
';
    edit_fields($L['fields'], $Xa, $uf);
    if (isset($_GET['function'])) {
        echo'<tr><td>'.'Return type';
        edit_type('returns', $L['returns'], $Xa, [], ($y == 'pgsql' ? ['void', 'trigger'] : []));
    }
    echo'</table>
<p>';
    textarea('definition', $L['definition']);
    echo'<p>
<input type="submit" value="Save">
';
    if ($da != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $da));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['trigger'])) {
    $a = $_GET['trigger'];
    $E = $_GET['name'];
    $Hg = trigger_options();
    $L = (array) trigger($E) + ['Trigger' => $a.'_bi'];
    if ($_POST) {
        if (! $k && in_array($_POST['Timing'], $Hg['Timing']) && in_array($_POST['Event'], $Hg['Event']) && in_array($_POST['Type'], $Hg['Type'])) {
            $fe = ' ON '.table($a);
            $Jb = 'DROP TRIGGER '.idf_escape($E).($y == 'pgsql' ? $fe : '');
            $B = ME.'table='.urlencode($a);
            if ($_POST['drop']) {
                query_redirect($Jb, $B, 'Trigger has been dropped.');
            } else {
                if ($E != '') {
                    queries($Jb);
                }
                queries_redirect($B, ($E != '' ? 'Trigger has been altered.' : 'Trigger has been created.'), queries(create_trigger($fe, $_POST)));
                if ($E != '') {
                    queries(create_trigger($fe, $L + ['Type' => reset($Hg['Type'])]));
                }
            }
        }
        $L = $_POST;
    }
    page_header(($E != '' ? 'Alter trigger'.': '.h($E) : 'Create trigger'), $k, ['table' => $a]);
    echo'
<form action="" method="post" id="form">
<table cellspacing="0">
<tr><th>Time<td>',html_select('Timing', $Hg['Timing'], $L['Timing'], 'triggerChange(/^'.preg_quote($a, '/')."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>Event<td>',html_select('Event', $Hg['Event'], $L['Event'], "this.form['Timing'].onchange();"),(in_array('UPDATE OF', $Hg['Event']) ? " <input name='Of' value='".h($L['Of'])."' class='hidden'>" : ''),'<tr><th>Type<td>',html_select('Type', $Hg['Type'], $L['Type']),'</table>
<p>Name: <input name="Trigger" value="',h($L['Trigger']),'" maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';
    textarea('Statement', $L['Statement']);
    echo'<p>
<input type="submit" value="Save">
';
    if ($E != '') {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', $E));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['user'])) {
    $fa = $_GET['user'];
    $Xe = ['' => ['All privileges' => '']];
    foreach (get_rows('SHOW PRIVILEGES')as$L) {
        foreach (explode(',', ($L['Privilege'] == 'Grant option' ? '' : $L['Context']))as$hb) {
            $Xe[$hb][$L['Privilege']] = $L['Comment'];
        }
    }
    $Xe['Server Admin'] += $Xe['File access on server'];
    $Xe['Databases']['Create routine'] = $Xe['Procedures']['Create routine'];
    unset($Xe['Procedures']['Create routine']);
    $Xe['Columns'] = [];
    foreach (['Select', 'Insert', 'Update', 'References']as$X) {
        $Xe['Columns'][$X] = $Xe['Tables'][$X];
    }
    unset($Xe['Server Admin']['Usage']);
    foreach ($Xe['Tables']as$z => $X) {
        unset($Xe['Databases'][$z]);
    }
    $Rd = [];
    if ($_POST) {
        foreach ($_POST['objects']as$z => $X) {
            $Rd[$X] = (array) $Rd[$X] + (array) $_POST['grants'][$z];
        }
    }
    $Cc = [];
    $de = '';
    if (isset($_GET['host']) && ($J = $e->query('SHOW GRANTS FOR '.q($fa).'@'.q($_GET['host'])))) {
        while ($L = $J->fetch_row()) {
            if (preg_match('~GRANT (.*) ON (.*) TO ~', $L[0], $C) && preg_match_all('~ *([^(,]*[^ ,(])( *\\([^)]+\\))?~', $C[1], $_d, PREG_SET_ORDER)) {
                foreach ($_d
                as$X) {
                    if ($X[1] != 'USAGE') {
                        $Cc["$C[2]$X[2]"][$X[1]] = true;
                    }
                    if (preg_match('~ WITH GRANT OPTION~', $L[0])) {
                        $Cc["$C[2]$X[2]"]['GRANT OPTION'] = true;
                    }
                }
            }
            if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $L[0], $C)) {
                $de = $C[1];
            }
        }
    }
    if ($_POST && ! $k) {
        $ee = (isset($_GET['host']) ? q($fa).'@'.q($_GET['host']) : "''");
        if ($_POST['drop']) {
            query_redirect("DROP USER $ee", ME.'privileges=', 'User has been dropped.');
        } else {
            $Td = q($_POST['user']).'@'.q($_POST['host']);
            $He = $_POST['pass'];
            if ($He != '' && ! $_POST['hashed']) {
                $He = $e->result('SELECT PASSWORD('.q($He).')');
                $k = ! $He;
            }
            $lb = false;
            if (! $k) {
                if ($ee != $Td) {
                    $lb = queries((min_version(5) ? 'CREATE USER' : 'GRANT USAGE ON *.* TO')." $Td IDENTIFIED BY PASSWORD ".q($He));
                    $k = ! $lb;
                } elseif ($He != $de) {
                    queries("SET PASSWORD FOR $Td = ".q($He));
                }
            }
            if (! $k) {
                $rf = [];
                foreach ($Rd
                as$Yd => $r) {
                    if (isset($_GET['grant'])) {
                        $r = array_filter($r);
                    }
                    $r = array_keys($r);
                    if (isset($_GET['grant'])) {
                        $rf = array_diff(array_keys(array_filter($Rd[$Yd], 'strlen')), $r);
                    } elseif ($ee == $Td) {
                        $be = array_keys((array) $Cc[$Yd]);
                        $rf = array_diff($be, $r);
                        $r = array_diff($r, $be);
                        unset($Cc[$Yd]);
                    }
                    if (preg_match('~^(.+)\\s*(\\(.*\\))?$~U', $Yd, $C) && (! grant('REVOKE', $rf, $C[2], " ON $C[1] FROM $Td") || ! grant('GRANT', $r, $C[2], " ON $C[1] TO $Td"))) {
                        $k = true;
                        break;
                    }
                }
            }
            if (! $k && isset($_GET['host'])) {
                if ($ee != $Td) {
                    queries("DROP USER $ee");
                } elseif (! isset($_GET['grant'])) {
                    foreach ($Cc
                    as$Yd => $rf) {
                        if (preg_match('~^(.+)(\\(.*\\))?$~U', $Yd, $C)) {
                            grant('REVOKE', array_keys($rf), $C[2], " ON $C[1] FROM $Td");
                        }
                    }
                }
            }
            queries_redirect(ME.'privileges=', (isset($_GET['host']) ? 'User has been altered.' : 'User has been created.'), ! $k);
            if ($lb) {
                $e->query("DROP USER $Td");
            }
        }
    }
    page_header((isset($_GET['host']) ? 'Username'.': '.h("$fa@$_GET[host]") : 'Create user'), $k, ['privileges' => ['', 'Privileges']]);
    if ($_POST) {
        $L = $_POST;
        $Cc = $Rd;
    } else {
        $L = $_GET + ['host' => $e->result("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];
        $L['pass'] = $de;
        if ($de != '') {
            $L['hashed'] = true;
        }
        $Cc[(DB == '' || $Cc ? '' : idf_escape(addcslashes(DB, '%_\\'))).'.*'] = [];
    }
    echo'<form action="" method="post">
<table cellspacing="0">
<tr><th>Server<td><input name="host" maxlength="60" value="',h($L['host']),'" autocapitalize="off">
<tr><th>Username<td><input name="user" maxlength="16" value="',h($L['user']),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($L['pass']),'" autocomplete="new-password">
';
    if (! $L['hashed']) {
        echo
        script("typePassword(qs('#pass'));");
    }
    echo
    checkbox('hashed', 1, $L['hashed'], 'Hashed', "typePassword(this.form['pass'], this.checked);"),'</table>

';
    echo"<table cellspacing='0'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(['sql' => 'grant.html#priv_level']);
    $t = 0;
    foreach ($Cc
    as$Yd => $r) {
        echo'<th>'.($Yd != '*.*' ? "<input name='objects[$t]' value='".h($Yd)."' size='10' autocapitalize='off'>" : "<input type='hidden' name='objects[$t]' value='*.*' size='10'>*.*");
        $t++;
    }
    echo"</thead>\n";
    foreach (['' => '', 'Server Admin' => 'Server', 'Databases' => 'Database', 'Tables' => 'Table', 'Columns' => 'Column', 'Procedures' => 'Routine']as$hb => $Ab) {
        foreach ((array) $Xe[$hb]as$We => $bb) {
            echo'<tr'.odd().'><td'.($Ab ? ">$Ab<td" : " colspan='2'").' lang="en" title="'.h($bb).'">'.h($We);
            $t = 0;
            foreach ($Cc
            as$Yd => $r) {
                $E = "'grants[$t][".h(strtoupper($We))."]'";
                $Y = $r[strtoupper($We)];
                if ($hb == 'Server Admin' && $Yd != (isset($Cc['*.*']) ? '*.*' : '.*')) {
                    echo'<td>&nbsp;';
                } elseif (isset($_GET['grant'])) {
                    echo"<td><select name=$E><option><option value='1'".($Y ? ' selected' : '').'>'.'Grant'."<option value='0'".($Y == '0' ? ' selected' : '').'>'.'Revoke'.'</select>';
                } else {
                    echo"<td align='center'><label class='block'>","<input type='checkbox' name=$E value='1'".($Y ? ' checked' : '').($We == 'All privileges' ? " id='grants-$t-all'>" : '>'.($We == 'Grant option' ? '' : script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$t-all'); };"))),'</label>';
                }
                $t++;
            }
        }
    }
    echo"</table>\n",'<p>
<input type="submit" value="Save">
';
    if (isset($_GET['host'])) {
        echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?', "$fa@$_GET[host]"));
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
';
} elseif (isset($_GET['processlist'])) {
    if (support('kill') && $_POST && ! $k) {
        $id = 0;
        foreach ((array) $_POST['kill']as$X) {
            if (kill_process($X)) {
                $id++;
            }
        }
        queries_redirect(ME.'processlist=', lang(['%d process has been killed.', '%d processes have been killed.'], $id), $id || ! $_POST['kill']);
    }
    page_header('Process list', $k);
    echo'
<form action="" method="post">
<table cellspacing="0" class="nowrap checkable">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");
    $t = -1;
    foreach (process_list()as$t => $L) {
        if (! $t) {
            echo"<thead><tr lang='en'>".(support('kill') ? '<th>&nbsp;' : '');
            foreach ($L
            as$z => $X) {
                echo"<th>$z".doc_link(['sql' => 'show-processlist.html#processlist_'.strtolower($z), 'pgsql' => 'monitoring-stats.html#PG-STAT-ACTIVITY-VIEW', 'oracle' => '../b14237/dynviews_2088.htm']);
            }
            echo"</thead>\n";
        }
        echo'<tr'.odd().'>'.(support('kill') ? '<td>'.checkbox('kill[]', $L[$y == 'sql' ? 'Id' : 'pid'], 0) : '');
        foreach ($L
        as$z => $X) {
            echo'<td>'.(($y == 'sql' && $z == 'Info' && preg_match('~Query|Killed~', $L['Command']) && $X != '') || ($y == 'pgsql' && $z == 'current_query' && $X != '<IDLE>') || ($y == 'oracle' && $z == 'sql_text' && $X != '') ? "<code class='jush-$y'>".shorten_utf8($X, 100, '</code>').' <a href="'.h(ME.($L['db'] != '' ? 'db='.urlencode($L['db']).'&' : '').'sql='.urlencode($X)).'">'.'Clone'.'</a>' : nbsp($X));
        }
        echo"\n";
    }
    echo'</table>
<p>
';
    if (support('kill')) {
        echo($t + 1).'/'.sprintf('%d in total', max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";
    }
    echo'<input type="hidden" name="token" value="',$T,'">
</form>
',script('tableCheck();');
} elseif (isset($_GET['select'])) {
    $a = $_GET['select'];
    $R = table_status1($a);
    $w = indexes($a);
    $m = fields($a);
    $o = column_foreign_keys($a);
    $ae = $R['Oid'];
    parse_str($_COOKIE['adminer_import'], $ma);
    $sf = [];
    $d = [];
    $sg = null;
    foreach ($m
    as$z => $l) {
        $E = $b->fieldName($l);
        if (isset($l['privileges']['select']) && $E != '') {
            $d[$z] = html_entity_decode(strip_tags($E), ENT_QUOTES);
            if (is_shortable($l)) {
                $sg = $b->selectLengthProcess();
            }
        }
        $sf += $l['privileges'];
    }
    [$N, $s] = $b->selectColumnsProcess($d, $w);
    $cd = count($s) < count($N);
    $Z = $b->selectSearchProcess($m, $w);
    $oe = $b->selectOrderProcess($m, $w);
    $_ = $b->selectLimitProcess();
    if ($_GET['val'] && is_ajax()) {
        header('Content-Type: text/plain; charset=utf-8');
        foreach ($_GET['val']as$Pg => $L) {
            $ua = convert_field($m[key($L)]);
            $N = [$ua ? $ua : idf_escape(key($L))];
            $Z[] = where_check($Pg, $m);
            $K = $j->select($a, $N, $Z, $N);
            if ($K) {
                echo
                reset($K->fetch_row());
            }
        }
        exit;
    }
    $Te = $Rg = null;
    foreach ($w
    as$v) {
        if ($v['type'] == 'PRIMARY') {
            $Te = array_flip($v['columns']);
            $Rg = ($N ? $Te : []);
            foreach ($Rg
            as$z => $X) {
                if (in_array(idf_escape($z), $N)) {
                    unset($Rg[$z]);
                }
            }
            break;
        }
    }
    if ($ae && ! $Te) {
        $Te = $Rg = [$ae => 0];
        $w[] = ['type' => 'PRIMARY', 'columns' => [$ae]];
    }
    if ($_POST && ! $k) {
        $mh = $Z;
        if (! $_POST['all'] && is_array($_POST['check'])) {
            $Oa = [];
            foreach ($_POST['check']as$Ma) {
                $Oa[] = where_check($Ma, $m);
            }
            $mh[] = '(('.implode(') OR (', $Oa).'))';
        }
        $mh = ($mh ? "\nWHERE ".implode(' AND ', $mh) : '');
        if ($_POST['export']) {
            cookie('adminer_import', 'output='.urlencode($_POST['output']).'&format='.urlencode($_POST['format']));
            dump_headers($a);
            $b->dumpTable($a, '');
            $Ac = ($N ? implode(', ', $N) : '*').convert_fields($d, $m, $N)."\nFROM ".table($a);
            $Ec = ($s && $cd ? "\nGROUP BY ".implode(', ', $s) : '').($oe ? "\nORDER BY ".implode(', ', $oe) : '');
            if (! is_array($_POST['check']) || $Te) {
                $I = "SELECT $Ac$mh$Ec";
            } else {
                $Ng = [];
                foreach ($_POST['check']as$X) {
                    $Ng[] = '(SELECT'.limit($Ac, "\nWHERE ".($Z ? implode(' AND ', $Z).' AND ' : '').where_check($X, $m).$Ec, 1).')';
                }
                $I = implode(' UNION ALL ', $Ng);
            }
            $b->dumpData($a, 'table', $I);
            exit;
        }
        if (! $b->selectEmailProcess($Z, $o)) {
            if ($_POST['save'] || $_POST['delete']) {
                $J = true;
                $na = 0;
                $P = [];
                if (! $_POST['delete']) {
                    foreach ($d
                    as$E => $X) {
                        $X = process_input($m[$E]);
                        if ($X !== null && ($_POST['clone'] || $X !== false)) {
                            $P[idf_escape($E)] = ($X !== false ? $X : idf_escape($E));
                        }
                    }
                }
                if ($_POST['delete'] || $P) {
                    if ($_POST['clone']) {
                        $I = 'INTO '.table($a).' ('.implode(', ', array_keys($P)).")\nSELECT ".implode(', ', $P)."\nFROM ".table($a);
                    }
                    if ($_POST['all'] || ($Te && is_array($_POST['check'])) || $cd) {
                        $J = ($_POST['delete'] ? $j->delete($a, $mh) : ($_POST['clone'] ? queries("INSERT $I$mh") : $j->update($a, $P, $mh)));
                        $na = $e->affected_rows;
                    } else {
                        foreach ((array) $_POST['check']as$X) {
                            $lh = "\nWHERE ".($Z ? implode(' AND ', $Z).' AND ' : '').where_check($X, $m);
                            $J = ($_POST['delete'] ? $j->delete($a, $lh, 1) : ($_POST['clone'] ? queries('INSERT'.limit1($a, $I, $lh)) : $j->update($a, $P, $lh, 1)));
                            if (! $J) {
                                break;
                            }
                            $na += $e->affected_rows;
                        }
                    }
                }
                $D = lang(['%d item has been affected.', '%d items have been affected.'], $na);
                if ($_POST['clone'] && $J && $na == 1) {
                    $nd = last_id();
                    if ($nd) {
                        $D = sprintf('Item%s has been inserted.', " $nd");
                    }
                }
                queries_redirect(remove_from_uri($_POST['all'] && $_POST['delete'] ? 'page' : ''), $D, $J);
                if (! $_POST['delete']) {
                    edit_form($a, $m, (array) $_POST['fields'], ! $_POST['clone']);
                    page_footer();
                    exit;
                }
            } elseif (! $_POST['import']) {
                if (! $_POST['val']) {
                    $k = 'Ctrl+click on a value to modify it.';
                } else {
                    $J = true;
                    $na = 0;
                    foreach ($_POST['val']as$Pg => $L) {
                        $P = [];
                        foreach ($L
                        as$z => $X) {
                            $z = bracket_escape($z, 1);
                            $P[idf_escape($z)] = (preg_match('~char|text~', $m[$z]['type']) || $X != '' ? $b->processInput($m[$z], $X) : 'NULL');
                        }
                        $J = $j->update($a, $P, ' WHERE '.($Z ? implode(' AND ', $Z).' AND ' : '').where_check($Pg, $m), ! $cd && ! $Te, ' ');
                        if (! $J) {
                            break;
                        }
                        $na += $e->affected_rows;
                    }
                    queries_redirect(remove_from_uri(), lang(['%d item has been affected.', '%d items have been affected.'], $na), $J);
                }
            } elseif (! is_string($rc = get_file('csv_file', true))) {
                $k = upload_error($rc);
            } elseif (! preg_match('~~u', $rc)) {
                $k = 'File must be in UTF-8 encoding.';
            } else {
                cookie('adminer_import', 'output='.urlencode($ma['output']).'&format='.urlencode($_POST['separator']));
                $J = true;
                $Ya = array_keys($m);
                preg_match_all('~(?>"[^"]*"|[^"\\r\\n]+)+~', $rc, $_d);
                $na = count($_d[0]);
                $j->begin();
                $Ff = ($_POST['separator'] == 'csv' ? ',' : ($_POST['separator'] == 'tsv' ? "\t" : ';'));
                $M = [];
                foreach ($_d[0]as$z => $X) {
                    preg_match_all("~((?>\"[^\"]*\")+|[^$Ff]*)$Ff~", $X.$Ff, $Ad);
                    if (! $z && ! array_diff($Ad[1], $Ya)) {
                        $Ya = $Ad[1];
                        $na--;
                    } else {
                        $P = [];
                        foreach ($Ad[1]as$t => $Ua) {
                            $P[idf_escape($Ya[$t])] = ($Ua == '' && $m[$Ya[$t]]['null'] ? 'NULL' : q(str_replace('""', '"', preg_replace('~^"|"$~', '', $Ua))));
                        }
                        $M[] = $P;
                    }
                }
                $J = (! $M || $j->insertUpdate($a, $M, $Te));
                if ($J) {
                    $J = $j->commit();
                }
                queries_redirect(remove_from_uri('page'), lang(['%d row has been imported.', '%d rows have been imported.'], $na), $J);
                $j->rollback();
            }
        }
    }
    $gg = $b->tableName($R);
    if (is_ajax()) {
        page_headers();
        ob_start();
    } else {
        page_header('Select'.": $gg", $k);
    }
    $P = null;
    if (isset($sf['insert']) || ! support('table')) {
        $P = '';
        foreach ((array) $_GET['where']as$X) {
            if ($o[$X['col']] && count($o[$X['col']]) == 1 && ($X['op'] == '=' || (! $X['op'] && ! preg_match('~[_%]~', $X['val'])))) {
                $P .= '&set'.urlencode('['.bracket_escape($X['col']).']').'='.urlencode($X['val']);
            }
        }
    }
    $b->selectLinks($R, $P);
    if (! $d && support('table')) {
        echo"<p class='error'>".'Unable to select the table'.($m ? '.' : ': '.error())."\n";
    } else {
        echo"<form action='' id='form'>\n","<div style='display: none;'>";
        hidden_fields_get();
        echo DB != '' ? '<input type="hidden" name="db" value="'.h(DB).'">'.(isset($_GET['ns']) ? '<input type="hidden" name="ns" value="'.h($_GET['ns']).'">' : '') : '';
        echo'<input type="hidden" name="select" value="'.h($a).'">',"</div>\n";
        $b->selectColumnsPrint($N, $d);
        $b->selectSearchPrint($Z, $d, $w);
        $b->selectOrderPrint($oe, $d, $w);
        $b->selectLimitPrint($_);
        $b->selectLengthPrint($sg);
        $b->selectActionPrint($w);
        echo"</form>\n";
        $F = $_GET['page'];
        if ($F == 'last') {
            $_c = $e->result(count_rows($a, $Z, $cd, $s));
            $F = floor(max(0, $_c - 1) / $_);
        }
        $Af = $N;
        $Dc = $s;
        if (! $Af) {
            $Af[] = '*';
            $ib = convert_fields($d, $m, $N);
            if ($ib) {
                $Af[] = substr($ib, 2);
            }
        }
        foreach ($N
        as$z => $X) {
            $l = $m[idf_unescape($X)];
            if ($l && ($ua = convert_field($l))) {
                $Af[$z] = "$ua AS $X";
            }
        }
        if (! $cd && $Rg) {
            foreach ($Rg
            as$z => $X) {
                $Af[] = idf_escape($z);
                if ($Dc) {
                    $Dc[] = idf_escape($z);
                }
            }
        }
        $J = $j->select($a, $Af, $Z, $Dc, $oe, $_, $F, true);
        if (! $J) {
            echo"<p class='error'>".error()."\n";
        } else {
            if ($y == 'mssql' && $F) {
                $J->seek($_ * $F);
            }
            $Vb = [];
            echo"<form action='' method='post' enctype='multipart/form-data'>\n";
            $M = [];
            while ($L = $J->fetch_assoc()) {
                if ($F && $y == 'oracle') {
                    unset($L['RNUM']);
                }
                $M[] = $L;
            }
            if ($_GET['page'] != 'last' && $_ != '' && $s && $cd && $y == 'sql') {
                $_c = $e->result(' SELECT FOUND_ROWS()');
            }
            if (! $M) {
                echo"<p class='message'>".'No rows.'."\n";
            } else {
                $Ba = $b->backwardKeys($a, $gg);
                echo"<table id='table' cellspacing='0' class='nowrap checkable'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),'<thead><tr>'.(! $s && $N ? '' : "<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);", '')." <a href='".h($_GET['modify'] ? remove_from_uri('modify') : $_SERVER['REQUEST_URI'].'&modify=1')."'>".'Modify'.'</a>');
                $Qd = [];
                $Bc = [];
                reset($N);
                $ff = 1;
                foreach ($M[0]as$z => $X) {
                    if (! isset($Rg[$z])) {
                        $X = $_GET['columns'][key($N)];
                        $l = $m[$N ? ($X ? $X['col'] : current($N)) : $z];
                        $E = ($l ? $b->fieldName($l, $ff) : ($X['fun'] ? '*' : $z));
                        if ($E != '') {
                            $ff++;
                            $Qd[$z] = $E;
                            $c = idf_escape($z);
                            $Oc = remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($z);
                            $Ab = '&desc%5B0%5D=1';
                            echo'<th>'.script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});", ''),'<a href="'.h($Oc.($oe[0] == $c || $oe[0] == $z || (! $oe && $cd && $s[0] == $c) ? $Ab : '')).'">';
                            echo
                            apply_sql_function($X['fun'], $E).'</a>';
                            echo"<span class='column hidden'>","<a href='".h($Oc.$Ab)."' title='".'descending'."' class='text'> ↓</a>";
                            if (! $X['fun']) {
                                echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($z)."');");
                            }
                            echo'</span>';
                        }
                        $Bc[$z] = $X['fun'];
                        next($N);
                    }
                }
                $td = [];
                if ($_GET['modify']) {
                    foreach ($M
                    as$L) {
                        foreach ($L
                        as$z => $X) {
                            $td[$z] = max($td[$z], min(40, strlen(utf8_decode($X))));
                        }
                    }
                }
                echo($Ba ? '<th>'.'Relations' : '')."</thead>\n";
                if (is_ajax()) {
                    if ($_ % 2 == 1 && $F % 2 == 1) {
                        odd();
                    }
                    ob_end_clean();
                }
                foreach ($b->rowDescriptions($M, $o)as$Pd => $L) {
                    $Og = unique_array($M[$Pd], $w);
                    if (! $Og) {
                        $Og = [];
                        foreach ($M[$Pd]as$z => $X) {
                            if (! preg_match('~^(COUNT\\((\\*|(DISTINCT )?`(?:[^`]|``)+`)\\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\\(`(?:[^`]|``)+`\\))$~', $z)) {
                                $Og[$z] = $X;
                            }
                        }
                    }
                    $Pg = '';
                    foreach ($Og
                    as$z => $X) {
                        if (($y == 'sql' || $y == 'pgsql') && preg_match('~char|text|enum|set~', $m[$z]['type']) && strlen($X) > 64) {
                            $z = (strpos($z, '(') ? $z : idf_escape($z));
                            $z = 'MD5('.($y != 'sql' || preg_match('~^utf8~', $m[$z]['collation']) ? $z : "CONVERT($z USING ".charset($e).')').')';
                            $X = md5($X);
                        }
                        $Pg .= '&'.($X !== null ? urlencode('where['.bracket_escape($z).']').'='.urlencode($X) : 'null%5B%5D='.urlencode($z));
                    }
                    echo'<tr'.odd().'>'.(! $s && $N ? '' : '<td>'.checkbox('check[]', substr($Pg, 1), in_array(substr($Pg, 1), (array) $_POST['check'])).($cd || information_schema(DB) ? '' : " <a href='".h(ME.'edit='.urlencode($a).$Pg)."' class='edit'>".'edit'.'</a>'));
                    foreach ($L
                    as$z => $X) {
                        if (isset($Qd[$z])) {
                            $l = $m[$z];
                            $X = $j->value($X, $l);
                            if ($X != '' && (! isset($Vb[$z]) || $Vb[$z] != '')) {
                                $Vb[$z] = (is_mail($X) ? $Qd[$z] : '');
                            }
                            $A = '';
                            if (preg_match('~blob|bytea|raw|file~', $l['type']) && $X != '') {
                                $A = ME.'download='.urlencode($a).'&field='.urlencode($z).$Pg;
                            }
                            if (! $A && $X !== null) {
                                foreach ((array) $o[$z]as$n) {
                                    if (count($o[$z]) == 1 || end($n['source']) == $z) {
                                        $A = '';
                                        foreach ($n['source']as$t => $Nf) {
                                            $A .= where_link($t, $n['target'][$t], $M[$Pd][$Nf]);
                                        }
                                        $A = ($n['db'] != '' ? preg_replace('~([?&]db=)[^&]+~', '\\1'.urlencode($n['db']), ME) : ME).'select='.urlencode($n['table']).$A;
                                        if ($n['ns']) {
                                            $A = preg_replace('~([?&]ns=)[^&]+~', '\\1'.urlencode($n['ns']), $A);
                                        }
                                        if (count($n['source']) == 1) {
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($z == 'COUNT(*)') {
                                $A = ME.'select='.urlencode($a);
                                $t = 0;
                                foreach ((array) $_GET['where']as$W) {
                                    if (! array_key_exists($W['col'], $Og)) {
                                        $A .= where_link($t++, $W['col'], $W['val'], $W['op']);
                                    }
                                }
                                foreach ($Og
                                as$fd => $W) {
                                    $A .= where_link($t++, $fd, $W);
                                }
                            }
                            $X = select_value($X, $A, $l, $sg);
                            $u = h("val[$Pg][".bracket_escape($z).']');
                            $Y = $_POST['val'][$Pg][bracket_escape($z)];
                            $Qb = ! is_array($L[$z]) && is_utf8($X) && $M[$Pd][$z] == $L[$z] && ! $Bc[$z];
                            $rg = preg_match('~text|lob~', $l['type']);
                            if (($_GET['modify'] && $Qb) || $Y !== null) {
                                $Gc = h($Y !== null ? $Y : $L[$z]);
                                echo'<td>'.($rg ? "<textarea name='$u' cols='30' rows='".(substr_count($L[$z], "\n") + 1)."'>$Gc</textarea>" : "<input name='$u' value='$Gc' size='$td[$z]'>");
                            } else {
                                $xd = strpos($X, '<i>...</i>');
                                echo"<td id='$u' data-text='".($xd ? 2 : ($rg ? 1 : 0))."'".($Qb ? '' : " data-warning='".h('Use edit link to modify this value.')."'").">$X</td>";
                            }
                        }
                    }
                    if ($Ba) {
                        echo'<td>';
                    }
                    $b->backwardKeysPrint($Ba, $M[$Pd]);
                    echo"</tr>\n";
                }
                if (is_ajax()) {
                    exit;
                }
                echo"</table>\n";
            }
            if (! is_ajax()) {
                if ($M || $F) {
                    $fc = true;
                    if ($_GET['page'] != 'last') {
                        if ($_ == '' || (count($M) < $_ && ($M || ! $F))) {
                            $_c = ($F ? $F * $_ : 0) + count($M);
                        } elseif ($y != 'sql' || ! $cd) {
                            $_c = ($cd ? false : found_rows($R, $Z));
                            if ($_c < max(1e4, 2 * ($F + 1) * $_)) {
                                $_c = reset(slow_query(count_rows($a, $Z, $cd, $s)));
                            } else {
                                $fc = false;
                            }
                        }
                    }
                    $_e = ($_ != '' && ($_c === false || $_c > $_ || $F));
                    if ($_e) {
                        echo(($_c === false ? count($M) + 1 : $_c - $F * $_) > $_ ? '<p><a href="'.h(remove_from_uri('page').'&page='.($F + 1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, ".(+$_).", '".'Loading'."...');", '') : ''),"\n";
                    }
                }
                echo"<div class='footer'><div>\n";
                if ($M || $F) {
                    if ($_e) {
                        $Cd = ($_c === false ? $F + (count($M) >= $_ ? 2 : 1) : floor(($_c - 1) / $_));
                        echo'<fieldset>';
                        if ($y != 'simpledb') {
                            echo"<legend><a href='".h(remove_from_uri('page'))."'>".'Page'.'</a></legend>',script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($F + 1)."')); return false; };"),pagination(0, $F).($F > 5 ? ' ...' : '');
                            for ($t = max(1, $F - 4); $t < min($Cd, $F + 5); $t++) {
                                echo
                                pagination($t, $F);
                            }
                            if ($Cd > 0) {
                                echo($F + 5 < $Cd ? ' ...' : ''),($fc && $_c !== false ? pagination($Cd, $F) : " <a href='".h(remove_from_uri('page').'&page=last')."' title='~$Cd'>".'last'.'</a>');
                            }
                        } else {
                            echo'<legend>'.'Page'.'</legend>',pagination(0, $F).($F > 1 ? ' ...' : ''),($F ? pagination($F, $F) : ''),($Cd > $F ? pagination($F + 1, $F).($Cd > $F + 1 ? ' ...' : '') : '');
                        }
                        echo"</fieldset>\n";
                    }
                    echo'<fieldset>','<legend>'.'Whole result'.'</legend>';
                    $Fb = ($fc ? '' : '~ ').$_c;
                    echo
                    checkbox('all', 1, 0, ($_c !== false ? ($fc ? '' : '~ ').lang(['%d row', '%d rows'], $_c) : ''), "var checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Fb' : checked); selectCount('selected2', this.checked || !checked ? '$Fb' : checked);")."\n","</fieldset>\n";
                    if ($b->selectCommandPrint()) {
                        echo'<fieldset',($_GET['modify'] ? '' : ' class="jsonly"'),'><legend>Modify</legend><div>
<input type="submit" value="Save"',($_GET['modify'] ? '' : ' title="'.'Ctrl+click on a value to modify it.'.'"'),'>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">',confirm(),'</div></fieldset>
';
                    }
                    $yc = $b->dumpFormat();
                    foreach ((array) $_GET['columns']as$c) {
                        if ($c['fun']) {
                            unset($yc['sql']);
                            break;
                        }
                    }
                    if ($yc) {
                        print_fieldset('export', 'Export'." <span id='selected2'></span>");
                        $ye = $b->dumpOutput();
                        echo($ye ? html_select('output', $ye, $ma['output']).' ' : ''),html_select('format', $yc, $ma['format'])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";
                    }
                    $b->selectEmailPrint(array_filter($Vb, 'strlen'), $d);
                }
                echo"</div></div>\n";
                if ($b->selectImportPrint()) {
                    echo'<div>',"<a href='#import'>".'Import'.'</a>',script("qsl('a').onclick = partial(toggle, 'import');", ''),"<span id='import' class='hidden'>: ","<input type='file' name='csv_file'> ",html_select('separator', ['csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV'], $ma['format'], 1);
                    echo" <input type='submit' name='import' value='".'Import'."'>",'</span>','</div>';
                }
                echo"<input type='hidden' name='token' value='$T'>\n","</form>\n",(! $s && $N ? '' : script('tableCheck();'));
            }
        }
    }
    if (is_ajax()) {
        ob_end_clean();
        exit;
    }
} elseif (isset($_GET['variables'])) {
    $Uf = isset($_GET['status']);
    page_header($Uf ? 'Status' : 'Variables');
    $ch = ($Uf ? show_status() : show_variables());
    if (! $ch) {
        echo"<p class='message'>".'No rows.'."\n";
    } else {
        echo"<table cellspacing='0'>\n";
        foreach ($ch
        as$z => $X) {
            echo'<tr>',"<th><code class='jush-".$y.($Uf ? 'status' : 'set')."'>".h($z).'</code>','<td>'.nbsp($X);
        }
        echo"</table>\n";
    }
} elseif (isset($_GET['script'])) {
    header('Content-Type: text/javascript; charset=utf-8');
    if ($_GET['script'] == 'db') {
        $dg = ['Data_length' => 0, 'Index_length' => 0, 'Data_free' => 0];
        foreach (table_status()as$E => $R) {
            json_row("Comment-$E", nbsp($R['Comment']));
            if (! is_view($R)) {
                foreach (['Engine', 'Collation']as$z) {
                    json_row("$z-$E", nbsp($R[$z]));
                }
                foreach ($dg + ['Auto_increment' => 0, 'Rows' => 0]as$z => $X) {
                    if ($R[$z] != '') {
                        $X = format_number($R[$z]);
                        json_row("$z-$E", ($z == 'Rows' && $X && $R['Engine'] == ($Pf == 'pgsql' ? 'table' : 'InnoDB') ? "~ $X" : $X));
                        if (isset($dg[$z])) {
                            $dg[$z] += ($R['Engine'] != 'InnoDB' || $z != 'Data_free' ? $R[$z] : 0);
                        }
                    } elseif (array_key_exists($z, $R)) {
                        json_row("$z-$E");
                    }
                }
            }
        }
        foreach ($dg
        as$z => $X) {
            json_row("sum-$z", format_number($X));
        }
        json_row('');
    } elseif ($_GET['script'] == 'kill') {
        $e->query('KILL '.number($_POST['kill']));
    } else {
        foreach (count_tables($b->databases())as$i => $X) {
            json_row("tables-$i", $X);
            json_row("size-$i", db_size($i));
        }
        json_row('');
    }
    exit;
} else {
    $lg = array_merge((array) $_POST['tables'], (array) $_POST['views']);
    if ($lg && ! $k && ! $_POST['search']) {
        $J = true;
        $D = '';
        if ($y == 'sql' && $_POST['tables'] && count($_POST['tables']) > 1 && ($_POST['drop'] || $_POST['truncate'] || $_POST['copy'])) {
            queries('SET foreign_key_checks = 0');
        }
        if ($_POST['truncate']) {
            if ($_POST['tables']) {
                $J = truncate_tables($_POST['tables']);
            }
            $D = 'Tables have been truncated.';
        } elseif ($_POST['move']) {
            $J = move_tables((array) $_POST['tables'], (array) $_POST['views'], $_POST['target']);
            $D = 'Tables have been moved.';
        } elseif ($_POST['copy']) {
            $J = copy_tables((array) $_POST['tables'], (array) $_POST['views'], $_POST['target']);
            $D = 'Tables have been copied.';
        } elseif ($_POST['drop']) {
            if ($_POST['views']) {
                $J = drop_views($_POST['views']);
            }
            if ($J && $_POST['tables']) {
                $J = drop_tables($_POST['tables']);
            }
            $D = 'Tables have been dropped.';
        } elseif ($y != 'sql') {
            $J = ($y == 'sqlite' ? queries('VACUUM') : apply_queries('VACUUM'.($_POST['optimize'] ? '' : ' ANALYZE'), $_POST['tables']));
            $D = 'Tables have been optimized.';
        } elseif (! $_POST['tables']) {
            $D = 'No tables.';
        } elseif ($J = queries(($_POST['optimize'] ? 'OPTIMIZE' : ($_POST['check'] ? 'CHECK' : ($_POST['repair'] ? 'REPAIR' : 'ANALYZE'))).' TABLE '.implode(', ', array_map('idf_escape', $_POST['tables'])))) {
            while ($L = $J->fetch_assoc()) {
                $D .= '<b>'.h($L['Table']).'</b>: '.h($L['Msg_text']).'<br>';
            }
        }
        queries_redirect(substr(ME, 0, -1), $D, $J);
    }
    page_header(($_GET['ns'] == '' ? 'Database'.': '.h(DB) : 'Schema'.': '.h($_GET['ns'])), $k, true);
    if ($b->homepage()) {
        if ($_GET['ns'] !== '') {
            echo"<h3 id='tables-views'>".'Tables and views'."</h3>\n";
            $kg = tables_list();
            if (! $kg) {
                echo"<p class='message'>".'No tables.'."\n";
            } else {
                echo"<form action='' method='post'>\n";
                if (support('table')) {
                    echo'<fieldset><legend>'.'Search data in tables'." <span id='selected2'></span></legend><div>","<input type='search' name='query' value='".h($_POST['query'])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');", '')," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";
                    if ($_POST['search'] && $_POST['query'] != '') {
                        $_GET['where'][0]['op'] = 'LIKE %%';
                        search_tables();
                    }
                }
                $Gb = doc_link(['sql' => 'show-table-status.html']);
                echo"<table cellspacing='0' class='nowrap checkable'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);", ''),'<th>'.'Table','<td>'.'Engine'.doc_link(['sql' => 'storage-engines.html']),'<td>'.'Collation'.doc_link(['sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/']),'<td>'.'Data Length'.$Gb,'<td>'.'Index Length'.$Gb,'<td>'.'Data Free'.$Gb,'<td>'.'Auto Increment'.doc_link(['sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/']),'<td>'.'Rows'.$Gb,(support('comment') ? '<td>'.'Comment'.$Gb : ''),"</thead>\n";
                $S = 0;
                foreach ($kg
                as$E => $U) {
                    $fh = ($U !== null && ! preg_match('~table~i', $U));
                    $u = h('Table-'.$E);
                    echo'<tr'.odd().'><td>'.checkbox(($fh ? 'views[]' : 'tables[]'), $E, in_array($E, $lg, true), '', '', '', $u),'<th>'.(support('table') || support('indexes') ? "<a href='".h(ME).'table='.urlencode($E)."' title='".'Show structure'."' id='$u'>".h($E).'</a>' : h($E));
                    if ($fh) {
                        echo'<td colspan="6"><a href="'.h(ME).'view='.urlencode($E).'" title="'.'Alter view'.'">'.(preg_match('~materialized~i', $U) ? 'Materialized view' : 'View').'</a>','<td align="right"><a href="'.h(ME).'select='.urlencode($E).'" title="'.'Select data'.'">?</a>';
                    } else {
                        foreach (['Engine' => [], 'Collation' => [], 'Data_length' => ['create', 'Alter table'], 'Index_length' => ['indexes', 'Alter indexes'], 'Data_free' => ['edit', 'New item'], 'Auto_increment' => ['auto_increment=1&create', 'Alter table'], 'Rows' => ['select', 'Select data']]as$z => $A) {
                            $u = " id='$z-".h($E)."'";
                            echo $A ? "<td align='right'>".(support('table') || $z == 'Rows' || (support('indexes') && $z != 'Data_length') ? "<a href='".h(ME."$A[0]=").urlencode($E)."'$u title='$A[1]'>?</a>" : "<span$u>?</span>") : "<td id='$z-".h($E)."'>&nbsp;";
                        }
                        $S++;
                    }
                    echo support('comment') ? "<td id='Comment-".h($E)."'>&nbsp;" : '';
                }
                echo'<tr><td>&nbsp;<th>'.sprintf('%d in total', count($kg)),'<td>'.nbsp($y == 'sql' ? $e->result('SELECT @@storage_engine') : ''),'<td>'.nbsp(db_collation(DB, collations()));
                foreach (['Data_length', 'Index_length', 'Data_free']as$z) {
                    echo"<td align='right' id='sum-$z'>&nbsp;";
                }
                echo"</table>\n";
                if (! information_schema(DB)) {
                    echo"<div class='footer'><div>\n";
                    $ah = "<input type='submit' value='".'Vacuum'."'> ".on_help("'VACUUM'");
                    $le = "<input type='submit' name='optimize' value='".'Optimize'."'> ".on_help($y == 'sql' ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
                    echo'<fieldset><legend>'.'Selected'." <span id='selected'></span></legend><div>".($y == 'sqlite' ? $ah : ($y == 'pgsql' ? $ah.$le : ($y == 'sql' ? "<input type='submit' value='".'Analyze'."'> ".on_help("'ANALYZE TABLE'").$le."<input type='submit' name='check' value='".'Check'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'Repair'."'> ".on_help("'REPAIR TABLE'") : '')))."<input type='submit' name='truncate' value='".'Truncate'."'> ".on_help($y == 'sqlite' ? "'DELETE'" : "'TRUNCATE".($y == 'pgsql' ? "'" : " TABLE'")).confirm()."<input type='submit' name='drop' value='".'Drop'."'>".on_help("'DROP TABLE'").confirm()."\n";
                    $h = (support('scheme') ? $b->schemas() : $b->databases());
                    if (count($h) != 1 && $y != 'sqlite') {
                        $i = (isset($_POST['target']) ? $_POST['target'] : (support('scheme') ? $_GET['ns'] : DB));
                        echo'<p>'.'Move to other database'.': ',($h ? html_select('target', $h, $i) : '<input name="target" value="'.h($i).'" autocapitalize="off">')," <input type='submit' name='move' value='".'Move'."'>",(support('copy') ? " <input type='submit' name='copy' value='".'Copy'."'>" : ''),"\n";
                    }
                    echo"<input type='hidden' name='all' value=''>";
                    echo
                    script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support('table') ? " selectCount('selected2', formChecked(this, /^tables\[/) || $S);" : '').' }'),"<input type='hidden' name='token' value='$T'>\n","</div></fieldset>\n","</div></div>\n";
                }
                echo"</form>\n",script('tableCheck();');
            }
            echo'<p class="links"><a href="'.h(ME).'create=">'.'Create table'."</a>\n",(support('view') ? '<a href="'.h(ME).'view=">'.'Create view'."</a>\n" : '');
            if (support('routine')) {
                echo"<h3 id='routines'>".'Routines'."</h3>\n";
                $wf = routines();
                if ($wf) {
                    echo"<table cellspacing='0'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td>&nbsp;</thead>\n";
                    odd('');
                    foreach ($wf
                    as$L) {
                        $E = ($L['SPECIFIC_NAME'] == $L['ROUTINE_NAME'] ? '' : '&name='.urlencode($L['ROUTINE_NAME']));
                        echo'<tr'.odd().'>','<th><a href="'.h(ME.($L['ROUTINE_TYPE'] != 'PROCEDURE' ? 'callf=' : 'call=').urlencode($L['SPECIFIC_NAME']).$E).'">'.h($L['ROUTINE_NAME']).'</a>','<td>'.h($L['ROUTINE_TYPE']),'<td>'.h($L['DTD_IDENTIFIER']),'<td><a href="'.h(ME.($L['ROUTINE_TYPE'] != 'PROCEDURE' ? 'function=' : 'procedure=').urlencode($L['SPECIFIC_NAME']).$E).'">'.'Alter'.'</a>';
                    }
                    echo"</table>\n";
                }
                echo'<p class="links">'.(support('procedure') ? '<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>' : '').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";
            }
            if (support('event')) {
                echo"<h3 id='events'>".'Events'."</h3>\n";
                $M = get_rows('SHOW EVENTS');
                if ($M) {
                    echo"<table cellspacing='0'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Schedule'.'<td>'.'Start'.'<td>'.'End'."<td></thead>\n";
                    foreach ($M
                    as$L) {
                        echo'<tr>','<th>'.h($L['Name']),'<td>'.($L['Execute at'] ? 'At given time'.'<td>'.$L['Execute at'] : 'Every'.' '.$L['Interval value'].' '.$L['Interval field']."<td>$L[Starts]"),"<td>$L[Ends]",'<td><a href="'.h(ME).'event='.urlencode($L['Name']).'">'.'Alter'.'</a>';
                    }
                    echo"</table>\n";
                    $dc = $e->result('SELECT @@event_scheduler');
                    if ($dc && $dc != 'ON') {
                        echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($dc)."\n";
                    }
                }
                echo'<p class="links"><a href="'.h(ME).'event=">'.'Create event'."</a>\n";
            }
            if ($kg) {
                echo
                script("ajaxSetHtml('".js_escape(ME)."script=db');");
            }
        }
    }
}page_footer();
